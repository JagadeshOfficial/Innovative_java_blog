<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

session_start(); // Start session here at the top

// Database connection
$servername = "localhost"; 
$username = "root";        
$password = "12345";            
$dbname = "blog"; 

$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (isset($_POST['action'])) {
        if ($_POST['action'] == 'send_otp') {
            $email = $_POST['email'];
            $otp = rand(100000, 999999); // Generate 6-digit OTP
            
            // Start PHPMailer
            $mail = new PHPMailer(true);
            
            try {
                $mail->SMTPDebug = 2; // Set to 2 to enable debug output
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'innovatejavaschool@gmail.com';
                $mail->Password   = 'your-app-specific-password'; // Replace with your app-specific password
                $mail->SMTPSecure = 'tls';
                $mail->Port       = 587;

                $mail->setFrom('innovatejavaschool@gmail.com', 'Innovate Java School');
                $mail->addAddress($email);

                $mail->isHTML(true);
                $mail->Subject = 'Your OTP Code';
                $mail->Body    = 'Your OTP is: <b>' . $otp . '</b>';
                $mail->AltBody = 'Your OTP is: ' . $otp;

                if ($mail->send()) {
                    // Store OTP and email in session
                    $_SESSION['otp'] = $otp;
                    $_SESSION['email'] = $email;

                    echo json_encode(['status' => 'success', 'message' => 'OTP has been sent to your email.']);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Unable to send OTP.']);
                }
            } catch (Exception $e) {
                echo json_encode(['status' => 'error', 'message' => 'Mailer Error: ' . $mail->ErrorInfo]);
            }

        } elseif ($_POST['action'] == 'verify_otp') {
            // Verify the OTP entered by the user
            $input_otp = $_POST['otp'];

            if (isset($_SESSION['otp']) && $_SESSION['otp'] == $input_otp) {
                echo json_encode(['status' => 'success', 'message' => 'OTP verified successfully.']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Invalid OTP.']);
            }

        } elseif ($_POST['action'] == 'submit_form') {
            // Process the form submission after OTP verification
            $name = $_POST['name'];
            $email = $_SESSION['email'];
            $mobile = $_POST['mobile'];
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

            // Check if email already exists in the database
            $check_email_query = "SELECT * FROM users WHERE email = ?";
            $check_stmt = $conn->prepare($check_email_query);
            $check_stmt->bind_param("s", $email);
            $check_stmt->execute();
            $result = $check_stmt->get_result();

            if ($result->num_rows > 0) {
                echo json_encode(['status' => 'error', 'message' => 'This email is already registered.']);
            } else {
                // Insert the new user data into the database
                $sql = "INSERT INTO users (name, email, mobile, password) VALUES (?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssss", $name, $email, $mobile, $password);

                if ($stmt->execute()) {
                    echo json_encode(['status' => 'success', 'message' => 'Form submitted successfully.']);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Could not save user data.']);
                }

                $stmt->close();
            }

            $check_stmt->close();
        }
    }

    $conn->close();
}
?>
