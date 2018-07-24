<?php
class DB {

	function checkUser($email, $pass) {
		$manager = new MongoDB\Driver\Manager("mongodb://localhost:27017");
		
		$query = new MongoDB\Driver\Query(array("email"=>(string)$email));				
		$queryResult = $manager->executeQuery('filefucktory.user', $query);

		$queryArray = $queryResult->toArray();

		//If there are no results
		if(empty($queryArray)) {
			return "warning";
		}
		//If there are
		else {
			foreach($queryArray as $user) {
	
				if(password_verify($pass, $user->password)) {
					return "success";
				}
				else {
					return "warning";
				}
			}
		}
	}

	function inviteUser($email) {	
		//Check if user is already an existing user
		$manager = new MongoDB\Driver\Manager("mongodb://localhost:27017");

		$query = new MongoDB\Driver\Query(array("email"=>(string)$email));				
		$result = $manager->executeQuery('filefucktory.user',$query);

		if(!empty($result->toArray())) {
			return "info";
		}
		
		//Check if user is already invited
		$result = $manager->executeQuery('filefucktory.invited',$query);

		if(!empty($result->toArray())) {
			return "info";
		}

		//If not already invited, add invite
		$query = new MongoDB\Driver\BulkWrite(['ordered'=>true]);
		$query->insert(array("email"=>(string)$email));
		$result = $manager->executeBulkWrite('filefucktory.invited', $query);

		if($result) {
			return "success";
		} else {
			return "danger";
		}

	}

	function newUser($panelModel, $email, $pass) {
		//Check if there is already an user with that email
		$manager = new MongoDB\Driver\Manager("mongodb://localhost:27017");
				
		$query = new MongoDB\Driver\Query(array("email"=>(string)$email));				
		$existingUser = $manager->executeQuery('filefucktory.user', $query);
		
		if(!empty($existingUser->toArray())) {
			return "info";
		}

		//Check if the user is invited;
		$invited = $manager->executeQuery('filefucktory.invited',$query);

		if(empty($invited->toArray())) {
			return "warning";
		}

		//Get OID to make folder
		$folderName = $panelModel->getFolder($email);
		
		//Check dir or make user dir, and if that works, return success
		if(is_dir('./files/'.$folderName) || mkdir('./files/'.$folderName) ) {
			//If there isn't an user with that email, the folder exists, and has been invited, add user to DB
			$data = array(
				"email" => (string)$email,
				"password" => (string)password_hash($pass, PASSWORD_DEFAULT),
				"spacemb" => (string)"1024",
			);
			
			//Add user to DB
			$query = new MongoDB\Driver\BulkWrite(['ordered'=>true]);
			$query->insert($data);
			$result = $manager->executeBulkWrite('filefucktory.user', $query);

			//Delete invite from DB
			$query = new MongoDB\Driver\BulkWrite(['ordered'=>true]);
			$query->delete(array("email"=>(string)$email));
			$manager->executeBulkWrite('filefucktory.invited', $query);

			if($result) {
				return  "success";
			}
			else {
				return "danger";
			}
		
		}
		else {
			return "danger";
		}

	}
}