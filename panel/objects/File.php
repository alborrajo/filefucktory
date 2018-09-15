<?php
class File{
	public $name;

	public $relPath;
	public $path;

	public $bytes;

	function __construct($workDir,$relPath) {
		$this->name = basename($relPath);

		$this->relPath = $relPath; //path relative to workDir
		$this->path = $workDir."/".$relPath; //absolute path from files/ folder

		$this->bytes = filesize($this->path);
	}

	function getSizeString() {
		if($this->bytes > 1073742000) {
			return round($this->bytes/1073742000,2)." GB"; //Round with 2 decimals
		}
		else if($this->bytes > 1048576) {
			return round($this->bytes/1048576)." MB";
		}
		else if($this->bytes > 1024) {
			return round($this->bytes/1024)." KB";
		}
		else {
			return round($this->bytes)." B";
		}
	}

	function buildHTML() {
		echo $this->name;
	}
}
?>