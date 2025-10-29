<?php
$host = "localhost";
$user = "root";  
$pass = "";  
$dbname = "user_data";

// Connect to MySQL
$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$decryptedData = isset($_GET['data']) ? $_GET['data'] : null;
$formData = [];
parse_str($decryptedData, $formData);

// Function to mask Aadhaar number
function maskAadhaar($aadhaar) {
    return isset($aadhaar) && strlen($aadhaar) === 12 ? substr($aadhaar, 0, 4) . ' **** **** ' . substr($aadhaar, -4) : '************';
}

$aadhaarMasked = maskAadhaar($formData['aadhaar'] ?? '');
$studentData = null;
$formSubmitted = false;

// Handle Form Submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $gender = $_POST['gender'];
    $email = $_POST['email'];
    $aadhaar = $_POST['aadhaar'];
    $phone = $_POST['phone'];
    $dob = $_POST['dob'];
    $marks_10 = $_POST['marks_10'];
    $marks_12 = $_POST['marks_12'];

    $uploadDir = "uploads/";
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

    // Photo Upload
    $photoPath = $uploadDir . basename($_FILES["photo"]["name"]);
    move_uploaded_file($_FILES["photo"]["tmp_name"], $photoPath);

    // Document Upload
    $documentPath = $uploadDir . basename($_FILES["document"]["name"]);
    move_uploaded_file($_FILES["document"]["tmp_name"], $documentPath);

    // Insert into Database
    $stmt = $conn->prepare("INSERT INTO students (name, gender, email, aadhaar, phone, dob, marks_10, marks_12, photo_path, document_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssddss", $name, $gender, $email, $aadhaar, $phone, $dob, $marks_10, $marks_12, $photoPath, $documentPath);
    $stmt->execute();
    $last_id = $stmt->insert_id; 
    $stmt->close();

    // Fetch submitted data
    $result = $conn->query("SELECT * FROM students WHERE id = $last_id");
    if ($result->num_rows > 0) {
        $studentData = $result->fetch_assoc();
        $formSubmitted = true;
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>College Admission Form</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: url('logo.jpeg') no-repeat center center/cover;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            background: rgba(246, 237, 237, 0.85);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            width: 400px;
            max-width: 100%;
            backdrop-filter: blur(10px);
        }
        h2, h3 {
            text-align: center;
            color: rgb(14, 14, 14);
            margin-bottom: 20px;
        }
        label {
            font-weight: bold;
            display: block;
            margin-top: 10px;
            color: rgb(6, 6, 6);
        }
        input {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background: rgba(255, 255, 255, 0.5);
            color: black;
        }
        input[readonly] {
            background: rgba(200, 200, 200, 0.5);
            color: #555;
        }
        button {
            width: 100%;
            padding: 10px;
            margin-top: 15px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: 0.3s;
        }
        button:hover {
            background: #0056b3;
        }
        img, embed {
            width: 100%;
            height: auto;
            margin-top: 10px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>College Admission Form</h2>

        <?php if (!$formSubmitted): ?>
        <form method="post" enctype="multipart/form-data">
            <input type="hidden" name="name" value="<?php echo htmlspecialchars($formData['name'] ?? ''); ?>">
            <input type="hidden" name="gender" value="<?php echo htmlspecialchars($formData['gender'] ?? ''); ?>">
            <input type="hidden" name="email" value="<?php echo htmlspecialchars($formData['email'] ?? ''); ?>">
            <input type="hidden" name="aadhaar" value="<?php echo htmlspecialchars($formData['aadhaar'] ?? ''); ?>">
            <input type="hidden" name="phone" value="<?php echo htmlspecialchars($formData['phone'] ?? ''); ?>">
            <input type="hidden" name="dob" value="<?php echo htmlspecialchars($formData['dob'] ?? ''); ?>">

            <label>Name:</label>
            <input type="text" value="<?php echo htmlspecialchars($formData['name'] ?? ''); ?>" readonly>

            <label>Gender:</label>
            <input type="text" value="<?php echo htmlspecialchars($formData['gender'] ?? ''); ?>" readonly>

            <label>Email ID:</label>
            <input type="email" value="<?php echo htmlspecialchars($formData['email'] ?? ''); ?>" readonly>

            <label>Aadhaar Number:</label>
            <input type="text" value="<?php echo $aadhaarMasked; ?>" readonly>

            <label>Phone Number:</label>
            <input type="text" value="<?php echo htmlspecialchars($formData['phone'] ?? ''); ?>" readonly>

            <label>Date of Birth:</label>
            <input type="text" value="<?php echo htmlspecialchars($formData['dob'] ?? ''); ?>" readonly>

            <label>10th Marks:</label>
            <input type="text" name="marks_10" required>

            <label>12th Marks:</label>
            <input type="text" name="marks_12" required>

            <label>Upload Photo:</label>
            <input type="file" name="photo" accept="image/*" required>

            <label>Upload Document (PDF only):</label>
            <input type="file" name="document" accept=".pdf" required>

            <button type="submit">Submit</button>
        </form>
        <?php else: ?>
        <h3>Submitted Data:</h3>
        <p><strong>Name:</strong> <?php echo $studentData['name']; ?></p>
        <p><strong>Email:</strong> <?php echo $studentData['email']; ?></p>
        <p><strong>Phone:</strong> <?php echo $studentData['phone']; ?></p>
        <p><strong>Dob:</strong> <?php echo $studentData['dob']; ?></p>
        <p><strong>Gender:</strong> <?php echo $studentData['gender']; ?></p>
        <p><strong>10th_Mark(%):</strong> <?php echo $studentData['marks_10']; ?></p>
        <p><strong>12th_Mark(%):</strong> <?php echo $studentData['marks_12']; ?></p>

        <img src="<?php echo $studentData['photo_path']; ?>" alt="Uploaded Photo">

            <p><strong>Uploaded Document:</strong> <a href="<?php echo $studentData['document_path']; ?>" target="_blank">View Document</a></p>


                <button class="back-btn" onclick="window.location.href='game.html'">Back</button>

        <?php endif; ?>
    </div>
</body>
</html>
