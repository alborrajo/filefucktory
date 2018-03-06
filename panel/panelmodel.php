<?php
class PanelModel {

	function checkFolder($folder) {
		$ls = array_diff( scandir("files/".$folder), array('..','.'));

		$toReturn = array();
		foreach($ls as $file) {
			array_push($toReturn, array("file"=>$file, "size"=>filesize("files/".$folder."/".$file) ));
		}
		return $toReturn;
	}

	function uploadFile($files) {
		$targetDir = "files/".$_SESSION["userObjectId"]."/";
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
		$targetFile = "files/".$_SESSION["userObjectId"]."/".$relPath;

		if(unlink($targetFile)) {
			return "success";
		}
		else {
			return "warning";
		}
	}
	
}