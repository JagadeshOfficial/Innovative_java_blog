<?php
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Adjust path if you're not using Composer

$servername = "localhost"; // Database server
$username = "root"; // Database username
$password = "12345"; // Database password
$dbname = "blog"; // Database name

// Create connection to MySQL database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check if the connection is successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Action handling
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];

    // Send OTP to the user email
    if ($action == 'send_otp') {
        $email = $_POST['email'];
        $otp = rand(100000, 999999); // Generate a random 6-digit OTP

        // Save OTP and expiration time in session
        $_SESSION['otp'] = $otp;
        $_SESSION['otp_expiration'] = time() + 300; // OTP valid for 5 minutes
        $_SESSION['otp_verified'] = false; // Reset OTP verification status

        // Set up PHPMailer with SMTP
        $mail = new PHPMailer(true);
        try {
            // SMTP configuration
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'innovatejavaschool@gmail.com';
            $mail->Password = 'tstdfhhaooshctmk';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Recipients
            $mail->setFrom('innovatejavaschool@gmail.com', 'Innovative Java School');
            $mail->addAddress($email);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Your OTP for Sign Up';
            $mail->Body    = "Your OTP is: <strong>$otp</strong>";

            // Send email
            $mail->send();
            echo json_encode(['status' => 'success', 'message' => 'OTP sent successfully to your email']);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => 'Failed to send OTP: ' . $mail->ErrorInfo]);
        }
    }

    // Verify OTP action
    if ($action == 'verify_otp') {
        $userOtp = $_POST['otp'];

        // Check if the OTP is correct and not expired
        if ($_SESSION['otp'] == $userOtp && time() < $_SESSION['otp_expiration']) {
            $_SESSION['otp_verified'] = true; // Set verification flag
            echo json_encode(['status' => 'success', 'message' => 'OTP verified successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid OTP or OTP has expired']);
        }
    }

    // Submit form action after OTP verification
    if ($action == 'submit_form') {
        // Ensure OTP is verified
        if (!isset($_SESSION['otp_verified']) || $_SESSION['otp_verified'] !== true) {
            echo json_encode(['status' => 'error', 'message' => 'Please verify your OTP first']);
            exit();
        }

        // Get form data
        $name = $_POST['name'];
        $email = $_POST['email'];
        $mobile = $_POST['mobile'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

        // Insert the user data into the database
        $sql = "INSERT INTO users (name, email, mobile, password) VALUES ('$name', '$email', '$mobile', '$password')";

        if ($conn->query($sql) === TRUE) {
            // Clear session variables
            unset($_SESSION['otp']);
            unset($_SESSION['otp_expiration']);
            unset($_SESSION['otp_verified']);

            // Redirect success message
            echo json_encode(['status' => 'success', 'message' => 'User registered successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error: ' . $conn->error]);
        }
    }
}

$conn->close(); // Close the database connection
?>
