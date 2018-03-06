<?php

class Login {

	//Args:
	//	$msgtype (warning, success, info, danger): Tipo de error
	//	$msgtext (html): Mensajito guachi
	//Al crear la clase con new Login("",""); no se muestra mensajito
	
	function __construct($msgtype,$msgtext) {
		?>
	<html>
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

			<!-- Cookie functions -->
		    <script src="js/cookies.js"></script>

		    <!-- Hashing JS -->
			<script src="js/jshash-2.2/sha512-min.js"></script>

		    <!-- Password hashing script -->
			<script>
				function submitLogin(inputEmailId, inputPasswordId, inputPasswordHashId, inputRememberId) {
					var inputEmail = document.getElementById(inputEmailId);
				
					var inputPassword = document.getElementById(inputPasswordId);
					var inputPasswordHash = document.getElementById(inputPasswordHashId);

					var inputRemember = document.getElementById(inputRememberId);
					
					inputPasswordHash.value = hex_sha512(inputPassword.value);
					inputPassword.disabled = true;

					if(inputRemember.checked) {
						setCookie("email",inputEmail.value,365);
						setCookie("passwordHash",inputPasswordHash.value,365);

						alert(getCookie("passwordHash"));
					}

				}
			</script>
		    
		</head>

		<body>
			<div class="text-center">
			
				<form class="form-signin" action="./" method="post" onsubmit="submitLogin('inputEmail','inputPassword','inputPasswordHash', 'inputRemember');">
				
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
			    	<input type="hidden" name="passwordHash" id="inputPasswordHash">
	
					<input type="hidden" name="action" value="login">
					
			       	<div class="checkbox mb-3">
				    	<label>
				    		<input type="checkbox" value="remember-me" id="inputRemember"> Mantener sesion iniciada (WIP)
				    	</label>
			    	</div>
			    	
			    	<button class="btn btn-lg btn-primary btn-block" type="submit">Entrar</button>
		
			    </form>

    			<!-- Cookies! -->
    			<script>    			
    				if(getCookie("email") != null && getCookie("passwordHash") != null) {
    					var inputEmail = document.getElementById("inputEmail");
    					var inputPasswordHash = document.getElementById("inputPasswordHash");
    
    					inputEmail.value = getCookie("email");
    					inputPasswordHash.value = getCookie("passwordHash");
			    
    					inputPasswordHash.form.submit();
    				}
    			</script>

	   	    	<p class="mt-5 mb-3 text-muted">&copy; Cuacabel Cuacabelaria</p>
		
			</div>

		</body>
	</html>
		<?php
	}

}
?>
