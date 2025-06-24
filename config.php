<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = 'localhost'; // Database host
$dbname = 'jotechdb'; // Database name
$username = 'root'; // Database username
$password = ''; // Database password

$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to utf8mb4 for proper encoding
$conn->set_charset("utf8mb4");
?>