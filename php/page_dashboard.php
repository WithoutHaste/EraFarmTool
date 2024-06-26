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
		
		<?php include_once("page_navigation_bar.php"); ?>

		<table>
			<tr><td>New Task</td></tr>
			<tr><td>Days Left: </td><td><input name='days' type='number' /></td></tr>
			<tr><td>Text: </td><td><textarea name='text' cols='50' rows='5' maxlength='500'></textarea></td></tr>
			<tr><td></td><td style='text-align:right;'><button onclick='addTask()'>Add</button></td></tr>
		</table>
		<div id='container_errors' class='error'>
		</div>

		<hr/>

		<table>
			<tr>
				<td>
					<b>Open Tasks</b>
					<table class='data' id='open-tasks-table'>
					</table>
				</td>
				<td style='padding-left: 2em;'>
					<b>Closed Tasks</b>
					<table class='data' id='closed-tasks-table'>
					</table>
				</td>
			</tr>
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

	const openTable = document.getElementById('open-tasks-table');
	const closedTable = document.getElementById('closed-tasks-table');
	openTable.innerHTML = null;
	closedTable.innerHTML = null;
	
	const openHeaderRow = document.createElement('tr');
	openHeaderRow.appendChild(createElementWithText('th', 'Days Left'));
	openHeaderRow.appendChild(createElementWithText('th', 'Due Date'));
	openHeaderRow.appendChild(createElementWithText('th', 'Text'));
	openHeaderRow.appendChild(createElementWithText('th', ''));
	openHeaderRow.appendChild(createElementWithText('th', ''));
	openTable.appendChild(openHeaderRow);
	
	const closedHeaderRow = document.createElement('tr');
	closedHeaderRow.appendChild(createElementWithText('th', 'Closed Date'));
	closedHeaderRow.appendChild(createElementWithText('th', 'Text'));
	closedHeaderRow.appendChild(createElementWithText('th', 'Closing Text'));
	closedTable.appendChild(closedHeaderRow);
	
	for(let task of tasks) {
		if(task.is_closed) {
			const row = document.createElement('tr');
			row.classList.add('highlight');
			row.dataset.taskId = task.id;
			row.appendChild(createElementWithText('td', formatDateForDisplay(new Date(task.closed_date.date))));
			row.appendChild(createElementWithText('td', htmlEncodeText(task.text)));
			row.appendChild(createElementWithText('td', htmlEncodeText(task.closing_text)));
			closedTable.appendChild(row);
		}
		else {
			const row = document.createElement('tr');
			row.classList.add('highlight');
			row.dataset.taskId = task.id;
			row.appendChild(createElementWithText('td', calcDaysLeft(new Date(task.due_date.date))));
			row.appendChild(createElementWithText('td', formatDateForDisplay(new Date(task.due_date.date))));
			row.appendChild(createElementWithText('td', htmlEncodeText(task.text)));

			const editContainer = buildEditContainer(task);
			const editCell = document.createElement('td');
			editCell.appendChild(editContainer);
			row.appendChild(editCell);

			const closeContainer = buildCloseContainer(task.id);
			const closeCell = document.createElement('td');
			closeCell.appendChild(closeContainer);
			row.appendChild(closeCell);
			
			openTable.appendChild(row);
		}
	}
	
	function calcDaysLeft(date) {
		return parseInt((date - new Date()) / (1000 * 60 * 60 * 24)) + 1;
	}
	
	function formatDateForDisplay(date) {
		return `${date.getYear()+1900}-${date.getMonth()+1}-${date.getDate()}`;
	}
	
	function buildEditContainer(task) {
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
	}
}

function addTask() {
	let params = [];
	params['date'] = convertDaysToFormattedDate(getDaysInput().value);
	params['text'] = getTextInput().value;
	ajaxPost('request_add_task.php', params, addTaskSuccess, handleError);
	
}

//convert Days to YYYYmmdd DueDate
function convertDaysToFormattedDate(daysLeft) {
	let days = parseInt(daysLeft);
	let now = new Date();
	let dueDate = new Date();
	dueDate.setDate(now.getDate() + days);
	return `${dueDate.getYear()+1900}${(dueDate.getMonth()+1).toString().padStart(2,'0')}${dueDate.getDate().toString().padStart(2,'0')}`;
}

function addTaskSuccess(message) {
	getDaysInput().value = '';
	getTextInput().value = '';

	getDaysInput().focus();
	
	getTasks();
}

function editTaskSuccess(message) {
	getTasks();
}

function closeTaskSuccess(message) {
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
