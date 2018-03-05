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
		$targetDir = "files/".$_SESSION["password"]."/";
		$targetFile = $targetDir.basename($files["fileToUpload"]["name"]);

		if(pathinfo($files["fileToUpload"]["name"], PATHINFO_EXTENSION) == "php") {
			return "info";			
		}
		elseif(file_exists($targetFile)) {
			return "warning";
		}
		else {
			if(move_uploaded_file($files["fileToUpload"]["tmp_name"], $targetFile)) {
				return "success";
			}
			else {
				return "danger";
			}
		}
		
	}	

	function deleteFile($relPath) {
		$targetFile = "files/".$_SESSION["password"]."/".$relPath;

		if(unlink($targetFile)) {
			return "success";
		}
		else {
			return "warning";
		}
	}
}