<?php

include_once('constants.php');
include_once('classes.php');
include_once('security.php');
include_once('data_access.php');

if(isset($_COOKIE["id"])) {
	$is_authorized = eft_verify_auth_key($_COOKIE["id"], $_COOKIE["auth_key"]);
	if(!$is_authorized) {
		include('page_401_unauthorized.php');
		die();
	}
}

$user_id = $_COOKIE["id"];
$task_id = $_POST['taskId'];
$closing_text = $_POST['closingText'];

$result = eft_persist_close_task($task_id, $user_id, $closing_text);

echo json_encode($result);

?>

