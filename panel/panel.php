<?php
class Panel {

	function __construct($folder,$files,$msgtype,$msgtext) {
		?>
	<html>
		<head>
			<meta charset="utf-8"> 
			<meta name="viewport" content="width=device-width, initial-scale=1">
		    <link rel="icon" href="img/favicon.ico">

			<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
			<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>		    

			<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css">
			
			<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
			<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

			<title>FileFucktory</title>

			<!-- Cookie functions -->
		    <script src="js/cookies.js"></script>

			<!-- Delete cookies -->
			<script>
				function delCookies() {
					eraseCookie("email");
					eraseCookie("passwordHash");
				}
			</script>
						
		</head>

		<body>
			<!-- Navbar -->
			<nav class="navbar navbar-inverse">
				<div class="container-fluid">
					<a class="navbar-brand" href="./" style="padding: 5px; margin-top: 4px;">
						<img src="img/logowhite.png" class="img-fluid" style="height: 100%;">
					</a>

					<ul class="nav navbar-nav navbar-right" style="padding: 4px 10px 0px 0px">>
						<li>
							<button type="button" class="btn navbar-btn btn-primary" data-toggle="modal" data-target="#inviteModal">Invitar <span class="fa fa-address-book"></span></button>
						</li>
						<li>
							<button type="button" class="btn navbar-btn" onclick="eraseCookie('email'); eraseCookie('passwordHash'); window.location=('./?action=logout');">Desconectar <span class="fa fa-sign-out"></span></button>
						</li>
					</ul>
					
				</div>
			</nav>

			<!-- Modal de invitaciones -->
			<div class="modal fade" id="inviteModal" role="dialog">
			    <div class="modal-dialog">
			    
			      <!-- Modal content-->
			      <div class="modal-content">
			        <div class="modal-header">
			          <button type="button" class="close" data-dismiss="modal">&times;</button>
			          <h4 class="modal-title">Invitar usuario</h4>
			        </div>

					<div class="modal-body">
						<form action="./" method="post">
							<label for="email">Email:</label>
							<input type="email" name="email" class="form-control"></input>
							<input type="hidden" name="action" value="invite">

							<br>
							
							<button type="submit" class="btn btn-primary">Enviar invitación</button>
						</form>
			        </div>
			        
			        <div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Salir</button>
			        </div>
			      </div>
									      
			    </div>
			</div>
		
		
			<!-- Contenido -->
			<div class="container">
		
				<h2>Ficheros</h2>

				<?php
				//Si no se ha omitido el tercer arg
				if($msgtype != null || $msgtype != "") {
					?>
						<div class="alert alert-<?php echo $msgtype ?>">
							<?php
								echo $msgtext;
							?>
						</div>
					<?php
				}
				?>
		
				<div class="panel panel-default">
					<div class="panel-heading">
						<div class="btn-group">
							<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#uploadModal">Subir <span class="fa fa-cloud-upload"></span></button>
							<button type="button" class="btn">Crear carpeta (WIP) <span class="fa fa-plus-circle"></span><span class="fa fa-folder-open"></span></button>
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

										<div class="progress">
											<div class="progress-bar progress-bar-striped active" style="width:0%" id="progressBar"></div>
										</div>
						    		
						          		<input type="button" class="btn btn-primary" value="Subir" id="submit">

						          		<script type="text/javascript">
											var _submit = document.getElementById('submit'),
												_file = document.getElementById('fileToUpload'),
												_progress = document.getElementById('progressBar');

											var upload = function() {

												if(_file.files.length==0) {return;}
												
												var data = new FormData();
												data.append('fileToUpload',_file.files[0]);
												data.append('action',"upload");

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

						<div class="pull-right" width="30%";>
							<div class="label label-info">
							<?php
								$used = round($files["space"]["usedmb"]); //Used space in MB
								$usedString = $used." MB";
								
								$total = round($files["space"]["spacemb"]); //Total space in MB
								$totalString = $total." MB";

								//Convert to GB if needed (with one digit after the dot)
								//Used space
								if($used >= 1024)  {
									$used = round($used/1024, 1);
									$usedString = $used." GB";
								}
								//Free space
								if($total >= 1024) {
									$total = round($total/1024, 1);
									$totalString = $total." GB";
								}
				
								echo $usedString . " / " . $totalString;
							?>
							</div>
						</div>
						
					</div>


					
					<div class="panel-body">
						<table class="table table-hover">
								<tr>
							<thead>
									<th>Fichero <span class="fa fa-file"></span></th>
									<th>Peso <span class="fa fa-pie-chart"></span></th>
									<th>Borrar <span class="fa fa-trash"></span></th>
								</tr>
							</thead>
							
							<tbody>
								<?php
								$dirNum = 0;
								foreach($files["dirs"] as $dir) {
									?>
									<tr>
										<!-- Nombre -->
										<td>
											<form action="" method="post" id="dir<?php echo $dirNum;?>">
												<input type="hidden" name="dir" value="<?php echo $folder."/".$dir["dir"]; ?>">
												<a href="#" onclick="document.getElementById('dir<?php echo $dirNum;?>').submit()"><span class="fa fa-folder-open"></span> <?php echo $dir["dir"]; ?></a>
											</form>
										</td>

										<!-- Tamaño -->
										<td>
											<?php
												//Redondear tamaño
												if($dir["size"] > 1073742000) {
													echo round($dir["size"]/1073742000,2) ?> GB<?php
												}
												if($dir["size"] > 1048576) {
													echo round($dir["size"]/1048576) ?> MB<?php
												}
												else if($dir["size"] > 1024) {
													echo round($dir["size"]/1024) ?> KB<?php
												}
												else {
													echo round($dir["size"]) ?> B<?php
												}
											?>
										</td>

										<!-- Borrar -->
										<td>
											<button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteDir<?php echo $dirNum; ?>" name="action" value="delete">
												<span class="fa fa-trash"><span class="fa fa-folder-open"></span> (WIP)
											</button>

											<div class="modal fade" id="deleteDir<?php echo $dirNum; ?>" role="dialog">
											    <div class="modal-dialog">
											    
											      <!-- Modal content-->
											      <div class="modal-content">
											        <div class="modal-header">
											        	<button type="button" class="close" data-dismiss="modal">&times;</button>
														<h4>Eliminar directorio <?php echo $dir["file"]; ?></h4>
											        </div>
					        				        <div class="modal-body">
					        				        
														<form action="" method="post">

															<?php //Por seguridad, poner como value la ruta relativa a la carpeta del usuario
																	//Manejar en el controlador la ruta relativa a la raiz de la web ?>
															<input type="hidden" name="file" value="<?php echo $dir["file"]; ?>"">
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
								$dirNum++;
								}


								
								$fileNum = 0;
								foreach($files["files"] as $file) {
									?>
									<tr>
										<!-- Nombre -->
										<td><a href="files/<?php echo $_SESSION["userFolder"] ?>/<?php echo $folder ?>/<?php echo $file["file"]?>"><?php echo $file["file"] ?></a></td>

										<!-- Tamaño -->
										<td>
											<?php
												//Redondear tamaño
												if($file["size"] > 1073742000) {
													echo round($file["size"]/1073742000,2) ?> GB<?php
												}
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
												<span class="fa fa-trash">
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
	</html>
		<?php
	}

}
?>
