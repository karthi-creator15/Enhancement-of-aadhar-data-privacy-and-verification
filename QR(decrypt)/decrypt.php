<?php 
require 'vendor/autoload.php'; // Ensure PHPMailer is loaded via Composer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$decryptedData = null;
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['decrypt'])) {
        // Collect user input
        $encryptedData = $_POST['encryptedData'];
        $dob = $_POST['dob']; // Only DOB is provided for decryption
        $iv = $_POST['iv']; // 6-digit IV provided by the user (can be obtained via QR code or input)

        // Ensure the user input is not empty
        if (empty($encryptedData) || empty($dob) || empty($iv)) {
            die('Please provide the encrypted data, Date of Birth (DOB), and the 6-digit IV.');
        }

        // Ensure IV is exactly 6 digits long
        if (strlen($iv) !== 6 || !ctype_digit($iv)) {
            die('The IV must be exactly 6 digits long and numeric.');
        }

        // Define the cipher method
        $cipher = 'aes-256-cbc';

        // Derive the key from the provided DOB (same logic as encryption)
        $key = substr(hash('sha256', strrev($dob)), 0, 32); // 256-bit key derived from the reversed DOB

        // Convert hex input to binary
        $encryptedData = hex2bin($encryptedData);

        // Ensure the conversion was successful
        if ($encryptedData === false) {
            die('Conversion from hex to binary failed.');
        }

        // Decrypt the data using the provided 6-digit IV
        $decryptedData = openssl_decrypt($encryptedData, $cipher, $key, 0, $iv);
        if ($decryptedData === false) {
            $decryptedData = 'Decryption failed or result is empty.';
        }

        // Redirect to result page with decrypted data
        header('Location: result.php?data=' . urlencode($decryptedData));
        exit();
    } elseif (isset($_POST['forgot_iv'])) {
        // Handle "Forgot IV" functionality
        sendIvEmail();
    }
}

function sendIvEmail() {
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
    <title>Decryption</title>
    <style>
        html, body {
            height: 100%;
            margin: 0;
            font-family: Arial, sans-serif;
            background-image: url('photo.jpg');
            background-repeat: no-repeat;
            background-position: center;
            background-size: cover;
            color: #333;
            display: flex;
            flex-direction: column;
        }

        h1 {
            text-align: center;
            color: #333;
            margin-top: 20px;
        }

        .container {
            width: 80%;
            margin: 20px auto;
            max-width: 600px;
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease-in-out;
        }

        .container:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            margin-bottom: 5px;
        }

        input, textarea, button {
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        button {
            background-color: #007BFF;
            color: #fff;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
        }

        button:hover {
            background-color: #0056b3;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        #reader {
            width: 100%;
            max-width: 600px;
            margin: 20px auto;
            text-align: center;
        }

        #reader div {
            display: inline-block;
        }
    </style>
    <script src="html5-qrcode.min.js"></script>
</head>
<body>
    <h1>Decrypt Data</h1>
    <div id="reader"></div>

    <div class="container">
        <form method="POST" action="">
            <label for="encryptedData">Encrypted Data (Hex Encoded):</label>
            <textarea id="encryptedData" name="encryptedData" rows="4" required></textarea>

            <label for="dob">Enter your Date of Birth (DOB) as the Key:</label>
            <input type="text" id="dob" name="dob" required>

            <label for="iv">Enter the 6-Digit IV:</label>
            <input type="text" id="iv" name="iv" required>

            <button type="submit" name="decrypt">Decrypt</button>
        </form>

        <h1>Forgot IV?</h1>
        <form method="POST" action="">
            <label for="aadhaar">Enter your Aadhaar Number:</label>
            <input type="text" id="aadhaar" name="aadhaar" required>

            <button type="submit" name="forgot_iv">Send IV to Email</button>
        </form>
    </div>

        <script>
        function onScanSuccess(decodedText, decodedResult) {
            // Set the scanned QR code data in the encryptedData field
            document.getElementById('encryptedData').value = decodedText;
        }

        function onScanError(errorMessage) {
            // Hide error messages from the console
            console.log('QR code parse error:', errorMessage);
        }

        // Initialize the QR code scanner
        let html5QrcodeScanner = new Html5QrcodeScanner(
            "reader", 
            { fps: 10, qrbox: 250 },
            /* verbose= */ false);
        html5QrcodeScanner.render(onScanSuccess, onScanError);
    </script>
</body>
</html>