<?php
session_start();

// Check if the user is logged in, if not redirect to login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// logout
if (isset($_POST['logout'])) {
    session_destroy(); // Destroy the session
    header("Location: login.php"); // Redirect to login page
    exit;
}

// For connection
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

require '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);

// Server settings for email
$mail->isSMTP();
$mail->Host = 'smtp.gmail.com';
$mail->SMTPAuth = true;
$mail->Username = 'kingsbedhotel.help@gmail.com'; // Your Gmail address
$mail->Password = 'pzld ctos ersh gayg';   // Gmail App Password
$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
$mail->Port = 587;

// Handle the Confirm Reservation action
if (isset($_GET['confirm_id'])) {
    $confirm_id = $_GET['confirm_id'];
    
    // Update reservation status to 'confirmed'
    $confirm_sql = "UPDATE reservations SET status = 'confirmed' WHERE id = ?";
    $stmt = $conn->prepare($confirm_sql);
    $stmt->bind_param("i", $confirm_id);
    $stmt->execute();

    // Fetch reservation details
    $sql = "SELECT * FROM reservations WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $confirm_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $reservation = $result->fetch_assoc();

    // Send confirmation email
    try {
        // Recipients
        $mail->setFrom('kingsbedhotel.help@gmail.com', 'King\'s Bed Hotel');
        $mail->addAddress($reservation['email']); // Get the email from the reservation record
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = "Reservation Confirmed - kingsbedhotel.com";
        $mail->Body = '
        <html>
        <head>
            <title>Reservation Confirmation</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    background-color: #f4f4f4;
                    color: #333;
                    padding: 20px;
                }
                .receipt-container {
                    background-color: #fff;
                    padding: 20px;
                    border-radius: 8px;
                    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                    max-width: 600px;
                    margin: 0 auto;
                }
                .header {
                    text-align: center;
                    margin-bottom: 20px;
                }
                .header h1 {
                    color: #0044cc;
                }
                .details {
                    margin: 20px 0;
                }
                .details ul {
                    list-style: none;
                    padding: 0;
                }
                .details li {
                    margin: 8px 0;
                    font-size: 16px;
                }
                .total {
                    font-size: 18px;
                    font-weight: bold;
                    color: #0044cc;
                    margin-top: 20px;
                }
                .footer {
                    text-align: center;
                    margin-top: 30px;
                    font-size: 14px;
                    color: #777;
                }
            </style>
        </head>
        <body>
            <div class="receipt-container">
                <div class="header">
                    <h1>King\'s Bed Hotel</h1>
                    <p>Reservation Confirmation Receipt</p>
                </div>
                <div class="details">
                    <p><strong>Dear ' . htmlspecialchars($reservation['name']) . ',</strong></p>
                    <p>Thank you for your reservation! Below are your reservation details:</p>
                    <ul>
                        <li><strong>Reservation ID:</strong> ' . htmlspecialchars($reservation['id']) . '</li>
                        <li><strong>Room Type:</strong> ' . htmlspecialchars($reservation['room_type']) . '</li>
                        <li><strong>Check-in Date:</strong> ' . htmlspecialchars($reservation['checkin_date']) . '</li>
                        <li><strong>Check-out Date:</strong> ' . htmlspecialchars($reservation['checkout_date']) . '</li>
                        <li><strong>Guest Name:</strong> ' . htmlspecialchars($reservation['name']) . '</li>
                        <li><strong>Email:</strong> ' . htmlspecialchars($reservation['email']) . '</li>
                        <li><strong>Phone:</strong> ' . htmlspecialchars($reservation['phone']) . '</li>
                        <li><strong>Address:</strong> ' . htmlspecialchars($reservation['address']) . '</li>
                    </ul>
                </div>
                <div class="footer">
                    <p>If you have any questions or need to make changes to your reservation, feel free to contact us.</p>
                    <p>Contact Us: <strong>(87900-900)</strong> | Email: <strong>kingsbedhotel.help@gmail.com</strong></p>
                    <p>Thank you for choosing King\'s Bed Hotel!</p>
                </div>
            </div>
        </body>
        </html>';

        
        $mail->send();

        
        header("Location: admin.php?status=success");
        exit;
    } catch (Exception $e) {
        echo "Error: Email not sent. Mailer Error: {$mail->ErrorInfo}";
    }
}

// reservation get all the info for admin
$sql = "SELECT * FROM reservations";
$reservations_result = $conn->query($sql);

//html
echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Admin - King's Bed Hotel</title>
    <link rel='stylesheet' href='admin.css'> <!-- Make sure this path is correct -->
</head>
<body>

    <div class='header'>
    <img src='https://i.ibb.co/FmT02bH/King-s-Bed-Hotel-2500-x-1024-px-4.png' alt='King\'s Bed Hotel Logo' class='logo'>
    </div>

    <h2>Reservations List</h2>

    <div class='logout-container'>
    <form method='POST'>
        <button type='submit' name='logout' class='logout-button'>Log Out</button>
    </form>
    </div>

    
    <table class='reservations-table'>
        <thead>
            <tr>
                <th>ID</th>
                <th>Room Type</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Address</th>
                <th>Check-in Date</th>
                <th>Check-out Date</th>
                <th>Created at</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>";

while ($row = $reservations_result->fetch_assoc()) {
    $actionText = ($row['status'] == 'confirmed') ? 'Resend' : 'Confirm';
    echo "<tr>
            <td>" . htmlspecialchars($row['id']) . "</td>
            <td>" . htmlspecialchars($row['room_type']) . "</td>
            <td>" . htmlspecialchars($row['name']) . "</td>
            <td>" . htmlspecialchars($row['email']) . "</td>
            <td>" . htmlspecialchars($row['phone']) . "</td>
            <td>" . htmlspecialchars($row['address']) . "</td>
            <td>" . htmlspecialchars($row['checkin_date']) . "</td>
            <td>" . htmlspecialchars($row['checkout_date']) . "</td>
            <td>" . htmlspecialchars((new DateTime($row['created_at']))->format('Y-m-d h:i A')) . "</td>
            <td>" . htmlspecialchars($row['status']) . "</td>
            <td><a href='?confirm_id=" . htmlspecialchars($row['id']) . "'>$actionText</a></td>
        </tr>";
}

echo "</tbody></table>
</body>
</html>";

$conn->close();
?>
