<?php

include 'login/login.php';
include 'login/db.php';

include 'login/register.php';
include 'login/mailmodel.php';

include 'panel/panel.php';
include 'panel/panelmodel.php';
?>

		<?php
		session_start();
		
		// Si no hay sesión
		if(empty($_SESSION)) {
			if(isset($_POST["action"])) {
				switch($_POST["action"]) {
					case 'login':
						$db = new DB();
						$panelModel = new PanelModel();
						$result = $db->checkUser($_POST["email"],$_POST["passwordHash"]);
						switch($result) {
							case "success":
								$_SESSION["email"] = $_POST["email"];
								$_SESSION["password"] = $_POST["passwordHash"];
								$_SESSION["userFolder"] = $panelModel->getFolder($_POST["email"]);
								header("Location: ./?");
								break;
								
							default:
								//Clear session
								session_destroy();
						
								//Clear cookies
								setcookie("email","",-3600);
								setcookie("passwordHash","",-3600);

								//Load login page with message
								header("Location: ./?action=".$_POST["action"]."&result=".$result);
								exit();
								break;

						}
						break;
						
					case 'register':
						$db = new DB();
						$result = $db->newUser($_POST["email"],$_POST["passwordHash"]);

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


					case 'register': //Tras un POST de registrarse o a punto de registrarse
					
						//A punto de registrarse
						if(isset($_GET["email"])) {
							new Register($_GET["email"],null,null);
						}

						//Tras un POST de registrarse
						else {
							switch($_GET["result"]) {
								case "success":
									new Login("success","Cuenta creada con exito");
									break;
								case "info":
									new Login("info","Ya existe una cuenta con ese email");
									break;
								case "warning":
									new Login("warning","El email no ha sido invitado a FileFucktory");
									break;
								case "danger":
									new Login("danger","Error desconocido mientras se creaba la cuenta");
									break;
								default:
									new Login("info","<strong>chiquitan</strong> chiquitin tan tan tan quetun pampam quetum pan que tepetepe tam tantan quetum pam quepin");
									break;
							}						
							break;
						}

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
			if(isset($_GET["action"]) && $_GET["action"] == "logout") {
				session_destroy();
				new Login("success","Sesión cerrada");
			}

			// Register
			elseif(isset($_GET["action"]) && $_GET["action"] == "register") {
				session_destroy();
				header("Location: ./?action=register&email=".$_GET["email"]);
				exit();
			}
			
			//Funcionamiento normal
			else {
				//Comprobar si la sesión iniciada es correcta
				$db = new DB();
				$result = $db->checkUser($_SESSION["email"],$_SESSION["password"]);

				switch($result) {
					//Si lo es
					case "success":
					
						//If a folder is received, use it
						if(isset($_REQUEST["dir"])) {
							$folder = str_replace("../","",$_REQUEST["dir"]); //For security reasons, avoid users from putting ../ somewhere and escaping their folder
						}
						else {
							$folder = "";
						}
					
						//Comprobar si viene alguna accion
						if(isset($_POST["action"])) {
							$status = null;
							$panelModel = new PanelModel();
		
							switch($_POST["action"]) {
								case "upload": //Subida de fichero
									$status = $panelModel->uploadFile($folder,$_FILES,$_SESSION["email"]);
									break;

								case "makedir": //Directory creation
									$status = $panelModel->makeDir($folder,$_POST["dirName"]);
									break;
									
								case "delete":
									$status = $panelModel->deleteFile($_POST["file"]);
									break;

								case "deleteDir":
									$status = $panelModel->deleteDir($_POST["dirToDelete"]);
									break;

								case "invite":
									$db = new DB();
									$statusDB = $db->inviteUser($_POST["email"]);

									//if($statusDB == "success" || $statusDB == "info") {
										$mailModel = new MailModel();
										if($mailModel->sendInvite($_POST["email"])) {
											$status = "success";
										}
										else {
											$status = "warning";
										}
									//} else {
									//	$status = $statusDB;
									//}
									break;
								
								default:
									$status = "warning";
									break;
							}

							header("Location: ./?action=".$_POST["action"]."&status=".$status."&dir=".$folder);							
							exit;
						}
						
						//Si no, comprobar si viene por GET
						if(isset($_GET["action"])) {

							$msgtext = "";
							$msgstatus = $_GET["status"];
																				
							switch($_GET["action"]) {
								case "upload":								
									switch($_GET["status"]) {
										case "success":
											$msgtext = "Fichero subido con éxito";
											break;
										case "info":
											$msgtext = "Formato de fichero no admitido";
											break;
										case "warning":
											$msgtext = "El fichero ya existe";
											break;
										case "warningfull":
											$msgtext = "No hay suficiente espacio libre";
											$msgstatus = "warning";
											break;
										case "danger":
											$msgtext = "Error en la subida del fichero";
											break;
										default:
											$msgtext = "Algo raro ha ocurrido";
											break;
									}
									break;

								case "makedir":
									switch($_GET["status"]) {
										case "success":
											$msgtext = "Directorio creado con éxito";
											break;
										case "warning":
											$msgtext = "Error creando el directorio";
											break;
										default:
											$msgtext = "Algo raro ha ocurrido";
											break;
									}
									break;

								case "delete":
									switch($_GET["status"]) {
										case "success":
											$msgtext = "Fichero eliminado con éxito";
											break;
										case "warning":
											$msgtext = "Error eliminando el fichero";
											break;
										default:
											$msgtext = "Algo raro ha ocurrido";
											break;
									}
									break;

								case "deleteDir":
									switch($_GET["status"]) {
										case "success":
											$msgtext = "Directorio eliminado con éxito";
											break;
										case "warning":
											$msgtext = "Error eliminando el directorio";
											break;
										default:
											$msgtext = "Algo raro ha ocurrido";
											break;
									}
									break;

								case "invite":
									switch($_GET["status"]) {
										case "success":
											$msgtext = "Invitación enviada";
											break;
										case "info":
											$msgtext = "Email ya invitado";
											break;
										case "info":
											$msgtext = "Error enviando el email";
											break;
										case "danger":
											$msgtext = "Error invitando al usuario";
											break;
										default:
											$msgtext = "Algo raro ha ocurrido";
											break;
									}
									break;

								default:
									$msgtext = "Algo raro ha ocurrido";
									break;	
									
							}
							
							$panelModel = new PanelModel();
							new Panel($folder,$panelModel->checkFolder($folder,$_SESSION["email"]),$msgstatus,$msgtext);				
						}
						
						//Si no, mostrar panel
						else {
							$panelModel = new PanelModel();
							new Panel($folder,$panelModel->checkFolder($folder,$_SESSION["email"]),null,null);
						}
						
						break;

					//Si hay algún error
					default:
						//Clear session
						session_destroy();

						//Clear cookies
						setcookie("email","",-3600);
						setcookie("passwordHash","",-3600);

						//Display login page
						new Login("danger","Error con la sesión iniciada");
						break;
				}
			}			
	
		}

		?>