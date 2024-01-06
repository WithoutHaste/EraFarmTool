<?php

include_once("constants.php");
include_once("global_utils.php");
include_once("classes.php");
include_once("security.php");
include_once("data_access.php");

const EFT_CLI_TAG_LONG_ISADMIN = "--admin";
const EFT_CLI_TAG_SHORT_ADDUSER = "-a";
const EFT_CLI_TAG_LONG_ADDUSER = "--add";
const EFT_CLI_TAG_SHORT_USERNAME = "-u";
const EFT_CLI_TAG_SHORT_PASSWORD = "-p";
const EFT_CLI_TAG_SHORT_EMAIL = "-e";
const EFT_CLI_TAG_SHORT_PHONENUMBER = "-ph";

// Returns a message describing what was done
function eft_handle_command(array $arguments) : string {
	//$arguments[0] will be "command_line.php"
	try 
	{	
		$arg_1 = eft_get_element($arguments, 1);
		if($arg_1 == EFT_CLI_TAG_SHORT_ADDUSER || $arg_1 == EFT_CLI_TAG_LONG_ADDUSER) {
			$user_id = eft_handle_add_user(array_slice($arguments, 2));
			return "user added with id: ".$user_id;
		}
	}
	catch(Exception $exception)
	{
		return "ERROR: ".$exception->getMessage();
	}
	
	return "no action taken";
}

// Returns the id of the user
function eft_handle_add_user(array $arguments) : int {
	$user = eft_parse_user_from_arguments($arguments);
	$id = eft_persist_new_user($user);
	return $id;
}

function eft_parse_user_from_arguments(array $arguments) : Eft_User {
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
function eft_get_argument_pairs(array $arguments) : array {
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
function eft_is_dash_argument(?string $argument) : bool {
	return (strlen($argument) > 1 && $argument[0] == "-");
}

?>