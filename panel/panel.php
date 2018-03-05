<?php
class Panel {

	function __construct($folder,$files,$alert) {
		?>

		<head>
			<meta charset="utf-8"> 
			<meta name="viewport" content="width=device-width, initial-scale=1">
		    <link rel="icon" href="img/favicon.ico">

			<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
			<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>		    

			<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
			<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

		</head>

		<body>
			<!-- Navbar -->
			<nav class="navbar navbar-inverse">
				<div class="container-fluid">
					<a class="navbar-brand" href="" style="padding: 5px; margin-top:5px">
						<img src="img/logowhite.png" class="img-fluid" style="height: 100%;">
					</a>
					<ul class="nav navbar-nav navbar-right" style="padding: 5px 10px 0px 0px">
						<li>
							<form action="" method="post">
								<input type="hidden" name="action" value="logout">
								<button type="submit" class="btn navbar-btn">Desconectar</button>
							</form>
						</li>
					</ul>
				</div>
			</nav>
		
			<!-- Contenido -->
			<div class="container">
		
				<h2>Ficheros</h2>

				<?php
				//Si no se ha omitido el tercer arg
				if($alert != null) {
					?>
						<div class="alert alert-<?php echo $alert["status"] ?>">
							<?php
							switch($alert["action"]) {
								case "upload":
									switch($alert["status"]) {
										case "success":
											echo "Fichero subido con éxito";
											break;
										case "info":
											echo "Formato de fichero no admitido";
											break;
										case "warning":
											echo "El fichero ya existe";
											break;
										case "danger":
											echo "Error en la subida del fichero";
											break;
										default:
											echo "Algo raro ha ocurrido";
											break;
									}
									break;

								case "delete":
									switch($alert["status"]) {
										case "success":
											echo "Fichero eliminado con éxito";
											break;
										case "warning":
											echo "Error eliminando el fichero";
											break;
										default:
											echo "Algo raro ha ocurrido";
											break;
									}
									break;

								default:
									echo "Algo raro ha ocurrido";
									break;	
									
							}
							?>
						</div>
					<?php
				}
				?>
		
				<div class="panel panel-default">
					<div class="panel-heading">
						<div class="btn-group">
							<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#uploadModal">Subir</button>
							<button type="button" class="btn">Crear carpeta (WIP)</button>
						</div>
						
						<div class="modal fade" id="uploadModal" role="dialog">
						    <div class="modal-dialog">
						    
						      <!-- Modal content-->
						      <div class="modal-content">
						        <div class="modal-header">
						          <button type="button" class="close" data-dismiss="modal">&times;</button>
						          <h4 class="modal-title">Subir fichero</h4>
						        </div>
						        <div class="modal-body">
						        
						          <form action="" method="post" enctype="multipart/form-data">
						          	<input type="hidden" name="action" value="upload">
						          	<label class="btn btn-default" for="fileToUpload">
						          		Elegir fichero <input type="file" name="fileToUpload" id="fileToUpload" style="display:none;" onchange="$('#fileInfo').html(this.files[0].name)">
						          	</label>
									<span class="label label-info" id="fileInfo"></span>
									
						    		<br>
						    		<br>
						    		
						          	<input type="submit" class="btn btn-primary" value="Subir">
						          </form>
						          
						        </div>
						        <div class="modal-footer">
						          <button type="button" class="btn btn-default" data-dismiss="modal">Salir</button>
						        </div>
						      </div>
						      
						    </div>
						</div>

						<div class="pull-right" width="30%";>
							<div class="label label-info">
							<?php
								$si_prefix = array('B', 'KB', 'MB', 'GB', 'TB');
								$base = 1024;

								$freeBytes = disk_free_space(".");
								$freeClass = min((int)log($freeBytes,$base), count($si_prefix) - 1);

								$allBytes = disk_total_space(".");
								$allClass = min((int)log($allBytes,$base), count($si_prefix) - 1);

								$remainingString = round($allBytes/pow($base,$freeClass)) - round($freeBytes/pow($base,$freeClass)) . " " . $si_prefix[$freeClass];
								$allString = round($allBytes/pow($base,$allClass)) . " " . $si_prefix[$allClass];
					
								echo $remainingString . " / " . $allString;
							?>
							</div>
						</div>
						
					</div>


					
					<div class="panel-body">
						<table class="table table-hover">
							<thead>
								<tr>
									<th>Fichero<span class="glyphicons glyphicons-file"></span></th>
									<th>Peso<span class="glyphicons glyphicons-pie-chart"></span></th>
									<th>Borrar<span class="glyphicons glyphicons-remove-sign"></span></th>
								</tr>
							</thead>
							
							<tbody>
								<?php
								$fileNum = 0;
								foreach($files as $file) {
									?>
									<tr>
										<!-- Nombre -->
										<td><a href="files/<?php echo $folder ?>/<?php echo $file["file"]?>"><?php echo $file["file"] ?></a></td>

										<!-- Tamaño -->
										<td>
											<?php
												//Redondear tamaño
												if($file["size"] > 1048576) {
													echo round($file["size"]/1048576) ?> MB<?php
												}
												else if($file["size"] > 1024) {
													echo round($file["size"]/1024) ?> KB<?php
												}
												else {
													echo round($file["size"]) ?> B<?php
												}
											?>
										</td>

										<!-- Borrar -->
										<td>
											<button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#delete<?php echo $fileNum; ?>" name="action" value="delete">
												<span class="glyphicons glyphicons-remove-sign">
											</button>

											<div class="modal fade" id="delete<?php echo $fileNum; ?>" role="dialog">
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
															<input type="hidden" name="file" value="<?php echo $file["file"]; ?>"">
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
											
										</td>
									</tr>
									<?php
								$fileNum++;
								}
								?>
							</tbody>
						
						</table>
					</div>
		
			</div>

		</body>
		<?php
	}

}
?>
