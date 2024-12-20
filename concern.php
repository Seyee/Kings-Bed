<?php
$servername = "localhost"; // usually localhost
$username = "root"; // your database username
$password = "wawapogi@202X"; // your database password
$dbname = "schoolsystem"; // your database name

// connect
$conn = new mysqli($servername, $username, $password, $dbname);

// checks connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


$stmt = $conn->prepare("INSERT INTO concerns (name, email, message) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $name, $email, $message);

// Set parameters
$name = $_POST['name'];
$email = $_POST['email'];
$message = $_POST['message'];

if ($conn->query($sql) === TRUE) {
    // Redirect
    header("Location: concern.html");
    exit;
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}
$stmt->close();
$conn->close();
?>
