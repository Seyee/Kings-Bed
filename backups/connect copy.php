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

// Capture and sanitize the form data
$name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
$email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
$phone = filter_var($_POST['phone'], FILTER_SANITIZE_STRING);
$address = filter_var($_POST['address'], FILTER_SANITIZE_STRING);
$room_type = filter_var($_POST['room_type'], FILTER_SANITIZE_STRING);
$checkin_date = $_POST['checkin_date'];
$checkout_date = $_POST['checkout_date'];

// Validate dates

// SQL query to insert data into the database
$sql = "INSERT INTO reservations (name, email, phone, address, room_type, checkin_date, checkout_date)
        VALUES (?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sssssss", $name, $email, $phone, $address, $room_type, $checkin_date, $checkout_date);

if ($stmt->execute()) {
    header("Location: reservation.php?status=success");
    exit;
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
