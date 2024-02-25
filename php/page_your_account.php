<?php

include_once("data_access.php");
include_once("security.php");

if(isset($_COOKIE["id"])) {
	$is_authorized = eft_verify_auth_key($_COOKIE["id"], $_COOKIE["auth_key"]);
	if(!$is_authorized) {
		include('page_401_unauthorized.php');
		die();
	}
}

http_response_code(200);

?>

<!DOCTYPE HTML>
<html lang="en">
    <head>
        <meta http-equiv="content-type" content="text/html; charset=utf-8">
        <title>Your Account - Era Farm Tool</title>
		<script type="text/javascript" src="js/utils.js"></script>
		<script type="text/javascript" src="js/attribution.js"></script>
		<link rel="stylesheet" type="text/css" href="css/default.css" />
    </head>
    <body>
		<h2>Era Farm Tool</h2>
		
		<?php include_once("page_navigation_bar.php"); ?>

		<table>
			<tr><td>Change Your Password</td></tr>
			<tr><td>Current Password: </td><td><input name='passwordCurrent' type='password' /></td></tr>
			<tr><td>New Password: </td><td><input name='passwordNew' type='password' /></td></tr>
			<tr><td>New Password Again: </td><td><input name='passwordNewAgain' type='password' /></td></tr>
			<tr><td></td><td style='text-align:right;'><button onclick='changePassword()'>Save</button></td></tr>
		</table>
		<div id='container_successes' class='success'>
		</div>
		<div id='container_errors' class='error'>
		</div>

    </body>
</html>

<script type='text/javascript'>

function changePassword() {
	if(getPasswordCurrentInput().value == getPasswordNewInput().value) {
		displayError('new password must differ from current password');
		return;
	}
	if(getPasswordNewInput().value != getPasswordNewAgainInput().value) {
		displayError('new passwords do not match');
		return;
	}
	
	let params = [];
	params['passwordCurrent'] = getPasswordCurrentInput().value;
	params['passwordNew'] = getPasswordNewInput().value;

	ajaxPost('request_change_password.php', params, changePasswordSuccess, handleError);
}

function changePasswordSuccess(message) {
	getPasswordCurrentInput().value = '';
	getPasswordNewInput().value = '';
	getPasswordNewAgainInput().value = '';
	
	displaySuccess('password changed');
}

function getPasswordCurrentInput() {
	return document.getElementsByName('passwordCurrent')[0];
}

function getPasswordNewInput() {
	return document.getElementsByName('passwordNew')[0];
}

function getPasswordNewAgainInput() {
	return document.getElementsByName('passwordNewAgain')[0];
}

function handleError(xhr) {
	displayError('operation failed at '+formattedTimestamp()+'.');

	console.log('error');
	console.log(xhr);
}

function displayError(message) {
	const container = document.getElementById('container_errors');
	let element = document.createElement('div');
	element.innerHTML = 'Error: ' + message;
	container.prepend(element);
}

function displaySuccess(message) {
	const container = document.getElementById('container_successes');
	let element = document.createElement('div');
	element.innerHTML = 'Success: ' + message;
	container.prepend(element);
}


</script>
