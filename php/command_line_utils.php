<?php

include("constants.php");

function eft_handle_command($arguments) {
	//$arguments[0] will be "command_line.php"
	
	$arg_1 = eft_get_element($arguments, 1);
	if($arg_1 == "-a" || $arg_1 == "--add") {
		eft_handle_add_user(array_slice($arguments, 2));
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
	$isAdmin = false;
	if(eft_get_element($arguments, 0) == "--admin") {
		$isAdmin = true;
		$arguments = array_slice($arguments, 1);
	}
	$argument_pairs = eft_get_argument_pairs($arguments);
	$username = eft_get_element($argument_pairs, "-u");
	$password = eft_get_element($argument_pairs, "-p");
	$email = eft_get_element($argument_pairs, "-e");
	$phone = eft_get_element($argument_pairs, "-ph");
	
	if($username == null || $password == null) {
		throw new Exception(MESSAGE_ADD_USER_REQUIRED_ARGUMENTS);
	}
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