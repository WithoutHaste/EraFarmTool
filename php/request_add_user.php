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
	$user = eft_get_user_by_id($_COOKIE["id"]);
	if(!$user->is_admin) {
		include('page_401_unauthorized.php');
		die();
	}
}

$user = new Eft_User();
$user->username = $_POST['username'];
$user->is_admin = $_POST['is_admin'];
$user->email = $_POST['email'];
$user->phone_number = $_POST['phone_number'];
$password_raw = $_POST['password'];

if($user->username == null || $password_raw == null) {
	throw new Exception(MESSAGE_ADD_USER_REQUIRED_ARGUMENTS);
}

$user->password_hashed = eft_create_password_hash($password_raw);

$result = eft_persist_new_user($user);

echo json_encode($result);

?>

