<?php

include_once("constants.php");
include_once("classes.php");

function eft_create_password_hash(string $password_raw) : string {
	/*
	PHP password_hash and password_verify:
	salting is added automatically by password_hash
	the result of password_hash includes the actual hash plus info about the hashing algorithm used and the salt, all in one string
	handing that string to password_verify gives it all the info it needs
	*/
	
	return password_hash($password_raw, PASSWORD_BCRYPT);
}

function eft_verify_login(string $password_raw, string $password_hashed) : bool {
	$verify_result = password_verify($password_raw, $password_hashed);
	if($verify_result) 
		return True;
	return False;
}

// Assumes permissions to add a user have already been verified
// Returns id of the user
function eft_persist_new_user(Eft_User $user) : int {
	return eft_use_file_lock(DATA_FILE_USERS, eft_persist_new_user_callback);
}

// intended to be passed as a callback to a data_access.php function
// returns id of the user
function eft_persist_new_user_callback($file_pointer) : int {
	$format_version = eft_get_data_format_version($file_pointer);
	if($format_version != "1.0") {
		throw new Exception(MESSAGE_UNKNOWN_DATA_FORMAT);
	}
	$users = eft_deserialize_users_format_1_0($file_pointer);
	//if username is already used throw exception
	//if email is not null and is already used throw exception
	//determine next id number
	//append new user with next id
	//return id
}

?>
