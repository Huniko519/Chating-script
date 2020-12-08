<?php 

session_start();

require_once('../config.php');
require_once('functions/func.admin.php');



session_unset($_SESSION['admin']['id']);
session_unset($_SESSION['admin']['username']);

echo '<script>window.location="login.php"</script>';



?>

