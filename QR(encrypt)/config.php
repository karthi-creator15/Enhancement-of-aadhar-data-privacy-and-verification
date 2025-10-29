<?php
// Database connection details
$servername = "localhost";
$username = "root"; // Use your MySQL username
$password = ""; // Use your MySQL password
$dbname = "users"; // Replace with your actual database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
