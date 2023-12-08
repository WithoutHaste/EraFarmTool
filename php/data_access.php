<?php

include_once("constants.php");

// $callback is expecting a function name that accepts a $file_pointer argument and operates on it
// returns whatever the $callback returns
function eft_use_file_lock(string $file_name, $callback) {
	if(!file_exists($file_name)) {
		throw new Exception(MESSAGE_FILE_NOT_FOUND);
	}
	
	$file_pointer = fopen($file_name, "r+");
	if (flock($file_pointer, LOCK_EX)) {  // acquire an exclusive lock
		$result = $callback($file_pointer);
		fflush($file_pointer);            // flush output before releasing the lock
		flock($file_pointer, LOCK_UN);    // release the lock
		fclose($file_pointer);
		return $result;
	} 

	fclose($file_pointer);
	throw new Exception(MESSAGE_EDIT_RECORD_LOCK_FAILED);
}

/* NOTE TO SELF: PHP does not support explicitly setting parameter type "resource", leave it blank */

// returns the format version recorded in this file, or null
// $file_pointer expects an open file stream resource
function eft_get_data_format_version($file_pointer) : ?string {
	rewind($file_pointer);
	$line = fgets($file_pointer);
	if(!$line) {
		return null;
	}

	$line = trim($line, "\n\r ");
	$matches = "";
	preg_match_all('/\#version\:(.*)/i', $line, $matches);
	if(count($matches) < 2 || count($matches[1]) < 1 || !$matches[1][0]) {
		return null;
	}

	return $matches[1][0];
}

// read in all users.txt records, format 1.0
// assumes the file format has already been correctly determined
// $file_pointer expects an open file stream resource
// returns an array of Eft_User objects
function eft_deserialize_users_format_1_0($file_pointer) : array {
	$lines = eft_get_data_lines($file_pointer);
	$users = array();
	foreach($lines as $line) {
	}
	return $users;
}

// returns array of lines from the file
// which are not comments and not the headers
// order is maintained
// lines are trimmed
function eft_get_data_lines($file_pointer) {
	$lines = array();
	$found_headers = false;
	rewind($file_pointer);
	while(($line = fgets($file_pointer)) !== false) {
		if(preg_match('/^\#/', $line)) { //skip comment lines
			continue;
		}
		if(!$found_headers) {
			$found_headers = true; //first non-comment line is assumed to be the headers
			continue;
		}
		$line = trim($line, "\n\r ");
		array_push($lines, $line);
	}
	return $lines;
}

?>
