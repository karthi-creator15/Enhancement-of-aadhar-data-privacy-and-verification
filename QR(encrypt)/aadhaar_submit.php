<?php
// Include the phpqrcode library
include('libs/phpqrcode/qrlib.php');

// Database Connection
$host = 'localhost';       // Replace with your DB host
$user = 'root';            // Replace with your DB username
$password = '';            // Replace with your DB password
$database = 'users';        // Replace with your DB name

// Connect to the Database
$conn = new mysqli($host, $user, $password, $database);

// Check Database Connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Aadhaar and DOB from User Input (via POST method)
$aadhaar = isset($_POST['aadhaar']) ? $_POST['aadhaar'] : '';
$dob = isset($_POST['dob']) ? $_POST['dob'] : '';

// Ensure user input is provided
if (!$aadhaar || !$dob) {
    die("Aadhaar and Date of Birth are required.");
}

// Fetch encrypted data based on Aadhaar and DOB
$query = "SELECT encrypted_data FROM user_ddocr WHERE aadhaar = ? AND dob = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('ss', $aadhaar, $dob);
$stmt->execute();
$stmt->bind_result($encrypted_data);
$stmt->fetch();
$stmt->close();

// Check if data exists
if (!$encrypted_data) {
    die("No data found for the provided Aadhaar and DOB.");
}

// Directory for QR codes
$qr_directory = 'qrcodes';

// Check if directory exists, otherwise create it
if (!is_dir($qr_directory)) {
    mkdir($qr_directory, 0777, true); // Create directory with write permissions
}

// File path for QR code
$qr_file_path = $qr_directory . '/user_qr_' . time() . '.png'; // Unique file name with timestamp

// Generate QR code
QRcode::png($encrypted_data, $qr_file_path, QR_ECLEVEL_L, 5); // Generate QR code with low error correction

// Close the Database Connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code Generator</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-image: url('photo1.jpg');
            background-repeat: no-repeat;
            background-position: center;
            background-size: cover;
            font-family: 'Arial', sans-serif;
        }

        .container {
            background-color: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 360px;
            text-align: center;
        }

        h1 {
            margin: 0 0 20px;
            font-size: 28px;
            color: #333;
        }

        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
        }

        .form-group input {
            width: 100%;
            padding: 12px;
            border-radius: 6px;
            border: 1px solid #ddd;
            box-sizing: border-box;
            font-size: 16px;
        }

        button {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 6px;
            background-color: #007bff;
            color: #fff;
            font-size: 18px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #0056b3;
        }

        .qr-button {
            margin-top: 20px;
            background-color: #28a745;
        }

        .qr-button:hover {
            background-color: #218838;
        }

        .error-message {
            color: red;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Generate Aadhaar QR Code</h1>
        <?php if (isset($qr_file_path)) : ?>
            <h3>QR Code Generated</h3>
            <img src="<?php echo $qr_file_path; ?>" alt="QR Code">
            <br><br>
            <a href="<?php echo $qr_file_path; ?>" download="aadhaar_qr.png">
                <button class="qr-button" type="button">Download QR Code</button>
            </a>
        <?php endif; ?>
    </div>
</body>
</html>
