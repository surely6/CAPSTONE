<?php
// Database configuration
$host = "localhost";     
$username = "root";          
$password = '';              
$dbname = "capstone"; 

// Create connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set character set to utf8mb4
if (!$conn->set_charset("utf8mb4")) {
    printf("Error loading character set utf8mb4: %s\n", $conn->error);
    exit();
}


function escape_input($data) {
    global $conn;
    return $conn->real_escape_string($data);
}


function close_connection() {
    global $conn;
    $conn->close();
}

// Optional: Set timezone for date/time functions
date_default_timezone_set('Asia/Kuala_Lumpur'); // Change to your timezone
?>