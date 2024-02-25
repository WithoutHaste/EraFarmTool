<?php

include_once('data_access.php');

$user_id = $_COOKIE["id"];
$user = eft_get_user_by_id($user_id);

?>

<hr/>

<a href='index.php'>Tasks</a>&nbsp;&nbsp;&nbsp;&nbsp;
<?php if($user->is_admin) { ?> <a href='page_users.php'>Users</a>&nbsp;&nbsp;&nbsp;&nbsp; <?php } ?>
<a href='page_your_account.php'>Your Account</a>&nbsp;&nbsp;&nbsp;&nbsp;
<hr/>
