<?php
require 'vendor/autoload.php'; // Ensure PHPMailer is loaded via Composer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Handle forgot IV functionality

    // Database connection
    $host = 'localhost'; // Change this to your host
    $username = 'root'; // Change this to your DB username
    $password = ''; // Change this to your DB password
    $dbname = 'users'; // Change this to your database name
    $conn = new mysqli($host, $username, $password, $dbname);

    if ($conn->connect_error) {
        die('Database connection failed: ' . $conn->connect_error);
    }

    // Get the Aadhaar number entered by the user
    $aadhaar = $_POST['aadhaar']; // Aadhaar number entered by the user

    // Ensure Aadhaar is provided
    if (empty($aadhaar)) {
        die('Please provide your Aadhaar number.');
    }

    // Search the database for Aadhaar and fetch associated Gmail and IV
    $query = "SELECT email, iv FROM user_ddocr WHERE aadhaar = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s', $aadhaar);
    $stmt->execute();
    $stmt->bind_result($email, $iv);
    $stmt->fetch();
    $stmt->close();
    $conn->close();

    if (!$email || !$iv) {
        die('No user found with this Aadhaar number.');
    }

    // Send the IV via email
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Replace with your SMTP host
        $mail->SMTPAuth = true;
        $mail->Username = 'topjrealworld@gmail.com'; // Replace with your email
        $mail->Password = 'twyapmxcoqaugebz'; // Replace with your email password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('topjrealworld@gmail.com', 'IV Recovery Service');
        $mail->addAddress($email);
        $mail->Subject = 'Your IV Code';
        $mail->Body = "Dear User,\n\nYour IV Code is: $iv\n\nBest Regards,\nIV Recovery Team";

        $mail->send();
        echo 'IV code has been sent successfully to your email.';
    } catch (Exception $e) {
        echo "Email could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot IV</title>
    <style>
        /* Styles omitted for brevity */
    </style>
</head>
<body>
    <h1>Forgot IV</h1>
    <div class="container">
        <form method="POST" action="">
            <label for="aadhaar">Enter your Aadhaar Number:</label>
            <input type="text" id="aadhaar" name="aadhaar" required>

            <button type="submit">Send IV to Email</button>
        </form>
    </div>
</body>
</html>
