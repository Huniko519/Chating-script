<?php
require_once('config.php');
require_once('includes/dbcon.php');
require_once('includes/setting.php');
error_reporting(E_ALL);
ini_set('display_errors', 0);
session_unset($GLOBALS['sesId']);

echo '<script>window.location="login.php"</script>';



?>

