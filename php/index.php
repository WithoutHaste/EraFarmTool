<?php

include_once("security.php");

if(isset($_COOKIE["id"])) {
	$is_authorized = eft_verify_auth_key($_COOKIE["id"], $_COOKIE["auth_key"]);
	if($is_authorized) {
		echo "Authorization successful. Page under construction.";;
	}
	else {
		echo "Invalid authorization. Return to <a href='page_login.html'></a> to login again.";
	}
	exit;
}

header("Location: page_login.html");
exit;

?>