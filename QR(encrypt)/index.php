<?php
// Database connection setup
$servername = "localhost";
$username = "root"; // your DB username
$password = ""; // your DB password
$dbname = "users"; // your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the Aadhaar number from the form
    $aadhaar = $_POST['aadhaar'];

    // Check if Aadhaar number exists in the database
    $sql = "SELECT * FROM aadhaar WHERE aadhaar = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $aadhaar);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Aadhaar exists, proceed with encryption and QR generation logic
        $name = $_POST['name'];
        $gender = $_POST['gender'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $dob = $_POST['dob'];

        // Redirect to encrypt.php with form data
        header("Location: encrypt.php?name=$name&gender=$gender&email=$email&aadhaar=$aadhaar&phone=$phone&dob=$dob");
        exit(); // Stop further execution
    } else {
        // Aadhaar does not exist in the database
        echo "<script>alert('Invalid Aadhaar number. Please provide a valid Aadhaar number.');</script>";
    }
}
$today = date('Y-m-d');
$six_years_ago = date('Y-m-d', strtotime('-6 years', strtotime($today)));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Encrypt & Generate QR</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
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
            background: #fff;
            color: #333;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            padding: 30px;
            width: 400px;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 24px;
        }

        label {
            font-weight: bold;
            display: block;
            margin-top: 15px;
        }

        input, select, button {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border-radius: 8px;
            border: 1px solid #ddd;
            font-size: 16px;
            box-sizing: border-box;
        }

        button {
            background: #2575fc;
            color: #fff;
            border: none;
            cursor: pointer;
            margin-top: 20px;
        }

        button:hover {
            background: #1a56d3;
        }

        .note {
            font-size: 14px;
            text-align: center;
            color: #666;
        }
    </style>
    <script>
        function validateAadhaar() {
            var aadhaar = document.getElementById("aadhaar").value;
            if (aadhaar.length !== 12 || isNaN(aadhaar)) {
                alert("Aadhaar number must be exactly 12 digits.");
                return false;
            }
            return true;
        }
    </script>
</head>
<body>
    <div class="container">
        <h1>Encrypt & Generate QR</h1>
        <form method="post" action="" onsubmit="return validateAadhaar()">
            <label for="name">Name</label>
            <input type="text" id="name" name="name" placeholder="Enter your name" required>

            <label for="gender">Gender</label>
            <select id="gender" name="gender" required>
                <option value="">Select Gender</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
                <option value="Other">Other</option>
            </select>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="Enter your email" required>

            <label for="aadhaar">Aadhaar Number</label>
            <input type="text" id="aadhaar" name="aadhaar" placeholder="Enter Aadhaar number" required>

            <label for="phone">Phone Number</label>
            <input type="text" id="phone" name="phone" placeholder="Enter Phone number" required>

            <div class="form-group">
        <label for="dob">Date of Birth</label>
        <input type="date" id="dob" name="dob" required max="<?php echo $six_years_ago; ?>">
            </div>

            <button type="submit">Encrypt & Proceed</button>
        </form>
        <p class="note">Your data will be securely encrypted and converted to a QR code.</p>
    </div>
</body>
</html>

<?php
$conn->close();
?>
