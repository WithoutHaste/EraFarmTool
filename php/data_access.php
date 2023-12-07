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

// returns the format version recorded in this file
function eft_get_data_format_version($file_pointer) {
	//TODO
}

// read in all users.txt records, format 1.0
// assumes the file format has already been correctly determined
function eft_deserialize_users_format_1_0($file_pointer) {
	//TODO
}

?>
