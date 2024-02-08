<?php

include_once("data_access.php");

http_response_code(200);

$tasks = eft_get_open_tasks();
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

$now = new DateTime();

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
		<button onclick='saveTask()'>Save</button>
		<div id='container_errors'>
		</div>

		<hr/>

		<b>Open Tasks</b>
		<table class='data'>
			<tr><th>Days Left</th><th>Due Date</th><th>Text</th></tr>
			<?php foreach($tasks as $task) { ?>
				<tr data-id='<?php echo $task->id; ?>'>
					<td><?php echo $now->diff($task->due_date)->format("%a"); ?></td>
					<td><?php echo $task->due_date->format("YYYY-mm-dd"); ?></td>
					<td><?php echo htmlentities(str_replace("\n", "<br/>", $task->text)); ?></td>
				</tr>
			<?php } ?>
		</table>
    </body>
</html>
