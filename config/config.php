<?php
$host = 'localhost'; // Database host
$db_name = 'fchat'; // Database name
$username = 'root'; // Database username
$password = ''; // Database password

// Create a connection
$conn = new mysqli($host, $username, $password, $db_name);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to utf8
$conn->set_charset("utf8");

// Other configuration constants can be defined here
define('APP_NAME', 'PHP Chat Application');
define('APP_VERSION', '1.0.0');
?>