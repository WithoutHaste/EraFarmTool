<?php

include_once("security.php");

if(isset($_COOKIE["id"])) {
	$is_authorized = eft_verify_auth_key($_COOKIE["id"], $_COOKIE["auth_key"]);
	if(!$is_authorized) {
		include('page_401_unauthorized.php');
		die();
	}
}

$plants = eft_get_plants();
usort($plants, 'sort_plants_by_categories');

// sort by categories, ascending
// with 'null' considered the earliest
function sort_plants_by_categories($a, $b) {
	if($a->categories == null || $a->categories == "") return -1;
	if($b->categories == null || $b->categories == "") return 1;
	if($a->categories < $b->categories) return -1;
	if($b->categories < $a->categories) return 1;
	return 0;
}

echo json_encode($plants);

?>

