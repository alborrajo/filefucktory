<?php

class MailModel {

	function __construct() {
	}

	function sendInvite($email) {
		// To
		$to = $email;
		
		// Subject
		$subject = 'Invitación a FileFucktory';
		
		// Message
		$mailHTML = file_get_contents("login/mail.html");
		$mailContent = str_replace("%EMILIO%",$email,$mailHTML); //Replace %EMILIO% with the destination email address
		
		// To send HTML mail, the Content-type header must be set
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

		// Additional headerś
		$headers .= 'From: FileFucktory Enterprises S.L. <noreply@filefucktory.ga>'.'\r\n';
		
		// Mail it
		return mail($to, $subject, $mailContent, $headers,"-fnoreply@filefucktory.ga");
		
	}
	
}
?>
