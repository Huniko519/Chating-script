<?php
require_once('config.php');
if(isset($_GET['uname'])){
    $uname = $_GET['uname'];

    $fname = "Chat with ".$uname.".txt";
    $filename = "Chat with ".$uname.".txt";
    $data = $_GET['content'];
    $handle = fopen($filename, 'w') or die('Cannot open file:  '.$filename);
    $data = str_replace('\r\n', PHP_EOL, $data);
    fwrite($handle, $data);
    fclose($handle);

    header("Cache-Control: public");
    header("Content-Description: File Transfer");
    header("Content-Length: ". filesize("$filename").";");
    header("Content-Disposition: attachment; filename=$filename");
    header("Content-Type: application/octet-stream; ");
    header("Content-Transfer-Encoding: binary");

    readfile($filename);

    unlink($filename);
}
else{
    exit();
}


?>