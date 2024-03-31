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
        <title>Plants - Era Farm Tool</title>
		<script type="text/javascript" src="js/utils.js"></script>
		<script type="text/javascript" src="js/attribution.js"></script>
		<link rel="stylesheet" type="text/css" href="css/default.css" />
    </head>
    <body>
		<h2>Era Farm Tool</h2>
		
		<?php include_once("page_navigation_bar.php"); ?>

		<table>
			<tr><td>New Plant</td></tr>
			<tr><td>Name: </td><td><input name='name' /> (Specific Variety)</td></tr>
			<tr><td>Categories: </td><td>
				<textarea name='categories' cols='30' rows='5' maxlength='200'></textarea><br/>
				(Type one category per line)
			</td></tr>
			<tr><td>Notes: </td><td><textarea name='notes' cols='50' rows='5' maxlength='500'></textarea></td></tr>
			<tr><td></td><td style='text-align:right;'><button onclick='addPlant()'>Add</button></td></tr>
		</table>
		<div id='container_errors' class='error'>
		</div>

		<hr/>

		<table>
			<tr>
				<td>
					<b>Plants</b>
					<table class='data' id='plants-table'>
					</table>
				</td>
			</tr>
		</table>
    </body>
</html>

<script type='text/javascript'>

window.addEventListener('load', getPlants);

function getPlants() {
	ajaxGet('request_get_plants.php', getPlantsSuccess, handleError);
}

function getPlantsSuccess(message) {
	const plants = JSON.parse(message);

	const table = document.getElementById('plants-table');
	table.innerHTML = null;
	
	const headerRow = document.createElement('tr');
	headerRow.appendChild(createElementWithText('th', 'Name'));
	headerRow.appendChild(createElementWithText('th', 'Categories'));
	headerRow.appendChild(createElementWithText('th', 'Notes'));
	table.appendChild(headerRow);

	for(let plant of plants) {
		const row = document.createElement('tr');
		row.classList.add('highlight');
		row.dataset.plantId = plant.id;
		row.appendChild(createElementWithText('td', plant.name));
		row.appendChild(createElementWithText('td', plant.categories));
		row.appendChild(createElementWithText('td', htmlEncodeText(plant.notes)));

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

function addPlant() {
	let params = [];
	params['name'] = getNameInput().value;
	params['categories'] = getCategoriesInput().value.replace('\n', ', ');
	params['notes'] = getNotesInput().value;

	ajaxPost('request_add_plant.php', params, addPlantSuccess, handleError);
}

function addPlantSuccess(message) {
	getNameInput().value = '';
	getCategoriesInput().value = '';
	getNotesInput().value = '';

	getNameInput().focus();
	
	getPlants();
}

function editPlantSuccess(message) {
	getPlants();
}

function getNameInput() {
	return document.getElementsByName('name')[0];
}

function getCategoriesInput() {
	return document.getElementsByName('categories')[0];
}

function getNotesInput() {
	return document.getElementsByName('notes')[0];
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


</script>
