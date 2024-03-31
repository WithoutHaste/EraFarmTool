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

$name = $_POST['date'];
$text = $_POST['text'];

$plant = new Eft_Plant();
$plant->name = $_POST['name'];
$plant->categories = $_POST['categories'];
$plant->notes = $_POST['notes'];

$result = eft_persist_new_plant($plant);

echo json_encode($result);

?>

