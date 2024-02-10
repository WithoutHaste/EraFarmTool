<?php

include_once("data_access.php");

http_response_code(200);

?>

<!DOCTYPE HTML>
<html lang="en">
    <head>
        <meta http-equiv="content-type" content="text/html; charset=utf-8">
        <title>Dashboard - Era Farm Tool</title>
		<script type="text/javascript" src="js/utils.js"></script>
		<script type="text/javascript" src="js/attribution.js"></script>
		<link rel="stylesheet" type="text/css" href="css/default.css" />
    </head>
    <body>
		<h2>Era Farm Tool</h2>

		<table>
			<tr><td>New Task</td></tr>
			<tr><td>Days Left: </td><td><input name='days' type='number' /></td></tr>
			<tr><td>Text: </td><td><textarea name='text' cols='50' rows='5' maxlength='500'></textarea></td></tr>
		</table>
		<button onclick='addTask()'>Add</button>
		<div id='container_errors' class='error'>
		</div>

		<hr/>

		<b>Open Tasks</b>
		<table class='data' id='tasks-table'>
		</table>
    </body>
</html>

<script type='text/javascript'>

window.addEventListener('load', getTasks);

function getTasks() {
	ajaxGet('request_get_tasks.php', getTasksSuccess, handleError);
}

function getTasksSuccess(message) {
	const tasks = JSON.parse(message);
	console.log(tasks);

	const table = document.getElementById('tasks-table');
	table.innerHTML = null;
	
	const headerRow = document.createElement('tr');
	headerRow.appendChild(createElementWithText('th', 'Days Left'));
	headerRow.appendChild(createElementWithText('th', 'Due Date'));
	headerRow.appendChild(createElementWithText('th', 'Text'));
	table.appendChild(headerRow);
	
	for(let task of tasks) {
		const row = document.createElement('tr');
		row.appendChild(createElementWithText('td', calcDaysLeft(new Date(task.due_date.date))));
		row.appendChild(createElementWithText('td', formatDateForDisplay(new Date(task.due_date.date))));
		row.appendChild(createElementWithText('td', htmlEncodeText(task.text)));
		table.appendChild(row);
	}
	
	function calcDaysLeft(date) {
		return parseInt((date - new Date()) / (1000 * 60 * 60 * 24));
	}
	
	function formatDateForDisplay(date) {
		return `${date.getYear()+1900}-${date.getMonth()+1}-${date.getDate()}`;
	}
	
	function htmlEncodeText(text) {
		//TODO script injection protection, html injection protection
		return text.replace('\n', '<br/>');
	}
}

function addTask() {
	let params = [];
	params['date'] = convertDaysToFormattedDate();
	params['text'] = getTextInput().value;
	ajaxPost('request_add_task.php', params, addTaskSuccess, handleError);
	
	//convert Days to YYYYmmdd DueDate
	function convertDaysToFormattedDate() {
		let days = parseInt(getDaysInput().value);
		let now = new Date();
		let dueDate = new Date();
		dueDate.setDate(now.getDate() + days);
		return `${dueDate.getYear()+1900}${(dueDate.getMonth()+1).toString().padStart(2,'0')}${dueDate.getDate().toString().padStart(2,'0')}`;
	}
}

function addTaskSuccess(message) {
	console.log(message);

	getDaysInput().value = '';
	getTextInput().value = '';

	getDaysInput().focus();
	
	getTasks();
}

function getDaysInput() {
	return document.getElementsByName('days')[0];
}

function getTextInput() {
	return document.getElementsByName('text')[0];
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
