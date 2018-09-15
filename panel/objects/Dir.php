<?php

include "File.php";

class Dir{

	//ROOT DIRECTORY
	// - Can't be moved
	// - Can't be deleted
	public $rootDir;

	public $name;

	public $relPath;
	public $path;

	public $bytes;

	public $lsDirs;
	public $lsFiles;


	function __construct($workDir,$relPath,$rootDir) {

		$this->rootDir = $rootDir; //Is this directory the root directory?

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
				array_push($this->lsDirs, new Dir($workDir,$this->relPath."/".$file,false));
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
					<!-- Upload -->	<button type="button" class="btn btn-primary btn-xs" data-toggle="modal" data-target="#upload<?php echo md5($this->path); ?>"><span class="fa fa-cloud-upload"></span></button>
					<!-- mkdir -->	<button type="button" class="btn btn-xs" data-toggle="modal" data-target="#makedir<?php echo md5($this->path); ?>"><span class="fa fa-plus-circle"></span><span class="fa fa-folder-open"></span></button>
					<?php if(!$this->rootDir) { ?>
					<!-- Delete -->	<button type="button" class="btn btn-danger btn-xs" data-toggle="modal" data-target="#deleteDir<?php echo md5($this->path); ?>"><span class="fa fa-trash"></span></button>
					<!-- Mover -->	<button type="button" class="btn btn-primary btn-xs" data-toggle="modal" data-target="#moveDir<?php echo md5($this->path); ?>"><span class="fa fa-share"></button>
					<?php } ?>
				</div>

				<!-- UPLOAD MODAL -->
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
									Elegir fichero <input type="file" name="fileToUpload" id="file<?php echo md5($this->path); ?>" style="display:none;" onchange="$('#fileInfo<?php echo md5($this->path); ?>').html(this.files[0].name)">
								</label>
								<span class="label label-info" id="fileInfo<?php echo md5($this->path); ?>"></span>

								<div class="progress">
									<div class="progress-bar progress-bar-striped active" style="width:0%" id="uploadBar<?php echo md5($this->path); ?>"></div>
								</div>
							
								<input type="button" class="btn btn-primary" value="Subir" id="submit<?php echo md5($this->path); ?>">

								<script type="text/javascript">
									var _submit<?php echo md5($this->path); ?> = document.getElementById('submit<?php echo md5($this->path); ?>'),
										_file<?php echo md5($this->path); ?> = document.getElementById('file<?php echo md5($this->path); ?>'),
										_progress<?php echo md5($this->path); ?> = document.getElementById('uploadBar<?php echo md5($this->path); ?>');

									var upload<?php echo md5($this->path); ?> = function() {

										if(_file<?php echo md5($this->path); ?>.files.length==0) {return;}
										
										var data = new FormData();
										data.append('fileToUpload',_file<?php echo md5($this->path); ?>.files[0]);
										data.append('action',"upload");
										data.append('dir',"<?php echo $this->relPath;?>");

										console.log(data.toString());

										var request = new XMLHttpRequest();
										request.onreadystatechange = function(){
											if(request.readyState == 4) {
												console.log(request.responseText);
												var status = JSON.parse(request.responseText).status;
												location.href="./?action=upload&status="+status;
											}
										};

										request.upload.addEventListener('progress',function(e){
											_progress<?php echo md5($this->path); ?>.style.width = Math.ceil((e.loaded/e.total)*100) + "%";
										},false);

										request.open('POST','./');
										request.send(data);
									}

									_submit<?php echo md5($this->path); ?>.addEventListener('click',upload<?php echo md5($this->path); ?>);
								</script>
								
							</form>
							
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal">Salir</button>
						</div>
						</div>
						
					</div>
				</div>

				<!-- MKDIR MODAL -->
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
		
			<!-- DELETE MODAL -->
			<?php if(!$this->rootDir) { ?>
			<div class="modal fade" id="deleteDir<?php echo md5($this->path); ?>" role="dialog">
				<div class="modal-dialog">
				
					<!-- Modal content-->
					<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4>Eliminar directorio <?php echo $this->name; ?></h4>
					</div>
					<div class="modal-body">
					
						<form action="" method="post">

							<?php //Por seguridad, poner como value la ruta relativa a la carpeta del usuario
									//Manejar en el controlador la ruta relativa a la raiz de la web ?>
							<input type="hidden" name="dirToDelete" value="<?php echo $this->relPath; ?>"">
							<input type="hidden" name="action" value="deleteDir">
							
							<input type="submit" class="btn btn-danger" value="Eliminar">
						</form>
						
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Salir</button>
					</div>
					</div>
					
				</div>
			</div>


			<!-- MOVE MODAL -->
			<div class="modal fade" id="moveDir<?php echo md5($this->path); ?>" role="dialog">
				<div class="modal-dialog">
				
					<!-- Modal content-->
					<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4>Mover <?php echo $this->name; ?></h4>
					</div>
					<div class="modal-body">

						<div class="panel-group">
							<div class="panel panel-default">
								<h1>YA MAÑANA</h1> <!-- TODO -->
							</div>
						</div>
						
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Salir</button>
					</div>
					</div>
					
				</div>
			</div>
			<?php } ?>



			<!-- Show directories and files inside folder -->
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