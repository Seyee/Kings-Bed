<?php
session_start();

if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true) {
    $loginMessage = "Successfully logged in!";
    $showLoginModal = true;
    unset($_SESSION['user_logged_in']); // Unset the session variable after showing the login message
} else {
    $showLoginModal = false;
}

if (isset($_GET['status']) && $_GET['status'] === 'reservation_success') {
    // Display the success modal
    $showReservationSuccessModal = true;
} else {
    $showReservationSuccessModal = false;
}


// Check if the user has logged out
if (isset($_GET['status']) && $_GET['status'] === 'logout_success') {
    $logoutMessage = "You have been logged out.";
    $showLogoutModal = true;
} else {
    $showLogoutModal = false;
}

// Modal content for login reminder when not logged in
if (isset($_GET['reservation']) && !isset($_SESSION['user_id'])) {
    $modalMessage = "You have to login to make reservations";
    $showReservationModal = true;
} else {
    $showReservationModal = false;
}

// If the user isn't logged in, redirect to login page
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Initialize variables for pre-filling form
$name = $email = $phone = $address = $room_type = $checkin_date = $checkout_date = "";

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['confirm'])) {
    // Save form data to session
    $_SESSION['reservation_data'] = $_POST;
    
    // Get form data
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $room_type = $_POST['room_type'];
    $checkin_date = $_POST['checkin_date'];
    $checkout_date = $_POST['checkout_date'];
}

// If reservation is confirmed, clear the session data and redirect
if (isset($_POST['confirm'])) {
    // Here you would handle the reservation, e.g., store the data in the database
    unset($_SESSION['reservation_data']); // Clear the session data
    header("Location: reservation.php?status=reservation_success"); // Redirect after successful reservation
    exit;
}

// Retrieve the data from session if available
if (isset($_SESSION['reservation_data'])) {
    $name = $_SESSION['reservation_data']['name'];
    $email = $_SESSION['reservation_data']['email'];
    $phone = $_SESSION['reservation_data']['phone'];
    $address = $_SESSION['reservation_data']['address'];
    $room_type = $_SESSION['reservation_data']['room_type'];
    $checkin_date = $_SESSION['reservation_data']['checkin_date'];
    $checkout_date = $_SESSION['reservation_data']['checkout_date'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="https://i.ibb.co/nmDNrp6/Untitled-design-4.png">
    <title>King's Bed Hotel</title>
    <link rel="stylesheet" href="reservation.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
    .bold-underline {
        font-weight: bold;
        text-decoration: underline;
    }

    .confirmation-container {
    position: relative ;
    top: 70px ;
    max-width: 500px;
    margin: 50px auto;
    padding: 20px;
    background: #f9f9f9;
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    text-align: center;
    font-family: 'Arial', sans-serif;
    }
    .confirmation-container h2 {
        font-size: 24px;
        color: #333;
        margin-bottom: 20px;
        text-transform: uppercase;
        border-bottom: 2px solid #ebd1b5;
        display: inline-block;
        padding-bottom: 5px;
    }
    .confirmation-container p {
        font-size: 16px;
        color: #555;
        margin: 10px 0;
        text-align: left;
    }
    .confirmation-container strong {
        color: #333;
    }
    .confirmation-container button {
        background: #858b86;
        color: white;
        border: none;
        padding: 10px 20px;
        font-size: 16px;
        border-radius: 5px;
        cursor: pointer;
        transition: background 0.3s ease;
        margin: 10px 5px;
    }
    .confirmation-container button:hover {
        background: rgb(203, 207, 200);
    }
    .confirmation-container a button {
        background: rgb(135, 138, 135);
    }
    .confirmation-container a button:hover {
        background: rgb(203, 207, 200);
    }

            /* Modal styles */
            .modal {
            display: <?php echo ($showLoginModal || $showReservationSuccessModal || $showLogoutModal || $showReservationModal) ? 'block' : 'none'; ?>;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
            transition: opacity 0.3s ease-in-out;
        }

        .modal-content p {
            font-size: 18px;
        }

        .modal-content h1 {
            font-size: 20px;
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 40px;
            border: 1px solid #888;
            width: 80%;
            max-width: 500px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.3);
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>


<nav class="navbar">
        <div class="navdiv">
            <div class="logo">
                <a href="index.php"><img src="https://i.ibb.co/FmT02bH/King-s-Bed-Hotel-2500-x-1024-px-4.png" alt="King's Bed Hotel Logo"></a>
            </div>
            <ul>
                <li><a href="index.php">HOME</a></li>
                <li><a href="rooms.php">Rooms</a></li>
                <li><a href="about.php">About</a></li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li><a href="reservation.php">Reservation</a></li>
                    <li><a href="contact.php">Contact</a></li>
                    <li><a href="logout.php" class="logout-btn">Logout</a></li>
                <?php else: ?>
                    <li><a href="?reservation=true">Reservation</a></li>
                    <li><a href="login.php" class="login-btn">Login</a></li>
 <!-- Reservation link when not logged in -->
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <?php if ($showReservationSuccessModal): ?>
        <!-- Success Modal -->
        <div class="modal">
            <div class="modal-content">
            <a href="reservation.php">
                <span class="close">&times;</span>
            </a>
                <h1>Reservation request submitted successfully!</h1><br>
                <p>
                Thank you for your reservation request. We have received your inquiry, and a confirmation email has been sent to  
                <span class="bold-underline"><?php echo $email; ?></span>. Please check your inbox for further details. Additionally, we will contact you shortly for further verification and to finalize your booking. If you have any questions, feel free to reach out to us.  
                <br>
                <br>
                <b style="display: block; margin-top: 20px;">Please note:</b> 
                Our agents are available from 8:00 AM to 8:00 PM. If your inquiry falls outside these hours, we will get back to you as soon as possible during our operating hours.
                </p>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['confirm'])): ?>
        <!-- Confirmation Form -->
        <div class="confirmation-container">
            <h2>Confirm Your Details</h2>
            <p><strong>Name:</strong> <?php echo htmlspecialchars($name); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
            <p><strong>Phone:</strong> <?php echo htmlspecialchars($phone); ?></p>
            <p><strong>Address:</strong> <?php echo htmlspecialchars($address); ?></p>
            <p><strong>Room Type:</strong> <?php echo htmlspecialchars($room_type); ?></p>
            <p><strong>Check-in Date:</strong> <?php echo htmlspecialchars($checkin_date); ?></p>
            <p><strong>Check-out Date:</strong> <?php echo htmlspecialchars($checkout_date); ?></p>

            <form method="POST" action="connect.php" action="email.php">
                <input type="hidden" name="name" value="<?php echo htmlspecialchars($name); ?>">
                <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
                <input type="hidden" name="phone" value="<?php echo htmlspecialchars($phone); ?>">
                <input type="hidden" name="address" value="<?php echo htmlspecialchars($address); ?>">
                <input type="hidden" name="room_type" value="<?php echo htmlspecialchars($room_type); ?>">
                <input type="hidden" name="checkin_date" value="<?php echo htmlspecialchars($checkin_date); ?>">
                <input type="hidden" name="checkout_date" value="<?php echo htmlspecialchars($checkout_date); ?>">
                <button type="submit" name="confirm">Confirm</button>
            </form>
            <a href="reservation.php">
                <button type="button">Go Back</button>
            </a>
        </div>
    <?php else: ?>
        <!-- Reservation Form -->
        <div class="container">
            <form action="reservation.php" method="POST">
                <h2><u>Reserve Now</u></h2>
        
                <input type="text" id="name" name="name" placeholder="Full Name" required value="<?php echo htmlspecialchars($name); ?>">
        
                <div>
                    <input type="email" id="email" name="email" placeholder="Email" required value="<?php echo htmlspecialchars($email); ?>">
                    <input type="tel" id="phone" name="phone" placeholder="Phone number (09123456789)" maxlength="11" pattern="09[0-9]{9}" required value="<?php echo htmlspecialchars($phone); ?>">
                </div>
        
                <input type="text" id="address" name="address" placeholder="Address" required value="<?php echo htmlspecialchars($address); ?>">
        
                <div class="room-type-container">
                    <label for="room-type">Room Type</label>
                    <select id="room-type" name="room_type" required>
                        <option value="simple-room" <?php echo ($room_type == 'simple-room' ? 'selected' : ''); ?>>Simple Room</option>
                        <option value="comfort-room" <?php echo ($room_type == 'comfort-room' ? 'selected' : ''); ?>>Comfort Room</option>
                        <option value="elegant-room" <?php echo ($room_type == 'elegant-room' ? 'selected' : ''); ?>>Elegant Room</option>
                        <option value="luxury-room" <?php echo ($room_type == 'luxury-room' ? 'selected' : ''); ?>>Luxury Room</option>
                        <option value="royal-room" <?php echo ($room_type == 'royal-room' ? 'selected' : ''); ?>>Royal Room</option>
                        <option value="kings-room" <?php echo ($room_type == 'kings-room' ? 'selected' : ''); ?>>King's Room</option>
                    </select>
                </div>
                
                <div class="date-time">

                <div>
                    <label for="checkin-date">Check-in Date</label>
                    <input type="date" id="checkin-date" name="checkin_date" min="2024-12-13" max="2025-01-31"  required value="<?php echo htmlspecialchars($checkin_date ?? date('Y-m-d')); ?>">
                </div>
                <div>
                    <label for="checkout-date">Check-out Date</label>
                    <input type="date" id="checkout-date" name="checkout_date" min="2024-12-14" max="2025-01-31"  required value="<?php echo htmlspecialchars($checkout_date); ?>">
                </div>
                </div>

                <script>
                document.getElementById('checkin-date').addEventListener('change', function() {
                    var checkinDate = new Date(this.value);
                    var checkoutDate = document.getElementById('checkout-date');

                    
                    checkinDate.setDate(checkinDate.getDate() + 1);
                    var minCheckoutDate = checkinDate.toISOString().split('T')[0];

                    
                    checkoutDate.min = minCheckoutDate;
                
                    
                    if (new Date(checkoutDate.value) < new Date(minCheckoutDate)) {
                        checkoutDate.value = minCheckoutDate;
                    }
                });
                </script>

        
                <div>
                    <button type="submit">Reserve Now</button>
                </div>
            </form>
        </div>
    <?php endif; ?>
</body>
</html>
