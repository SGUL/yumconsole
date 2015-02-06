<?php
$server=$_GET['server'];
$str = file_get_contents("./output/$server.details");
echo $str;
?>
