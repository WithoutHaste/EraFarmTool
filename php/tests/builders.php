<?php

include_once("../global_utils.php");
include_once("../classes.php");

function build_user() : Eft_User 
{
	$user = new Eft_User();
	$user->id = 1;
	$user->created_date = DateTime::createFromFormat('Ymd', '20230131');
	$user->is_admin = False;
	$user->username = "jack";
	$user->password_hashed = "abcde";
	$user->email = "j@gmail.com";
	$user->phone_number = "1234567890";
	$user->last_login_date = DateTime::createFromFormat('Ymd', '20230315');
	$user->session_key = new_guid();
	$user->is_deactivated = False;
	return $user;
}

function users_match(Eft_User $a, Eft_User $b) : bool
{
	if($a->id != $b->id) return false;
	if($a->created_date->format('Y-m-d') != $b->created_date->format('Y-m-d')) return false;
	if($a->is_admin != $b->is_admin) return false;
	if($a->username != $b->username) return false;
	if($a->password_hashed != $b->password_hashed) return false;
	if($a->email != $b->email) return false;
	if($a->phone_number != $b->phone_number) return false;
	if($a->last_login_date->format('Y-m-d') != $b->last_login_date->format('Y-m-d')) return false;
	if($a->session_key != $b->session_key) return false;
	if($a->is_deactivated != $b->is_deactivated) return false;
	return true;
}

function build_task() : Eft_Task
{
	$task = new Eft_Task();
	$task->id = 1;
	$task->created_by_user_id = 2;
	$task->created_date = DateTime::createFromFormat('Ymd', '20230131');
	$task->due_date = DateTime::createFromFormat('Ymd', '20230215');
	$task->text = 'llama llama';
	$task->is_closed = True;
	$task->closed_by_user_id = 3;
	$task->closed_date = DateTime::createFromFormat('Ymd', '20230216');
	$task->closing_text = 'giraffe giraffe';
	return $task;
}

function tasks_match(Eft_Task $a, Eft_Task $b) : bool
{
	if($a->id != $b->id) return false;
	if($a->created_by_user_id != $b->created_by_user_id) return false;
	if($a->created_date->format('Y-m-d') != $b->created_date->format('Y-m-d')) return false;
	if($a->due_date->format('Y-m-d') != $b->due_date->format('Y-m-d')) return false;
	if($a->text != $b->text) return false;
	if($a->is_closed != $b->is_closed) return false;
	if($a->closed_by_user_id != $b->closed_by_user_id) return false;
	if($a->closed_date->format('Y-m-d') != $b->closed_date->format('Y-m-d')) return false;
	if($a->closing_text != $b->closing_text) return false;
	return true;
}

?>