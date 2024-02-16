<?php

include_once("data_access.php");
include_once("security.php");

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

http_response_code(200);

?>

<!DOCTYPE HTML>
<html lang="en">
    <head>
        <meta http-equiv="content-type" content="text/html; charset=utf-8">
        <title>Users - Era Farm Tool</title>
		<script type="text/javascript" src="js/utils.js"></script>
		<script type="text/javascript" src="js/attribution.js"></script>
		<link rel="stylesheet" type="text/css" href="css/default.css" />
    </head>
    <body>
		<h2>Era Farm Tool</h2>
		
		<?php include_once("page_navigation_bar.php"); ?>

		<table>
			<tr><td>New User</td></tr>
			<tr><td>Username: </td><td><input name='username' /></td></tr>
			<tr><td>Password: </td><td><input name='password' /></td></tr>
			<tr><td>Is Admin:</td><td>No: <input type='radio' name='isAdmin' value='0' /> Yes: <input type='radio' name='isAdmin' value='1' /></td></tr>
			<tr><td>Email: </td><td><input name='email' /></td></tr>
			<tr><td>Phone Number: </td><td><input name='phoneNumber' /></td></tr>
			<tr><td></td><td style='text-align:right;'><button onclick='addUser()'>Add</button></td></tr>
		</table>
		<div id='container_errors' class='error'>
		</div>

		<hr/>

		<table>
			<tr>
				<td>
					<b>Users</b>
					<table class='data' id='users-table'>
					</table>
				</td>
			</tr>
		</table>
    </body>
</html>

<script type='text/javascript'>

window.addEventListener('load', getUsers);

function getUsers() {
	ajaxGet('request_get_users.php', getUsersSuccess, handleError);
}

function getUsersSuccess(message) {
	const users = JSON.parse(message);

	const table = document.getElementById('users-table');
	table.innerHTML = null;
	
	const headerRow = document.createElement('tr');
	headerRow.appendChild(createElementWithText('th', 'Username'));
	headerRow.appendChild(createElementWithText('th', 'Is Admin'));
	headerRow.appendChild(createElementWithText('th', 'Email'));
	headerRow.appendChild(createElementWithText('th', 'Phone Number'));
	headerRow.appendChild(createElementWithText('th', ''));
	headerRow.appendChild(createElementWithText('th', ''));
	table.appendChild(headerRow);

	for(let user of users) {
		const row = document.createElement('tr');
		row.classList.add('highlight');
		row.dataset.userId = user.id;
		row.appendChild(createElementWithText('td', user.username));
		row.appendChild(createElementWithText('td', convertBoolToCheckmark(user.is_admin)));
		row.appendChild(createElementWithText('td', user.email));
		row.appendChild(createElementWithText('td', user.phone_number));

/*		const editContainer = buildEditContainer(task);
		const editCell = document.createElement('td');
		editCell.appendChild(editContainer);
		row.appendChild(editCell);

		const closeContainer = buildCloseContainer(task.id);
		const closeCell = document.createElement('td');
		closeCell.appendChild(closeContainer);
		row.appendChild(closeCell);
*/		
		table.appendChild(row);
	}
	
	function convertBoolToCheckmark(value) {
		if(value) {
			return '✓';
		}
		return '';
	}
	
/*	function buildEditContainer(task) {
		const container = document.createElement('span');
		container.dataset.taskId = task.id;
		container.dataset.daysLeft = calcDaysLeft(new Date(task.due_date.date));
		container.dataset.text = task.text;
		const button = createElementWithText('button', 'Edit');
		button.addEventListener('click', clickEditButton);
		container.appendChild(button);
		return container;
	}
	
	function buildCloseContainer(taskId) {
		const container = document.createElement('span');
		container.dataset.taskId = taskId;
		const button = createElementWithText('button', 'Close');
		button.addEventListener('click', clickCloseButton);
		container.appendChild(button);
		return container;
	}
	
	function clickEditButton(event) {
		const container = event.target.parentElement;
		if(container.tagName != 'SPAN') {
			console.log("Error: cannot find edit container");
			return;
		}
		const taskId = container.dataset.taskId;
		const daysLeft = container.dataset.daysLeft;
		const text = container.dataset.text;
		
		container.innerHTML = '';
		const labelA = createElementWithText('span', 'Days Left: ');
		container.appendChild(labelA);
		const daysLeftInput = document.createElement('input');
		daysLeftInput.type = 'number';
		daysLeftInput.value = daysLeft;
		container.appendChild(daysLeftInput);
		container.appendChild(document.createElement('br'));
		const labelB = createElementWithText('span', 'Text: ');
		container.appendChild(labelB);
		container.appendChild(document.createElement('br'));
		const textArea = document.createElement('textarea');
		textArea.cols = '30';
		textArea.rows = '3';
		textArea.maxlength = '500';
		textArea.value = text;
		container.appendChild(textArea);
		textArea.focus();
		container.appendChild(document.createElement('br'));
		const button = createElementWithText('button', 'Confirm Edit');
		button.dataset.taskId = taskId;
		button.addEventListener('click', clickConfirmEditButton);
		container.appendChild(button);
		
		function clickConfirmEditButton(event) {
			const button = event.target;
			const input = button.parentElement.getElementsByTagName('input')[0];
			const textArea = button.parentElement.getElementsByTagName('textarea')[0];
			const taskId = button.dataset.taskId;

			let params = [];
			params['taskId'] = taskId;
			params['date'] = convertDaysToFormattedDate(input.value);
			params['text'] = textArea.value;
			ajaxPost('request_edit_task.php', params, editTaskSuccess, handleError);
		}
	}
	
	function clickCloseButton(event) {
		const container = event.target.parentElement;
		if(container.tagName != 'SPAN') {
			console.log("Error: cannot find close container");
			return;
		}
		const taskId = container.dataset.taskId;
		
		container.innerHTML = '';
		const label = createElementWithText('span', 'Closing Text');
		container.appendChild(label);
		container.appendChild(document.createElement('br'));
		const textArea = document.createElement('textarea');
		textArea.cols = '30';
		textArea.rows = '3';
		textArea.maxlength = '100';
		container.appendChild(textArea);
		textArea.focus();
		container.appendChild(document.createElement('br'));
		const button = createElementWithText('button', 'Confirm Close');
		button.dataset.taskId = taskId;
		button.addEventListener('click', clickConfirmCloseButton);
		container.appendChild(button);
		
		function clickConfirmCloseButton(event) {
			const button = event.target;
			const textArea = button.parentElement.getElementsByTagName('textarea')[0];
			const taskId = button.dataset.taskId;

			let params = [];
			params['taskId'] = taskId;
			params['closingText'] = textArea.value;
			ajaxPost('request_close_task.php', params, closeTaskSuccess, handleError);
		}
	}*/
}

function addUser() {
	let params = [];
	params['username'] = getUsernameInput().value;
	params['password'] = getPasswordInput().value;
	params['is_admin'] = getIsAdminInput().value;
	params['email'] = getEmailInput().value;
	params['phone_number'] = getPhoneNumberInput().value;
	ajaxPost('request_add_user.php', params, addUserSuccess, handleError);
}

function addUserSuccess(message) {
	getUsernameInput().value = '';
	getPasswordInput().value = '';
	getEmailInput().value = '';
	getPhoneNumberInput().value = '';

	getUsernameInput().focus();
	
	getUsers();
}

function editUserSuccess(message) {
	getUsers();
}

/*function closeTaskSuccess(message) {
	getTasks();
}*/

function getUsernameInput() {
	return document.getElementsByName('username')[0];
}

function getPasswordInput() {
	return document.getElementsByName('password')[0];
}

function getIsAdminInput() {
	return document.getElementsByName('isAdmin')[0];
}

function getEmailInput() {
	return document.getElementsByName('email')[0];
}

function getPhoneNumberInput() {
	return document.getElementsByName('phoneNumber')[0];
}

function handleError(xhr) {
	const container = document.getElementById('container_errors');
	let element = document.createElement('div');
	element.innerHTML = 'Error: operation failed at '+formattedTimestamp()+'.';
	container.prepend(element);

	console.log('error');
	console.log(xhr);
}


</script>
