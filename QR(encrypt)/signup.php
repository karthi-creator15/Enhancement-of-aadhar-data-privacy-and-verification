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
    $name = $_POST['name'];
    $password = $_POST['password'];

    $query = "INSERT INTO users (name, password) VALUES ('$name','$password')";
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Registration successful'); window.location.href = 'login.html';</script>";
    } else {
        echo "<script>alert('Registration failed'); window.location.href = 'signup.php';</script>";
    }
}

$conn->close();
?>
