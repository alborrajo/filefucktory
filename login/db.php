<?php
class DB {

	function checkUser($email, $pass) {
		//Load DB
		$db = json_decode(file_get_contents("config/users.json"));

		//Check entries for a match
		foreach($db->users as $user) {
			if($user->email == $email && password_verify($pass, $user->password)) {
				return "success";
			}
		}

		//If there are no results
		if(empty($db)) {
			return "warning";
		}
	}

	function inviteUser($email) {	
		//Load DB
		$db = json_decode(file_get_contents("config/users.json"));

		//Check entries for a match (A user is already invited)
		foreach($db->invites as $invite) {
			if($invite->email == $email) {
				return "info";
			}
		}

		//If not already invited, add invite
		$newInvite = clone $db->invites[0];
		$newInvite->email = $email;

		//Write updated DB to file
		if(array_push($db->invites, $newInvite) && file_put_contents("config/users.json",json_encode($db))) {
			return "success";
		} else {
			return "danger";
		}

	}

	function newUser($panelModel, $email, $pass) {
		//Load DB
		$db = json_decode(file_get_contents("config/users.json"));

		//Check entries for a match (User already exists)
		foreach($db->users as $user) {
			if($user->email == $email) {
				return "info";
			}
		}

		//Check entries for a match (User is invited)
		foreach($db->invites as $invite) {
			if($invite->email == $email) {
				//Get folderName to make new folder
				$folderName = $panelModel->getFolder($email);

				//If there isn't an user with that email, the folder exists, and has been invited, add user to DB
				if(is_dir('./files/'.$folderName) || mkdir('./files/'.$folderName) ) {	
					//Add new user to DB
					$newUser = clone $db->users[0];
					$newUser->email = (string)$email;
					$newUser->password = (string)password_hash($pass, PASSWORD_DEFAULT);
					$newUser->spacemb = (string)"1024";

					if(!array_push($db->users,$newUser)) {return "danger";} //Add new user, return error if it goes wrong

					//Remove invite from DB
					//if(!unset($invite)) {return "danger";} //Remove invite, return error if it goes wrong
					unset($invite);

					//Write DB
					if(file_put_contents("config/users.json",json_encode($db))) {
						return "success";
					}
					else {
						return "danger"
					}
				}
			}
		}

		//If email is not invited
		return "warning"
	}
}