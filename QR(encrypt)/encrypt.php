<?php
// Hide warnings and notices
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', '0');

// Database connection (MySQL)
$conn = new mysqli('localhost', 'root', '', 'users');
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Initialize variables
$hexEncryptedData = '';
$iv = '';
$encryptionStatus = '';

// Retrieve data from URL parameters and check if they are set
$name = isset($_GET['name']) ? $_GET['name'] : '';
$gender = isset($_GET['gender']) ? $_GET['gender'] : '';
$email = isset($_GET['email']) ? $_GET['email'] : '';
$aadhaar = isset($_GET['aadhaar']) ? $_GET['aadhaar'] : '';
$phone = isset($_GET['phone']) ? $_GET['phone'] : '';
$dob = isset($_GET['dob']) ? $_GET['dob'] : '';

if ($name && $gender && $phone && $aadhaar && $dob && $email) {
    // Encryption parameters
    $cipher = 'aes-256-cbc';

    // Generate a 6-digit numeric IV
    $iv = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);

    // Generate a key from DOB
    $key = substr(hash('sha256', strrev($dob)), 0, 32); // 256-bit key

    // Data to encrypt
    $data = "name=$name&gender=$gender&phone=$phone&aadhaar=$aadhaar&dob=$dob&email=$email";

    // Encrypt the data
    $encryptedData = openssl_encrypt($data, $cipher, $key, 0, $iv);

    if ($encryptedData !== false) {
        $hexEncryptedData = bin2hex($encryptedData); // Convert to hexadecimal

        // Save to database
        $stmt = $conn->prepare("INSERT INTO user_ddocr (name, gender,phone,aadhaar,dob,email, encrypted_data, iv) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('ssssssss', $name, $gender,$phone,$aadhaar, $dob, $email, $hexEncryptedData, $iv);
        $stmt->execute();
        $stmt->close();
    } else {
        $encryptionStatus = 'Encryption failed.';
    }
} else {
    $encryptionStatus = 'Please fill in all the fields.';
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Encryption Result</title>
    <style>
         body {
            font-family: Arial, sans-serif;
            background-image: url('photo1.jpg');
            background-repeat: no-repeat;
            background-position: center;
            background-size: cover;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: #fff;
        }

        .container {
            background-color: rgba(255, 255, 255, 0.9);            color: #333;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            padding: 30px;
            width: 400px;
            text-align: center;
        }

        h1 {
            margin-bottom: 20px;
            font-size: 24px;
        }

        p {
            font-size: 16px;
            line-height: 1.5;
            color: #333;
        }

        .error-message {
            color: red;
            font-weight: bold;
        }

        .btn {
            background-color: #2575fc;
            color: #fff;
            padding: 12px 20px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            margin-top: 20px;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        .btn:hover {
            background-color: #1a56d3;
        }

        .note {
            font-size: 14px;
            color: #666;
            margin-top: 20px;
        }

        .code-result {
            margin-top: 30px;
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .code-result p {
            word-wrap: break-word;
        }
        /* Add your existing CSS here */
    </style>
</head>
<body>
    <div class="container">
        <h1>Encryption Result</h1>
        
        <?php if ($encryptionStatus): ?>
            <p class="error-message"><?php echo htmlspecialchars($encryptionStatus); ?></p>
        <?php else: ?>
            <div class="code-result">
                <p><strong>Encrypted Data:</strong><br> <?php echo htmlspecialchars($hexEncryptedData); ?></p>
                <p><strong>IV (6-digit):</strong><br> <?php echo htmlspecialchars($iv); ?></p>
            </div>
            <form method="post" action="generate_qr.php">
                <input type="hidden" name="hexEncryptedData" value="<?php echo htmlspecialchars($hexEncryptedData); ?>">
                <button type="submit" class="btn">Generate QR Code</button>
            </form>
        <?php endif; ?>
        
        <p class="note">Click the button to generate the QR code with the encrypted data.</p>
    </div>
</body>
</html>
