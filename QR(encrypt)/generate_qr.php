<?php
include 'libs/phpqrcode/qrlib.php';

// Initialize variables
$hexEncryptedData = '';
$filePath = '';
$errorMessage = '';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['hexEncryptedData'])) {
    $hexEncryptedData = $_POST['hexEncryptedData'];

    if (!empty($hexEncryptedData)) {
        // Directory and file setup
        $tempDir = 'temp/';
        $fileName = uniqid('qrcode_', true) . '.png';
        $filePath = $tempDir . $fileName;

        // Ensure temp directory exists
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0777, true);
        }

        // Generate QR code
        QRcode::png($hexEncryptedData, $filePath, QR_ECLEVEL_L, 5);
    } else {
        $errorMessage = 'No valid data provided for QR code generation.';
    }
} else {
    $errorMessage = 'No QR Code generated yet. Please provide valid data.';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code Generator</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-image: url('photo.jpg');
            background-repeat: no-repeat;
            background-position: center;
            background-size: cover;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: #333;
        }

        .container {
            text-align: center;
            background: #fff;
            color: #333;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            width: 90%;
            max-width: 400px;
        }

        h1 {
            margin-bottom: 20px;
        }

        p {
            margin: 10px 0;
        }

        img {
            margin: 20px 0;
            width: 200px;
            height: 200px;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            background-color: #ff758c;
            color: #fff;
            cursor: pointer;
            font-size: 16px;
            text-decoration: none;
            display: inline-block;
        }

        .btn:hover {
            background-color: #ff5063;
        }

        .error-message {
            color: red;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>QR Code Generator</h1>
        <?php if (!empty($errorMessage)): ?>
            <p class="error-message"><?php echo htmlspecialchars($errorMessage); ?></p>
        <?php elseif (!empty($filePath) && file_exists($filePath)): ?>
            <!-- Display QR Code -->
            <img src="<?php echo htmlspecialchars($filePath); ?>" alt="QR Code">
            <!-- Download QR Code -->
            <a href="<?php echo htmlspecialchars($filePath); ?>" download="qrcode.png" class="btn">Download QR Code</a>
        <?php endif; ?>
    </div>
</body>
</html>
