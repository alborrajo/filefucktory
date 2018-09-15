<?php
class PanelModel {

	function getFolder($email) {
		return md5($email);
	}

	function getSpace($email) {
		//Load DB
		$db = json_decode(file_get_contents("config/users.json"));

		//Check entries for a match
		foreach($db->users as $user) {
			if($user->email == $email) {
				$usedmb = GetDirectorySize("files/".$_SESSION["userFolder"])/1048576; //Bytes to MB
				$space = array("usedmb"=>$usedmb, "spacemb"=>$user->spacemb);
			}
		}
		//If there are no results
		if(!isset($space)) {
			return "warning";
		}

		return $space;
	}

	function makeDir($relDir,$dirName) {
		$dirPath = "files/".$_SESSION["userFolder"]."/".$relDir."/".$dirName;
		$dirPath = preg_replace("/\/\.\.\/|^\.\.\/|\/\.\.$/","/",$dirPath); //Avoid escaping (remove /../, ../, and /..)
		if(mkdir($dirPath)) {return "success";} else {return "warning";}
	}

	function deleteDir($relDirPath) {
		$src = "files/".$_SESSION["userFolder"]."/".$relDirPath;
		$src = preg_replace("/\/\.\.\/|^\.\.\/|\/\.\.$/","/",$src);
		$this->rrmdir($src);
		return "success";
	}

	function rrmdir($src) {
		$dir = opendir($src);
		while(false !== ( $file = readdir($dir)) ) {
			if (( $file != '.' ) && ( $file != '..' )) {
				$full = $src . '/' . $file;
				if ( is_dir($full) ) {
					$this->rrmdir($full);
				}
				else {
					unlink($full);
				}
			}
		}
		closedir($dir);
		rmdir($src);
	}

	function moveFile($relPath,$newRelPath) {
		if(rename($relPath,$newRelPath)) {return "success";} else {return "warning";}
	}
			
	function GetDirectorySize($path){
	    $bytestotal = 0;
	    $path = realpath($path);
	    if($path!==false && $path!='' && file_exists($path)){
	        foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS)) as $object){
	            $bytestotal += $object->getSize();
	        }
	    }
	    return $bytestotal;
	}
	
	function checkFolder(/*$folder,$email*/) {
		return new Dir("files/".$_SESSION["userFolder"],"");
		/*
		//Array $files:
		//	"file": Filename
		//	"size": Filesize
		//Array $dirs:
		//	"dir": Directory
		//	"size": Dir size
		$files = array();
		$dirs = array();
		$ls = array_diff( scandir("files/".$_SESSION["userFolder"]."/".$folder), array('..','.')); 
		foreach($ls as $file) {
			if(is_dir("files/".$_SESSION["userFolder"]."/".$folder."/".$file)) {
				array_push($dirs, array("dir"=>$file, "size"=>$this->GetDirectorySize("files/".$_SESSION["userFolder"]."/".$folder."/".$file) ));
			}
			else {
				array_push($files, array("file"=>$file, "size"=>filesize("files/".$_SESSION["userFolder"]."/".$folder."/".$file) ));
			}
		}
		

		//Array return:
		//	"space": $space
		//	"files": $files
		//	"dirs":  $dirs
		return array("space"=>$space,"files"=>$files,"dirs"=>$dirs);
		*/
	}

	function uploadFile($folder,$files,$email) {
		//Load DB
		$db = json_decode(file_get_contents("config/users.json"));

		//Check used space
		$usedmb = $this->GetDirectorySize("files/".$_SESSION["userFolder"])/1048576; //Bytes to MB
		
		//Check entries for a match (Get user folder space)
		foreach($db->users as $user) {
			if($user->email == $email) {
				$spacemb = $user->spacemb;
			}
		}

		//If spacemb can't be found
		if(!isset($spacemb)) {
			header('Content-Type: application/json');
			echo json_encode(array("status"=>"danger"));
			exit;
		}

		$targetDir = "files/".$_SESSION["userFolder"].$folder."/";
		$targetDir = preg_replace("/\/\.\.\/|^\.\.\/|\/\.\.$/","/",$targetDir);
		$targetFile = $targetDir.basename($files["fileToUpload"]["name"]);

		if(pathinfo($files["fileToUpload"]["name"], PATHINFO_EXTENSION) == "php") {
			header('Content-Type: application/json');
			echo json_encode(array("status"=>"info"));
			exit;		
		}
						
		if(file_exists($targetFile)) {
			header('Content-Type: application/json');
			echo json_encode(array("status"=>"warning"));
			exit;
		}

		if($usedmb > $spacemb) {
			header('Content-Type: application/json');
			echo json_encode(array("status"=>"warningfulll"));
			exit;
		}

		if(move_uploaded_file($files["fileToUpload"]["tmp_name"], $targetFile)) {
			header('Content-Type: application/json');
			echo json_encode(array("status"=>"success"));
			exit;
		}
		else {
			header('Content-Type: application/json');
			echo json_encode(array("status"=>"danger"));
			exit;
		}
		
	}	

	function deleteFile($relPath) {
		$targetFile = "files/".$_SESSION["userFolder"].$relPath;
		$targetFile = preg_replace("/\/\.\.\/|^\.\.\/|\/\.\.$/","/",$targetFile);
		
		//If its a directory
		if(is_dir($targetFile)) {
			if(deleteDir($targetFile)) {
				return "success";
			}
			else {
				return "warning";
			}
		}
		//If its not
		else {
			if(unlink($targetFile)) {
				return "success";
			}
			else {
				return "warning";
			}
		}
	}
	
}