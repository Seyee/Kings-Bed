<?php 

require '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);

try {
    $name = htmlspecialchars($_POST['name']);
    $message = htmlspecialchars($_POST['message']);
    $email = htmlspecialchars($_POST['email']);

    // Settings
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'kingsbedhotel.help@gmail.com'; //email
    $mail->Password = 'pzld ctos ersh gayg';   // password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    // Recipients
    $mail->setFrom('your-email@gmail.com', 'kingsbedhotel.com');
    $mail->addAddress('kingsbedhotel.help@Gmail.com');

    // Content
    $mail->isHTML(false);
    $mail->Subject = "New Contact Form Submission from $name ";
    $mail->Body = "Name: $name \nEmail: $email\n\nMessage:\n$message";

    $mail->send();
    header('Location: contact.php?status=success');
    exit();
} catch (Exception $e) {
    header('Location: contact.php?status=error');
    exit();
}
?>
