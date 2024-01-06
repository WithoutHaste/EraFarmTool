<?php

/*
This file is for commands that should only be accessible from the server, not from the web.
There are other ways to ensure this that require changes to file locations or file permissions.
This solution was selected in the interest of keeping this project really easy to setup on a new server.
*/
if(php_sapi_name() != "cli") {
	include('404_not_found.php');
	die();
}

include("command_line_utils.php");

$message = eft_handle_command($argv);
echo $message."\n";

?>