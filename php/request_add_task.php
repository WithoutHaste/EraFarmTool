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
$due_date = $_POST['date'];
$text = $_POST['text'];

$task = new Eft_Task();
$task->created_by_user_id = $user_id;
$task->due_date = DateTime::createFromFormat('Ymd', $due_date);
$task->text = $text;

$result = eft_persist_new_task($task);

echo json_encode($result);

?>

