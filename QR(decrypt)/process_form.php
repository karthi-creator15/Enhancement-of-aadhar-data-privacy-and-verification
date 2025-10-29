<?php
// Database configuration
$servername = "localhost";
$username = "root";
$password = ""; // Update with your database password
$dbname = "user_data";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Prepare and bind
$stmt = $conn->prepare("INSERT INTO user_datas (name, gender, email, aadhaar, pan,dob) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssss", $name, $gender, $email, $aadhaar, $pan, $dob);

// Set parameters and execute
$name = $_POST['name'];
$gender = $_POST['gender'];
$email = $_POST['email'];
$aadhaar = $_POST['aadhaar'];
$pan = $_POST['pan'];
$ration = $_POST['ration'];

if ($stmt->execute()) {
    // Redirect to success page
    header("Location: success.php");
    exit();
} else {
    echo "Error: " . $stmt->error;
}

// Close connections
$stmt->close();
$conn->close();
?>
