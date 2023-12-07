<?php

function eft_create_password_hash($password_raw) {
	/*
	PHP password_hash and password_verify:
	salting is added automatically by password_hash
	the result of password_hash includes the actual hash plus info about the hashing algorithm used and the salt, all in one string
	handing that string to password_verify gives it all the info it needs
	*/
	
	return password_hash($password_raw, PASSWORD_BCRYPT);
}

function eft_verify_login($password_raw, $password_hashed) {
	$verify_result = password_verify($password_raw, $password_hashed);
	if($verify_result) 
		return True;
	return False;
}

/*
// Assumes permissions to add a user have already been verified
// $user is a User object
function eft_persist_new_user(User $user) {
}

//function eft_store_user
*/
?>