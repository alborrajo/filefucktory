<?php

include "File.php";

class Dir{
	public $name;

	public $relPath;
	public $path;

	public $bytes;

	public $lsDirs;
	public $lsFiles;


	function __construct($workDir,$relPath) {
		$this->name = basename($relPath);
		
		$this->relPath = $relPath; //path relative to workDir
		$this->path = $workDir."/".$relPath; //absolute path from index.php folder
		
		$this->bytes = Dir::getSize();
		
		$this->lsDirs = array();
		$this->lsFiles = array();
		
		//Get ls array with every file and folder on $relPath (excluding . and ..)
		//and build lsDirs with only the folders
		//and also lsFiles with only the files
		$ls = array_diff( scandir($workDir."/".$relPath), array('..','.')); //Don't add . and .. to the file list
		foreach($ls as $file) {
			if(is_dir($workDir."/".$relPath."/".$file)) {
				array_push($this->lsDirs, new Dir($workDir,$this->relPath."/".$file));
			}
			else {
				array_push($this->lsFiles, new File($workDir,$this->relPath."/".$file));
			}
		}

		/*
		//SPACE ARRAY
		//Load DB
		$db = json_decode(file_get_contents("config/users.json"));

		//Check entries for a match
		foreach($db->users as $user) {
			if($user->email == $email) {
				$usedmb = $this->getSize($this->workDir)/1048576; //Bytes to MB
				$space = array("usedmb"=>$usedmb, "spacemb"=>$user->spacemb);
			}
		}
		//If there are no results
		if(!isset($space)) {
			return "warning";
		}
		*/
	}

	function getSize(){
	    $bytestotal = 0;
	    $path = realpath($this->path);
	    if($path!==false && $path!='' && file_exists($path)){
	        foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS)) as $object){
	            $bytestotal += $object->getSize();
	        }
	    }
	    return $bytestotal;
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
		?>
		<div class="panel panel-default">
			<div class="panel panel-heading">
				<a data-toggle="collapse" href="#collapse<?php echo urlencode($this->path); ?>">
					<span class="fa fa-folder-open"></span>
					<?php echo $this->name; ?>
				</a>
				(<?php echo $this->getSizeString(); ?>)
			</div>
			
			<div id="collapse<?php echo urlencode($this->path); ?>" class="panel-collapse collapse">
				<div class="panel panel-body">
					<ul class="list-group">
						<?php
						foreach($this->lsDirs as $dir) {
							?><li class="list-group-item"><?php
							$dir->buildHTML();	
							?></li><?php
						}

						foreach($this->lsFiles as $file) {
							?><li class="list-group-item"><?php
							$file->buildHTML();
							?></li><?php
						}
						?>
					</ul>
				</div>
			</div>
		</div>
		<?php
	}
}
?>