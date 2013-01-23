<?php
$mysql_hostname = "localhost";
$mysql_user = "root";
$mysql_password = "rox";
$mysql_database = "inteleat";
$db = mysql_connect($mysql_hostname, $mysql_user, $mysql_password)
or die("DB connection went wrong.");
mysql_select_db($mysql_database, $db) or die("DB connection went wrong.");

?>
