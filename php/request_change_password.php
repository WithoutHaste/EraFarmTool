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

$user = eft_get_user_by_id($_COOKIE["id"]);
$current_password = $_POST["passwordCurrent"];
$password_is_valid = eft_verify_password($current_password, $user->password_hashed);
if(!$password_is_valid) {
	include('page_401_unauthorized.php');
	die();
}

$password_raw = $_POST['passwordNew'];
if($password_raw == null) {
	throw new Exception(MESSAGE_ADD_USER_REQUIRED_ARGUMENTS);
}

$password_hashed = eft_create_password_hash($password_raw);
$result = eft_update_user_with_password($_COOKIE["id"], $password_hashed);

echo json_encode($result);

?>

