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

				<h2>Ficheros</h2>
					
				<!-- Panel -->
				<?php $dir->buildHTML(); ?>


				<!-- MOVE MODAL -->
				<script>
					function setMoveSource(destination) {
						return $('.moveSRC').val(destination);
					}
				</script>
				<div class="modal fade" id="move" role="dialog">
					<div class="modal-dialog">
					
						<!-- Modal content-->
						<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal">&times;</button>
							<h4>Mover</h4>
						</div>
						<div class="modal-body">

							<div class="panel-group">
								<div class="panel panel-default">
									<?php $dir->buildMoveHTML(); ?>
								</div>
							</div>
							
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal">Salir</button>
						</div>
						</div>
						
					</div>
				</div>
		
			</div>

		</body>
	</html>
		<?php
	}

}
?>
