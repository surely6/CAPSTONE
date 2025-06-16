<?php
date_default_timezone_set('Asia/Kuala_Lumpur');


$host = "localhost";
$username = "root";
$password = "";
$db_name = "capstone";

$conn = new mysqli($host, $username, $password, $db_name);

if (!$conn) {
    die("Connection error.");
}

?>