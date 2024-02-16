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
$due_date = DateTime::createFromFormat('Ymd', $_POST['date']);
$text = $_POST['text'];

$result = eft_persist_edit_task($task_id, $user_id, $due_date, $text);

echo json_encode($result);

?>

