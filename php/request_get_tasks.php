<?php

include_once("security.php");

if(isset($_COOKIE["id"])) {
	$is_authorized = eft_verify_auth_key($_COOKIE["id"], $_COOKIE["auth_key"]);
	if(!$is_authorized) {
		include('page_401_unauthorized.php');
		die();
	}
}

$tasks = eft_get_tasks();
usort($tasks, 'sort_tasks_by_due_date');

// sort by due date, ascending
// with 'null' considered the earliest date
function sort_tasks_by_due_date($a, $b) {
	if($a->due_date == null) return -1;
	if($b->due_date == null) return 1;
	if($a->due_date < $b->due_date) return -1;
	if($b->due_date < $a->due_date) return 1;
	return 0;
}

echo json_encode($tasks);

?>

