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
		?>
		<a href="<?php echo $this->path; ?>">
			<?php echo $this->name; ?>
		</a>
		(<?php echo $this->getSizeString(); ?>)
		

		<div class="btn-group pull-right">
			<!-- Delete -->	<button type="button" class="btn btn-danger btn-xs" data-toggle="modal" data-target="#delete<?php echo md5($this->path); ?>"><span class="fa fa-trash"></span></button>
			<!-- Mover -->	<button type="button" class="btn btn-primary btn-xs" data-toggle="modal" data-target="#move<?php echo md5($this->path); ?>"><span class="fa fa-share"></button>
		</div>

		<!-- DELETE MODAL -->
		<div class="modal fade" id="delete<?php echo md5($this->path); ?>" role="dialog">
			<div class="modal-dialog">
			
				<!-- Modal content-->
				<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4>Eliminar <?php echo $file["file"]; ?></h4>
				</div>
				<div class="modal-body">
				
					<form action="" method="post">

						<?php //Por seguridad, poner como value la ruta relativa a la carpeta del usuario
								//Manejar en el controlador la ruta relativa a la raiz de la web ?>
						<input type="hidden" name="file" value="<?php echo $this->path; ?>"">
						<input type="hidden" name="action" value="delete">
						
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
		<div class="modal fade" id="move<?php echo md5($this->path); ?>" role="dialog">
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
	}
}
?>