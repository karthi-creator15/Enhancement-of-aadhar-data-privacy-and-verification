<?php
$decryptedData = isset($_GET['data']) ? $_GET['data'] : null;

$formData = [];
parse_str($decryptedData, $formData);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Decryption Result</title>
    <style>
        html, body {
            height: 100%;
            margin: 0;
            font-family: Arial, sans-serif;
            background-image: url('photo.jpg'); /* Replace 'your-image-path.jpg' with your image file path */
            background-repeat: no-repeat;
            background-position: center;
            background-size: cover;
        }

        h1 {
            text-align: center;
            color: #333;
            margin-top: 20px;
        }

        form {
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 30px;
            max-width: 600px;
            margin: 20px auto;
            transition: transform 0.3s ease-in-out;
        }

        form:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        .form-row {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
        }

        .form-group {
            flex: 1;
            margin: 5px;
        }

        .form-group label {
            display: block;
            margin: 10px 0 5px;
            color: #555;
            font-weight: bold;
            font-size: 1.05em;
        }

        .form-group input {
            width: 100%;
            padding: 10px;
            margin: 5px 0 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            box-sizing: border-box;
            transition: box-shadow 0.3s ease-in-out;
        }

        input[type="text"]:focus,
        input[type="email"]:focus {
            box-shadow: 0 4px 8px rgba(0, 123, 255, 0.2);
            border-color: #007bff;
        }

        input[type="submit"],
        button {
            width: 100%;
            padding: 10px;
            margin: 15px auto 0;
            border: none;
            border-radius: 6px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            cursor: pointer;
            transition: background-color 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
            display: block;
        }

        input[type="submit"] {
            background-color: #007bff;
            color: #fff;
            max-width: 200px;
            text-align: center;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        .form-footer {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <h1>User Details</h1>
    <form method="post" action="process_form.php">
        <div class="form-row">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($formData['name'] ?? ''); ?>" readonly>
            </div>
            <div class="form-group">
                <label for="gender">Gender:</label>
                <input type="text" id="gender" name="gender" value="<?php echo htmlspecialchars($formData['gender'] ?? ''); ?>" readonly>
            </div>
            <div class="form-group">
                <label for="email">Email ID:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($formData['email'] ?? ''); ?>" readonly>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="aadhaar">Aadhaar Number:</label>
                <input type="text" id="aadhaar" name="aadhaar" value="<?php echo htmlspecialchars($formData['aadhaar'] ?? ''); ?>" readonly>
            </div>
            <div class="form-group">
                <label for="phone">phone Number:</label>
                <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($formData['phone'] ?? ''); ?>" readonly>
            </div>
            <div class="form-group">
                <label for="dob">DOB:</label>
                <input type="data" id="ration" name="ration" value="<?php echo htmlspecialchars($formData['dob'] ?? ''); ?>" readonly>
            </div>
        </div>
        <div class="form-footer">
            <input type="submit" value="Submit">
        </div>
    </form>
</body>
</html>