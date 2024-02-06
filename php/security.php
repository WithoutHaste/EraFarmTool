<?php

include_once("constants.php");
include_once("classes.php");
include_once("data_access.php");

function eft_create_password_hash(string $password_raw) : string {
	/*
	PHP password_hash and password_verify:
	salting is added automatically by password_hash
	the result of password_hash includes the actual hash plus info about the hashing algorithm used and the salt, all in one string
	handing that string to password_verify gives it all the info it needs
	*/
	
	return password_hash($password_raw, PASSWORD_BCRYPT);
}

function eft_verify_password(string $password_raw, string $password_hashed) : bool {
	$verify_result = password_verify($password_raw, $password_hashed);
	if($verify_result) 
		return True;
	return False;
}

// Returns array("id"=>user_id, "auth_key"=>authorization_key_for_this_session)
function eft_login(string $username, string $password_raw) {
	$user = eft_get_user_by_username($username);
	$is_authorized = false;
	if($user != null) {
		$is_authorized = eft_verify_password($password_raw, $user->password_hashed);
	}
	if(!$is_authorized) {
		sleep(5);
		throw new Exception(MESSAGE_UNAUTHORIZED);
	}
	
	$guid = new_guid();
	eft_update_user_with_login_session($user->id, $guid);
	$auth_key = eft_format_auth_key($user->id, $guid);
	return array("id"=>$user->id, "auth_key"=>$auth_key);
}

function eft_format_auth_key($id, $session_key) : string {
	return md5($id."|".$session_key);
}

function eft_verify_auth_key($id, $auth_key) : bool {
	$user = eft_get_user_by_id($id);
	if($user == null) {
		return False;
	}
	
	$expected_auth_key = eft_format_auth_key($id, $user->session_key);
	return ($auth_key == $expected_auth_key);
}

?>
