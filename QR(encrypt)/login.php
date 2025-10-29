<?php
// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "users";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Check if username and password match
    $query = "SELECT * FROM users WHERE name = '$username' AND password = '$password'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        echo "<script>alert('Login successful'); window.location.href = 'main.html';</script>";
    } else {
        echo "<script>alert('Invalid username or password'); window.location.href = 'login.html';</script>";
    }
}

$conn->close();
?>
