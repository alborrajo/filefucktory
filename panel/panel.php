<?php

include "panel/objects/Dir.php";

class Panel {

	function __construct($dir,$msgtype,$msgtext) {
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
							<button type="button" class="btn" data-toggle="modal" data-target="#makedirModal">Crear carpeta<span class="fa fa-plus-circle"></span><span class="fa fa-folder-open"></span></button>
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
						          		<input type="hidden" name="dir" value="<?php echo $dir->name?>">	
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
												data.append('dir',"<?php echo $dir->name;?>");

												var request = new XMLHttpRequest();
												request.onreadystatechange = function(){
													if(request.readyState == 4) {
														console.log(request.responseText);
														var status = JSON.parse(request.responseText).status;
														location.href="./?action=upload&status="+status+"&dir=<?php echo $dir->name;?>";
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

						<div class="modal fade" id="makedirModal" role="dialog">
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
										<input type="hidden" name="dir" value="<?php echo $dir->name; ?>"">
										<input type="hidden" name="action" value="makedir">
											
										Nombre del directorio:
										<input type="text" name="dirName"></input>
											
										<input type="submit" class="btn btn-primary" value="Crear">
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
								$used = round($dir->space["usedmb"]); //Used space in MB
								$usedString = $used." MB";
								
								$total = round($dir->space["spacemb"]); //Total space in MB
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
						<?php $dir->buildHTML(); ?>
					</div>
		
			</div>

		</body>
	</html>
		<?php
	}

}
?>
