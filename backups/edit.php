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

// Fetch reservation details for editing
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM reservations WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $reservation = $result->fetch_assoc();
}

// Handle form submission for updating reservation
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $reservation['status'] != 'confirmed') {
    $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $phone = filter_var($_POST['phone'], FILTER_SANITIZE_STRING);
    $address = filter_var($_POST['address'], FILTER_SANITIZE_STRING);
    $room_type = filter_var($_POST['room_type'], FILTER_SANITIZE_STRING);
    $checkin_date = $_POST['checkin_date'];
    $checkout_date = $_POST['checkout_date'];

    $update_sql = "UPDATE reservations SET name = ?, email = ?, phone = ?, address = ?, room_type = ?, checkin_date = ?, checkout_date = ? WHERE id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("sssssssi", $name, $email, $phone, $address, $room_type, $checkin_date, $checkout_date, $id);
    if ($stmt->execute()) {
        header("Location: admin.php");
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Reservation</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <div class="container">
        <h1>Edit Reservation</h1>

        <?php if ($reservation['status'] == 'confirmed'): ?>
            <p>This reservation has already been confirmed and cannot be edited.</p>
        <?php else: ?>
            <form action="edit.php?id=<?php echo $reservation['id']; ?>" method="POST">
                <label for="name">Name</label>
                <input type="text" name="name" value="<?php echo htmlspecialchars($reservation['name']); ?>" required>

                <label for="email">Email</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($reservation['email']); ?>" required>

                <label for="phone">Phone</label>
                <input type="text" name="phone" value="<?php echo htmlspecialchars($reservation['phone']); ?>" required>

                <label for="address">Address</label>
                <input type="text" name="address" value="<?php echo htmlspecialchars($reservation['address']); ?>" required>

                <label for="room_type">Room Type</label>
                <input type="text" name="room_type" value="<?php echo htmlspecialchars($reservation['room_type']); ?>" required>

                <label for="checkin_date">Check-in Date</label>
                <input type="date" name="checkin_date" value="<?php echo htmlspecialchars($reservation['checkin_date']); ?>" required>

                <label for="checkout_date">Check-out Date</label>
                <input type="date" name="checkout_date" value="<?php echo htmlspecialchars($reservation['checkout_date']); ?>" required>

                <button type="submit">Update Reservation</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
