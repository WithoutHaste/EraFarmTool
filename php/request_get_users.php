<?php

include_once("security.php");
include_once("data_access.php");

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

$users = eft_get_users();
usort($tasks, 'sort_users_by_type_then_name');
$display_users = array();
foreach($users as $user) {
	array_push($display_users, Eft_User_Display::from_user($user));
}

// sort by type then name, descending
function sort_tasks_by_type_then_name($a, $b) {
	if($a->is_admin != $b->is_admin) {
		if($a->is_admin) return -1;
		return 1;
	}
	if($a->username < $b->username) return -1;
	if($b->username < $a->username) return 1;
	return 0;
}

echo json_encode($display_users);

?>

