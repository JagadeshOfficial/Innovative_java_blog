<?php
require 'vendor/autoload.php';

$servername = "localhost";  // Database host
$username = "root";         // Database username
$password = "12345";             // Database password
$dbname = "blog";  // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
