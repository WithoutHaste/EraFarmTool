<?php

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
	if($a->is_deactivated != $b->is_deactivated) return false;
	return true;
}

?>