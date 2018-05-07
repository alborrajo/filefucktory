<?php
class PanelModel {

	function getFolder($email) {
		return md5($email);
	}

	function makeDir($relDir,$dirName) {
		$dirPath = "files/".$_SESSION["userFolder"]."/".$relDir."/".$dirName;
		if(mkdir($dirPath)) {return "success";} else {return "warning";}
	}

	function deleteDir($relDirPath) {
		$src = "files/".$_SESSION["userFolder"]."/".$relDirPath;
		$dir = opendir($src);
		while(false !== ( $file = readdir($dir)) ) {
			if (( $file != '.' ) && ( $file != '..' )) {
				$full = $src . '/' . $file;
				if ( is_dir($full) ) {
					$this->deleteDir($full);
				}
				else {
					unlink($full);
				}
			}
		}
		closedir($dir);
		rmdir($src);
		return "success";
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
	
	function checkFolder($folder,$email) {
	
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

		//Array $space
		//	"usedmb": Folder size in mb
		//	"spacemb": Max space in mb
		$manager = new MongoDB\Driver\Manager("mongodb://localhost:27017");
				
		$query = new MongoDB\Driver\Query(array("email"=>(string)$email));				
		$queryResult = $manager->executeQuery('filefucktory.user', $query);

		$queryArray = $queryResult->toArray();
		
		//If there are no results
		if(empty($queryArray)) {
			return "warning";
		}
		//If there are
		else {				
			$usedmb = $this->GetDirectorySize("files/".$_SESSION["userFolder"])/1048576; //Bytes to MB
			$space = array("usedmb"=>$usedmb, "spacemb"=>$queryArray[0]->spacemb);
		}

		//Array return:
		//	"space": $space
		//	"files": $files
		//	"dirs":  $dirs
		return array("space"=>$space,"files"=>$files,"dirs"=>$dirs);
	}

	function uploadFile($folder,$files,$email) {
		//Check used space
		$usedmb = $this->GetDirectorySize("files/".$_SESSION["userFolder"])/1048576; //Bytes to MB
		
		//Check total space
		$manager = new MongoDB\Driver\Manager("mongodb://localhost:27017");
						
		$query = new MongoDB\Driver\Query(array("email"=>(string)$email));				
		$queryResult = $manager->executeQuery('filefucktory.user', $query);
		
		$queryArray = $queryResult->toArray();
		if(!empty($queryArray)) {
			$spacemb = $queryArray[0]->spacemb;
		}
		else {
			return "danger";
		}

		//Check if used > total
		if($usedmb > $spacemb) {
			return "warningfull";
		}


		$targetDir = "files/".$_SESSION["userFolder"].$folder."/";
		$targetFile = $targetDir.basename($files["fileToUpload"]["name"]);

		if(pathinfo($files["fileToUpload"]["name"], PATHINFO_EXTENSION) == "php") {
			header('Content-Type: application/json');
			echo json_encode(array("status"=>"info"));
			exit;
			return "info";			
		}
						
		if(file_exists($targetFile)) {
			header('Content-Type: application/json');
			echo json_encode(array("status"=>"warning"));
			exit;
			return "warning";
		}

		if(move_uploaded_file($files["fileToUpload"]["tmp_name"], $targetFile)) {
			header('Content-Type: application/json');
			echo json_encode(array("status"=>"success"));
			exit;
			return "success";
		}
		else {
			header('Content-Type: application/json');
			echo json_encode(array("status"=>"danger"));
			exit;
			return "danger";
		}
		
	}	

	function deleteFile($relPath) {
		$targetFile = "files/".$_SESSION["userFolder"].$relPath;
		
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