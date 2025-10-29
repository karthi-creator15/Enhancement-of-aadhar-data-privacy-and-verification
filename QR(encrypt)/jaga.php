<?php
if (isset($_FILES['pdf']) && isset($_POST['password']) && isset($_POST['email'])) {
    // Retrieve the uploaded PDF file, password, and email
    $pdfFile = $_FILES['pdf']['tmp_name'];
    $password = $_POST['password'];
    $email = $_POST['email'];

    // Call the Python script to extract data from the PDF
    $command = escapeshellcmd("python dim.py $pdfFile $password");
    $output = shell_exec($command);

    // Check if the output is valid and forward the extracted data to encryption.php
    if ($output) {
        // URL encode the output and email before passing them as parameters
        $encodedOutput = urlencode($output);
        $encodedEmail = urlencode($email);
        header("Location: encryption.php?data=$encodedOutput&email=$encodedEmail");
    } else {
        echo "No data was extracted from the PDF.";
    }
    exit();
} else {
    echo "Please upload a PDF file, provide a password, and enter an email address.";
}
