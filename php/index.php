<?php

include_once("security.php");

if(isset($_COOKIE["id"])) {
	$is_authorized = eft_verify_auth_key($_COOKIE["id"], $_COOKIE["auth_key"]);
	if($is_authorized) {
		include('page_dashboard.php');
		die();
	}
	else {
		include('page_401_unauthorized.php');
		die();
	}
}

header("Location: page_login.html");
exit;

?>