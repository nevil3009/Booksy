<?php
// Database configuration
$host = "localhost";
$user = "YOUR_DB_USER";
$pass = "YOUR_DB_PASSWORD";
$dbname = "YOUR_DB_NAME";

// Create connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>