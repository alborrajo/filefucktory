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
				<a data-toggle="collapse" href="#collapse<?php echo md5($this->path); ?>">
					<span class="fa fa-folder-open"></span>
					<?php echo $this->name; ?>
				</a>
				(<?php echo $this->getSizeString(); ?>)

				<div class="btn-group pull-right">
					<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#upload<?php echo md5($this->path); ?>">Subir <span class="fa fa-cloud-upload"></span></button>
					<button type="button" class="btn" data-toggle="modal" data-target="#makedir<?php echo md5($this->path); ?>">Crear carpeta<span class="fa fa-plus-circle"></span><span class="fa fa-folder-open"></span></button>
				</div>

				<div class="modal fade" id="upload<?php echo md5($this->path); ?>" role="dialog">
					<div class="modal-dialog">
					
						<!-- Modal content-->
						<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal">&times;</button>
							<h4 class="modal-title">Subir fichero</h4>
						</div>
						<div class="modal-body">
						
							<form action="" method="post" enctype="multipart/form-data">
								<input type="hidden" name="dir" value="<?php echo $this->relPath?>">	
								<input type="hidden" name="action" value="upload">

								<label class="btn btn-default" for="file<?php echo md5($this->path); ?>">
									Elegir fichero <input type="file" name="fileToUpload" id="file<?php echo md5($this->path); ?>" style="display:none;" onchange="$('#fileInfo').html(this.files[0].name)">
								</label>
								<span class="label label-info" id="fileInfo"></span>

								<div class="progress">
									<div class="progress-bar progress-bar-striped active" style="width:0%" id="progressBar"></div>
								</div>
							
								<input type="button" class="btn btn-primary" value="Subir" id="submit">

								<script type="text/javascript">
									var _submit = document.getElementById('submit'),
										_file = document.getElementById('file<?php echo md5($this->path); ?>'),
										_progress = document.getElementById('progressBar');

									var upload = function() {

										if(_file.files.length==0) {return;}
										
										var data = new FormData();
										data.append('fileToUpload',_file.files[0]);
										data.append('action',"upload");
										data.append('dir',"<?php echo $this->relPath;?>");

										var request = new XMLHttpRequest();
										request.onreadystatechange = function(){
											if(request.readyState == 4) {
												console.log(request.responseText);
												var status = JSON.parse(request.responseText).status;
												location.href="./?action=upload&status="+status;
											}
										};

										request.upload.addEventListener('progress',function(e){
											_progress.style.width = Math.ceil((e.loaded/e.total)*100) + "%";
										},false);

										request.open('POST','./');
										request.send(data);
									}

									_submit.addEventListener('click',upload);
								</script>
								
							</form>
							
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal">Salir</button>
						</div>
						</div>
						
					</div>
				</div>

				<div class="modal fade" id="makedir<?php echo md5($this->path); ?>" role="dialog">
					<div class="modal-dialog">
						
						<!-- Modal content-->
						<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal">&times;</button>
							<h4>Crear directorio</h4>
						</div>
						<div class="modal-body">
							<form action="" method="post">
								<?php //Por seguridad, poner como value la ruta relativa a la carpeta del usuario
									//Manejar en el controlador la ruta relativa a la raiz de la web ?>
								<input type="hidden" name="dir" value="<?php echo $this->relPath; ?>"">
								<input type="hidden" name="action" value="makedir">
									
								Nombre del directorio:
								<input type="text" name="dirName">
									
								<input type="submit" class="btn btn-primary" value="Crear">
							</form>
								
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal">Salir</button>
						</div>
					</div>
					</div>
				</div>
			</div>
			
			<div id="collapse<?php echo md5($this->path); ?>" class="panel-collapse collapse">
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