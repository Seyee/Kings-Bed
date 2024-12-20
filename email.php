<?php
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

// SQL query to fetch all reservations
$sql = "SELECT * FROM reservations";
$result = $conn->query($sql);

require '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);

// Server settings
$mail->isSMTP();
$mail->Host = 'smtp.gmail.com';
$mail->SMTPAuth = true;
$mail->Username = 'kingsbedhotel.help@gmail.com'; // Your Gmail address
$mail->Password = 'pzld ctos ersh gayg';   // Gmail App Password
$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
$mail->Port = 587;

// Loop through each reservation and send email
while($row = $result->fetch_assoc()) {
    try {
        // Recipients
        $mail->setFrom('your-email@gmail.com', 'kingsbedhotel.com');
        $mail->addAddress($row['email']); // Automatically get the email from the database
        
        // Content
        $mail->isHTML(false);
        $mail->Subject = "New Reservation from kingsbedhotel.com";
        $mail->Body = "Dear {$row['name']},\n\nWe have received your reservation. Our team at King's Bed Hotel will contact you within the next 30 minutes to confirm your booking and provide further details. If you have any questions or need to make changes to your reservation, please don't hesitate to reach out.\n\nThank you for choosing us, and we look forward to serving you soon!\n\nBest regards,\nKing's Bed Hotel";

        
        // Send email
        $mail->send();
        
        // Clear recipient for next iteration
        $mail->clearAddresses();
    } catch (Exception $e) {
        echo "Error: Email not sent. Mailer Error: {$mail->ErrorInfo}";
    }
}

$conn->close();
?>
