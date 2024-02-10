<?php
	/*
	* Verifies a user login and returns auth key
	*/
	
	include_once('constants.php');
	include_once('security.php');
	include_once('data_access.php');

	$username = $_POST['u'];
	$password = $_POST['p'];

	$result = array();
	try
	{
		$result = eft_login($username, $password);
	}
	catch(Exception $ex)
	{
		http_response_code(401); //unauthorized
		exit();
	}
	
	echo json_encode($result);
?>