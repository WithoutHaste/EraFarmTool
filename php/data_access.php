<?php

include_once("constants.php");

// $callback is expecting a function name that accepts a $file_pointer argument and operates on it
// returns whatever the $callback returns
// $callback_arguments is passed into the $callback
function eft_use_file_lock(string $file_name, $callback, $callback_arguments) {
	if(!file_exists($file_name)) {
		throw new Exception(MESSAGE_FILE_NOT_FOUND);
	}
	
	$file_pointer = fopen($file_name, "r+");
	if($file_pointer == false) {
		throw new Exception(MESSAGE_FILE_CANNOT_BE_OPENED);
	}
	if (flock($file_pointer, LOCK_EX|LOCK_NB)) {  // acquire an exclusive lock, but don't block if the lock fails
		$result = $callback($file_pointer, $callback_arguments);
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

/**
 * @return Eft_User[]
 */
function eft_deserialize_users($file_pointer) : array {
	$format_version = eft_get_data_format_version($file_pointer);
	switch($format_version) {
		case FORMAT_1_0: return eft_deserialize_users_format_1_0($file_pointer);
		default: throw new Exception(MESSAGE_UNKNOWN_DATA_FORMAT);
	}
}

/**
 * read in all users.txt records, format 1.0
 * assumes the file format has already been correctly determined
 * @param $file_pointer expects an open file stream resource
 * @return Eft_User[]
 */
function eft_deserialize_users_format_1_0($file_pointer) : array {
	$lines = eft_get_data_lines($file_pointer);
	$users = array();
	foreach($lines as $line) {
		$user = Eft_User::deserialize($line, FORMAT_1_0);
		if($user != null) {
			array_push($users, $user);
		}
	}
	return $users;
}

/**
 * @return Eft_Task[]
 */
function eft_deserialize_tasks($file_pointer) : array {
	$format_version = eft_get_data_format_version($file_pointer);
	switch($format_version) {
		case FORMAT_1_0: return eft_deserialize_tasks_format_1_0($file_pointer);
		default: throw new Exception(MESSAGE_UNKNOWN_DATA_FORMAT);
	}
}

/**
 * read in all tasks.txt records, format 1.0
 * assumes the file format has already been correctly determined
 * @param $file_pointer expects an open file stream resource
 * @return Eft_Task[]
 */
function eft_deserialize_tasks_format_1_0($file_pointer) : array {
	$lines = eft_get_data_lines($file_pointer);
	$tasks = array();
	foreach($lines as $line) {
		$task = Eft_Task::deserialize($line, FORMAT_1_0);
		if($task != null) {
			array_push($tasks, $task);
		}
	}
	return $tasks;
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

// Assumes permissions to add a user have already been verified
// Returns id of the user
function eft_persist_new_user(Eft_User $user) : int {
	return eft_use_file_lock(DATA_FILE_USERS, 'eft_persist_new_user_callback', $user);
}
// intended to be passed as a callback to a data_access.php function
// returns id of the user
function eft_persist_new_user_callback($file_pointer, $new_user) : int {
	$users = eft_deserialize_users($file_pointer);
	$max_id = 0;
	foreach($users as $user) {
		if($user->username == $new_user->username) {
			throw new Exception(MESSAGE_USERNAME_COLLISION);
		}
		if($new_user->email != null && $user->email == $new_user->email) {
			throw new Exception(MESSAGE_EMAIL_COLLISION);
		}
		if($user->id > $max_id) {
			$max_id = $user->id;
		}
	}
	$format_version = eft_get_data_format_version($file_pointer);
	$new_user->id = $max_id + 1;
	$new_user->created_date = new DateTime();
	$new_user_serialized = $new_user->serialize($format_version);
	fseek($file_pointer, 0, SEEK_END); //go to end of file
	fwrite($file_pointer, "\n".$new_user_serialized);
	return $new_user->id;
}

// Returns user record or null
function eft_get_user_by_id($id) : ?Eft_User {
	return eft_use_file_lock(DATA_FILE_USERS, 'eft_get_user_by_id_callback', $id);
}
// intended to be passed as a callback to a data_access.php function
// returns user record or null
function eft_get_user_by_id_callback($file_pointer, $id) : ?Eft_User {
	$users = eft_deserialize_users($file_pointer);
	foreach($users as $user) {
		if($user->id == $id) {
			return $user;
		}
	}
	return null;
}

// Returns user record or null
function eft_get_user_by_username($username) : ?Eft_User {
	return eft_use_file_lock(DATA_FILE_USERS, 'eft_get_user_by_username_callback', $username);
}
// intended to be passed as a callback to a data_access.php function
// returns user record or null
function eft_get_user_by_username_callback($file_pointer, $username) : ?Eft_User {
	$users = eft_deserialize_users($file_pointer);
	foreach($users as $user) {
		if($user->username == $username) {
			return $user;
		}
	}
	return null;
}

// Returns nothing
function eft_update_user_with_login_session($id, $session_key) {
	$params = array("id"=>$id, "session_key"=>$session_key);
	return eft_use_file_lock(DATA_FILE_USERS, 'eft_update_user_with_login_session_callback', $params);
}
// intended to be passed as a callback to a data_access.php function
// Returns nothing
function eft_update_user_with_login_session_callback($file_pointer, $params) {
	$id = $params["id"];
	$session_key = $params["session_key"];
	
	//finding the user and updating the record are done within the same file lock
	$users = eft_deserialize_users($file_pointer);
	$found_user = false;
	foreach($users as $user) {
		if($user->id == $id) {
			$user->last_login_date = new DateTime();
			$user->session_key = $session_key;
			$found_user = true;
			break;
		}
	}
	if(!$found_user) {
		throw new Exception(MESSAGE_EDIT_USER_FAILED);
	}
	$format_version = eft_get_data_format_version($file_pointer);
	eft_persist_users($file_pointer, $format_version, $users);
}

// Overwrites the whole file with the array of users
function eft_persist_users($file_pointer, $format_version, $users) {
	rewind($file_pointer);
	fwrite($file_pointer, "#version:".$format_version);
	fwrite($file_pointer, "\n".Eft_User::serialize_headers($format_version));
	foreach($users as $user) {
		fwrite($file_pointer, "\n".$user->serialize($format_version));
	}
}

/*
 * Assumes permissions to view tasks have already been verified
 * @returns Eft_Task[]
 */
function eft_get_open_tasks() : array {
	return eft_use_file_lock(DATA_FILE_TASKS, 'eft_deserialize_tasks', $user);
}

// Assumes permissions to add a task have already been verified
// Returns id of the task
function eft_persist_new_task(Eft_Task $task) : int {
	return eft_use_file_lock(DATA_FILE_TASKS, 'eft_persist_new_task_callback', $task);
}
// intended to be passed as a callback to a data_access.php function
// returns id of the task
function eft_persist_new_task_callback($file_pointer, $new_task) : int {
	$tasks = eft_deserialize_tasks($file_pointer);
	$max_id = 0;
	foreach($tasks as $task) {
		if($task->id > $max_id) {
			$max_id = $task->id;
		}
	}
	$format_version = eft_get_data_format_version($file_pointer);
	$new_task->id = $max_id + 1;
	$new_task->created_date = new DateTime();
	$new_task_serialized = $new_task->serialize($format_version);
	fseek($file_pointer, 0, SEEK_END); //go to end of file
	fwrite($file_pointer, "\n".$new_task_serialized);
	return $new_task->id;
}

// Assumes permissions to close a task have already been verified
// Returns True for success
function eft_persist_close_task($task_id, $user_id, $closing_text) : bool {
	$params = array("id"=>$task_id, "user_id"=>$user_id, "closing_text"=>$closing_text);
	eft_use_file_lock(DATA_FILE_TASKS, 'eft_persist_close_task_callback', $params);
	return True;
}
// intended to be passed as a callback to a data_access.php function
function eft_persist_close_task_callback($file_pointer, $params) {
	//finding the task and updating the record are done within the same file lock
	$tasks = eft_deserialize_tasks($file_pointer);
	$found_task = false;
	foreach($tasks as $task) {
		if($task->id == $params['id']) {
			$task->is_closed = True;
			$task->closed_date = new DateTime();
			$task->closed_by_user_id = $params['user_id'];
			$task->closing_text = $params['closing_text'];
			$found_task = true;
			break;
		}
	}
	if(!$found_task) {
		throw new Exception(MESSAGE_EDIT_TASK_FAILED);
	}
	$format_version = eft_get_data_format_version($file_pointer);
	eft_persist_tasks($file_pointer, $format_version, $tasks);
}

// Overwrites the whole file with the array of tasks
function eft_persist_tasks($file_pointer, $format_version, $tasks) {
	rewind($file_pointer);
	fwrite($file_pointer, "#version:".$format_version);
	fwrite($file_pointer, "\n".Eft_Task::serialize_headers($format_version));
	foreach($tasks as $task) {
		fwrite($file_pointer, "\n".$task->serialize($format_version));
	}
}

?>
