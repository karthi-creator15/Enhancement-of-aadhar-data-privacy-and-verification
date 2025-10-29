<?php
$name = isset($_GET['name']) ? $_GET['name'] : '';
$dob = isset($_GET['dob']) ? $_GET['dob'] : '';
$address = isset($_GET['address']) ? $_GET['address'] : '';
$aadhaar = isset($_GET['aadhaar']) ? $_GET['aadhaar'] : ''; // Masked Aadhaar
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admission Form</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; }
        .container { max-width: 400px; margin: auto; padding: 20px; border: 1px solid #ddd; border-radius: 10px; }
        input, button { width: 100%; padding: 10px; margin-top: 10px; }
    </style>
</head>
<body>

    <h2>College Admission Form</h2>

    <form action="submit_form.php" method="POST">
        <label>Name:</label>
        <input type="text" name="name" value="<?= htmlspecialchars($name) ?>" required>

        <label>Date of Birth (DOB):</label>
        <input type="date" name="dob" value="<?= htmlspecialchars($dob) ?>" required>

        <label>Address:</label>
        <input type="text" name="address" value="<?= htmlspecialchars($address) ?>" required>

        <label>Aadhaar Number:</label>
        <input type="text" name="aadhaar" value="<?= htmlspecialchars($aadhaar) ?>" readonly>

        <button type="submit">Submit</button>
    </form>

</body>
</html>
