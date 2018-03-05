<?php

include 'login/login.php';
include 'login/db.php';

include 'panel/panel.php';
include 'panel/panelmodel.php';
?>

<html>

		<?php
		session_start();
		
		// Si no hay sesión
		if(empty($_SESSION)) {
			if(isset($_POST["action"])) {
				switch($_POST["action"]) {
					case 'login':
						$password = $_POST["password"]; //TODO: hashear $_POST["password"]
						$db = new DB();
						$result = $db->checkUser($_POST["email"],$password);
						switch($result) {
							case "success":
								$_SESSION["email"] = $_POST["email"];
								$_SESSION["password"] = $password;
								header("Location: ./");
								break;
								
							default:
								header("Location: ./?action=".$_POST["action"]."&result=".$result);
								exit();
								break;
						}
						break;
						
					case 'register':
						$db = new DB();
						$result = $db->newUser($_POST["email"],$_POST["password"]);

						header("Location: ./?action=".$_POST["action"]."&result=".$result);
						exit();
						break;

					default:
						header("Location: ./");
						break;
						
				}
			}
			elseif(isset($_GET["action"])) {
				switch($_GET["action"]) {
					case 'login': //Tras un POST de login
						switch($_GET["result"]) {
							case "warning":
								new Login("warning","Credenciales incorrectas");
								break;
															
							default:
								new Login("info","<strong>HUH HAH</strong>");
								break;
						}
						break;

					case 'register': //Tras un POST de registrarse
						switch($_GET["result"]) {
							case "success":
								new Login("success","Cuenta creada con exito");
								break;
							case "warning":
								new Login("warning","Ya existe una cuenta con ese email");
								break;
							case "danger":
								new Login("danger","Error desconocido mientras se creaba la cuenta");
								break;
							default:
								new Login("info","<strong>HUH HAH</strong>");
								break;
						}						
						break;
						
					case 'registerForm':
						//Comprobar si invitacion valida
						//if valido
						//	new formulario register
						//else
						//	new mensajito de error
						break;

					default:
						new Login("info","Visite nuestra web afiliada:<br><a href='http://fucktorio.ddns.net' class='alert-link'>fucktorio</a>");
						break;
				}
			}
			else {
				new Login("info","Visite nuestra web afiliada:<br><a href='http://fucktorio.ddns.net' class='alert-link'>fucktorio</a>");
			}
		}


		
		//Si hay sesión
		else {
			//Logout
			if(isset($_POST["action"]) && $_POST["action"] == "logout") {
				session_destroy();
				new Login("success","Sesión cerrada");
			}			

			//Funcionamiento normal
			else {
				//Comprobar si la sesión iniciada es correcta
				$db = new DB();
				$result = $db->checkUser($_SESSION["email"],$_SESSION["password"]);

				switch($result) {
					//Si lo es
					case "success":
					
						//Comprobar si viene alguna accion
						if(isset($_POST["action"])) {
							$status = null;
							$panelModel = new PanelModel();
							
							switch($_POST["action"]) {
								case "upload": //Subida de fichero
									$status = $panelModel->uploadFile($_FILES);
									break;
									
								case "delete":
									$status = $panelModel->deleteFile($_POST["file"]);
									break;
								
								default:
									$status = "warning";
									break;
							}

							header("Location: ./?action=".$_POST["action"]."&status=".$status);							
							
						}
						
						//Si no, comprobar si viene por GET
						if(isset($_GET["action"])) {
							$panelModel = new PanelModel();
							new Panel($_SESSION["password"],$panelModel->checkFolder($_SESSION["password"]),array("action"=>$_GET["action"],"status"=>$_GET["status"]));				
						}
						
						//Si no, mostrar panel
						else {
							$panelModel = new PanelModel();
							new Panel($_SESSION["password"],$panelModel->checkFolder($_SESSION["password"]),null);							
						}
						
						break;

					//Si hay algún error
					default:
						session_destroy();
						new Login("danger","Error con la sesión iniciada");
						break;
				}
			}			
	
		}

		?>
	
</html>