<?php
	ini_set("display_errors","1");
	ERROR_REPORTING(E_ALL);
	require_once 'od_utils.php';
	session_start();

if (!isset($_GET['usr']) && !isset($_GET['code']) )
{
    $msg = "ERROR: Invalid code...";
    exit();
}
db_connect("CCDB");
$row = db_fetch_one("CCDB", "SELECT activation_code from sessions where user_email='$_GET[usr]'") or die('Error');

if ($row && $_GET['code'] == $row['activation_code'])
{
    db_exec("CCDB", "update sessions set user_activated=1 where user_email='$_GET[usr]'") or die('Error updating session');
    echo "<h3>Thank you </h3>Email confirmed and account activated. You can <a href=\"login.php\">login</a> now..";
} else { 
    echo "ERROR: Incorrect activation code...not valid"; 
}

db_close("CCDB");
?>

