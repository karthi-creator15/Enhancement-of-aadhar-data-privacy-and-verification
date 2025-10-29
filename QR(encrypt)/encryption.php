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
$extractedDataDisplay = ''; // For displaying extracted data

// Check if the 'data' and 'email' parameters are available in the URL
if (isset($_GET['data']) && isset($_GET['email'])) {
    // Retrieve the data and email from the URL and decode them
    $extractedData = json_decode(urldecode($_GET['data']), true);
    $email = urldecode($_GET['email']);

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $encryptionStatus = 'Invalid email address.';
    } else {
        // Check if decoding was successful
        if (is_array($extractedData)) {
            // Display extracted data for user review
            $extractedDataDisplay = '<h2>Extracted Data:</h2>';
            foreach ($extractedData as $key => $value) {
                $extractedDataDisplay .= "<p><strong>" . htmlspecialchars($key) . ":</strong> " . htmlspecialchars($value) . "</p>";
            }

            // Proceed if the data is valid
            $name = isset($extractedData['Name']) ? $extractedData['Name'] : 'Not Found';
            $gender = isset($extractedData['Gender']) ? $extractedData['Gender'] : 'Not Found';
            $aadhaar = isset($extractedData['Aadhaar_Number']) ? str_replace(' ', '', $extractedData['Aadhaar_Number']) : 'Not Found';
            $phone = isset($extractedData['Phone']) ? $extractedData['Phone'] : 'Not Found';
            $dob = isset($extractedData['DOB']) ? $extractedData['DOB'] : 'Not Found';

            // Format the DOB from dd/mm/yyyy to yyyy-mm-dd
            $dobFormatted = DateTime::createFromFormat('d/m/Y', $dob);
            if ($dobFormatted) {
                $dob = $dobFormatted->format('Y-m-d'); // Reformat to YYYY-MM-DD
            } else {
                $dob = 'Invalid Date';
            }

            // AES-256 Encryption
            $cipher = 'aes-256-cbc';

            // Generate a 6-digit numeric IV
            $iv = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);

            // Generate a key from DOB (reverse the DOB and use SHA-256)
            $key = substr(hash('sha256', strrev($dob)), 0, 32); // 256-bit key

            // Data to encrypt
            $dataToEncrypt = "name=$name&gender=$gender&phone=$phone&aadhaar=$aadhaar&dob=$dob&email=$email";
            
            // Encrypt the data
            $encryptedData = openssl_encrypt($dataToEncrypt, $cipher, $key, 0, $iv);

            if ($encryptedData !== false) {
                $hexEncryptedData = bin2hex($encryptedData); // Convert to hexadecimal

                // Save to database
                $stmt = $conn->prepare("INSERT INTO user_ddocr(name, gender, phone, aadhaar, dob, email, encrypted_data, iv) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param('ssssssss', $name, $gender, $phone, $aadhaar, $dob, $email, $hexEncryptedData, $iv);
                $stmt->execute();
                $stmt->close();
            } else {
                $encryptionStatus = 'Encryption failed.';
            }
        } else {
            $encryptionStatus = 'Error: Invalid data received or data is null.';
        }
    }
} else {
    $encryptionStatus = 'No data or email provided.';
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
        /* Your existing styles */
        body {
            font-family: Arial, sans-serif;
            background-image: url('photo.jpg');
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
            background-color: rgba(255, 255, 255, 0.9);
            color: #333;
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
        .code-result, .extracted-data {
            margin-top: 30px;
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .code-result p, .extracted-data p {
            word-wrap: break-word;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Encryption Result</h1>

        <?php if ($encryptionStatus): ?>
            <p class="error-message"><?php echo htmlspecialchars($encryptionStatus); ?></p>
        <?php else: ?>
            <div class="extracted-data">
                <?php echo $extractedDataDisplay; ?>
            </div>
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
