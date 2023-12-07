<?php

include("constants.php");
include("classes.php");
include("security.php");

const EFT_CLI_TAG_LONG_ISADMIN = "--admin";
const EFT_CLI_TAG_SHORT_ADDUSER = "-a";
const EFT_CLI_TAG_LONG_ADDUSER = "--add";
const EFT_CLI_TAG_SHORT_USERNAME = "-u";
const EFT_CLI_TAG_SHORT_PASSWORD = "-p";
const EFT_CLI_TAG_SHORT_EMAIL = "-e";
const EFT_CLI_TAG_SHORT_PHONENUMBER = "-ph";

function eft_handle_command($arguments) {
	//$arguments[0] will be "command_line.php"
	
	$arg_1 = eft_get_element($arguments, 1);
	if($arg_1 == EFT_CLI_TAG_SHORT_ADDUSER || $arg_1 == EFT_CLI_TAG_LONG_ADDUSER) {
		eft_handle_add_user(array_slice($arguments, 2));
		//TODO handle exceptions
		exit();
	}
	
	echo "no action taken\n";
	
	/*
Add an admin user:    
`php command_line.php -a --admin -u username -p password [-e email] [-ph phonenumber]`  
`php command_line.php --add --admin -u username -p password [-e email] [-ph phonenumber]`  

Add a regular user:  
`php command_line.php -a -u username -p password [-e email] [-ph phonenumber]`  
`php command_line.php --add -u username -p password [-e email] [-ph phonenumber]`  
	
	*/
}


function eft_handle_add_user($arguments) {
	$user = eft_parse_user_from_arguments($arguments);
	
	//TODO hash password and update data file
}

// Returns a User object or throws an exception
function eft_parse_user_from_arguments($arguments) {
	$user = new Eft_User();
	if(eft_get_element($arguments, 0) == EFT_CLI_TAG_LONG_ISADMIN) {
		$user->is_admin = true;
		$arguments = array_slice($arguments, 1);
	}
	$argument_pairs = eft_get_argument_pairs($arguments);
	$user->username = eft_get_element($argument_pairs, EFT_CLI_TAG_SHORT_USERNAME);
	$password_raw = eft_get_element($argument_pairs, EFT_CLI_TAG_SHORT_PASSWORD);
	$user->email = eft_get_element($argument_pairs, EFT_CLI_TAG_SHORT_EMAIL);
	$user->phone_number = eft_get_element($argument_pairs, EFT_CLI_TAG_SHORT_PHONENUMBER);
	
	if($user->username == null || $password_raw == null) {
		throw new Exception(MESSAGE_ADD_USER_REQUIRED_ARGUMENTS);
	}
	
	$user->password_hashed = eft_create_password_hash($password_raw);
	
	return $user;
}

// Returns key=>value pairs of named arguments
// dash-arguments without a following string are ignored
// Ex: ["-u", "username"] returns ["-u"=>"username"]
function eft_get_argument_pairs($arguments) {
	$pairs = array();
	$i = 0;
	while($i < count($arguments)) {
		$current = eft_get_element($arguments, $i);
		$next = eft_get_element($arguments, $i+1);
		if(eft_is_dash_argument($current) && !eft_is_dash_argument($next) && $next != null) {
			$pairs[$current] = $next;
			$i+=2;
			continue;
		}
		$i++;
	}
	return $pairs;
}

// Returns true if string is formatted as "-xxx"
// Expects $argument is a string
function eft_is_dash_argument($argument) {
	return (strlen($argument) > 1 && $argument[0] == "-");
}

// Safe-get element from array, for arrays and associative arrays
// Returns null if the $index does not exist
// Expects $array is an array
function eft_get_element($array, $index) {
	if(array_key_exists($index, $array)) {
		return $array[$index];
	}
	return null;
}

?>