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

?>
