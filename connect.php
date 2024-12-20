<?php
// Import PHPMailer classes
require '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Database connection details
$servername = "localhost";
$username = "root";
$password = "wawapogi@202X";
$dbname = "schoolsystem"; 

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Capture and sanitize the form data
$name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
$email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
$phone = filter_var($_POST['phone'], FILTER_SANITIZE_STRING);
$address = filter_var($_POST['address'], FILTER_SANITIZE_STRING);
$room_type = filter_var($_POST['room_type'], FILTER_SANITIZE_STRING);
$checkin_date = $_POST['checkin_date'];
$checkout_date = $_POST['checkout_date'];
$status = 'pending'; // Default status

// SQL query to insert data into the database
$sql = "INSERT INTO reservations (name, email, phone, address, room_type, checkin_date, checkout_date, status)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssssss", $name, $email, $phone, $address, $room_type, $checkin_date, $checkout_date, $status);

if ($stmt->execute()) {
    // Send confirmation email after successful insertion
    $mail = new PHPMailer(true);

    // Server settings
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'kingsbedhotel.help@gmail.com'; // Your Gmail address
    $mail->Password = 'pzld ctos ersh gayg';   // Gmail App Password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    try {
        // Send email to the hotel admin
        $mail->setFrom('your-email@gmail.com', 'King\'s Bed Hotel');
        $mail->addAddress('kingsbedhotel.help@gmail.com');  // The hotel's admin email
        $mail->isHTML(false);
        $mail->Subject = "New Reservation Alert";
        $mail->Body = "A new reservation request has been made by $name. Here are the details:\n\nName: $name\nEmail: $email\nPhone: $phone\nAddress: $address\nRoom Type: $room_type\nCheck-in Date: $checkin_date\nCheck-out Date: $checkout_date";
        $mail->send();

        // Send email to the user
        $mail->clearAddresses();  // Clear previous recipient
        $mail->addAddress($email);  // User's email
        $mail->Subject = "Thank You! We have Received Your Reservation Request";
        $mail->Body = "Dear $name,\n\nWe have received your reservation. Our team at King's Bed Hotel will contact you within the next 30 minutes to confirm your booking and provide further details. If you have any questions or need to make changes to your reservation, please don't hesitate to reach out.\n\nThank you for choosing us, and we look forward to serving you soon!\n\nBest regards,\nKing's Bed Hotel";
        $mail->send();
    } catch (Exception $e) {
        echo "Error: Email not sent. Mailer Error: {$mail->ErrorInfo}";
    }

    // Redirect to a success page
    header("Location: reservation.php?status=reservation_success");
    exit;
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
