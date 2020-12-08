<?php
/**
 * Created by PhpStorm.
 * User: yogi
 * Date: 16-09-2016
 * Time: 19:35
 */
session_start();
// Create connection in MYsqli
$con = new mysqli($config['db']['host'], $config['db']['user'], $config['db']['pass'], $config['db']['name']);
// Check connection in MYsqli
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}

?>