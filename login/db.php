<?php
class DB {

	function checkUser($email, $pass) {
		$manager = new MongoDB\Driver\Manager("mongodb://localhost:27017");
		
		$query = new MongoDB\Driver\Query(array("email"=>$email,"password"=>$pass));				
		$existingUser = $manager->executeQuery('filefucktory.user', $query);
		
		if(!empty($existingUser->toArray())) {
			return "success";
		}
		else {
			return "warning";
		}
	}

	function newUser($email, $pass) {
		//Check if there is already an user with that email
		$manager = new MongoDB\Driver\Manager("mongodb://localhost:27017");
				
		$query = new MongoDB\Driver\Query(array("email"=>$email));				
		$existingUser = $manager->executeQuery('filefucktory.user', $query);
		
		if(!empty($existingUser->toArray())) {
			return "warning";
		}
		//If there arent, add
		else {		
			$data = array(
				"email" => $email,
				"password" => $pass,
				"invites" => array()	
			);
			
			$query = new MongoDB\Driver\Query($data);				
			$result = $manager->executeQuery('filefucktory.user', $query);

			if($result) {
				return  "success";
			}
			else {
				return "danger";
			}
		}
	}
}