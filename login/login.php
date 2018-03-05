<?php

class Login {

	//Args:
	//	$msgtype (warning, success, info, danger): Tipo de error
	//	$msgtext (html): Mensajito guachi
	//Al crear la clase con new Login("",""); no se muestra mensajito
	
	function __construct($msgtype,$msgtext) {
		?>
		<head>
			<meta charset="utf-8">
		    <meta name="viewport" content="width=device-width, initial-scale=1">
		    <meta name="description" content="">
		    <meta name="author" content="">
		    <link rel="icon" href="img/favicon.ico">
				
		    <title>FileFucktory</title>
				
		    <!-- Bootstrap core CSS -->
		    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
					
		    <!-- Custom styles for this template -->
		    <link href="css/signin.css" rel="stylesheet">
		</head>

		<body>
			<div class="text-center">
			
				<form class="form-signin" action="" method="post">
				
			    	<a href=""><img class="mb-4" src="img/logo.png" alt="FileFucktory" width="300"></a>
	
					<?php
					if($msgtype != "") {
						?>
						<div class="alert alert-<?php echo $msgtype ?>">
							<?php echo $msgtext; ?>
						</div>
						<?php
					}
					?>
	
			    	<h1 class="h3 mb-3 font-weight-normal">Inicie Sesion</h1>
			    	
			    	<label for="inputEmail" class="sr-only">Email</label>
			    	<input type="email" name="email" id="inputEmail" class="form-control" placeholder="Email address" required autofocus>
		
			    	<label for="inputPassword" class="sr-only">Contrasena</label>
			    	<input type="password" name="password" id="inputPassword" class="form-control" placeholder="Password" required>
	
					<input type="hidden" name="action" value="login">
					
			       	<div class="checkbox mb-3">
				    	<label>
				    		<input type="checkbox" value="remember-me"> Mantener sesion iniciada (WIP)
				    	</label>
			    	</div>
			    	
			    	<button class="btn btn-lg btn-primary btn-block" type="submit">Entrar</button>
			    	
			    	<p class="mt-5 mb-3 text-muted">&copy; Cuacabel Cuacabelaria</p>
		
			    </form>
		
			</div>

		</body>
		<?php
	}

}
?>
