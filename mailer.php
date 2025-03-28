<?php
// First, install PHPMailer via Composer or download it manually
// Composer: composer require phpmailer/phpmailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // If using Composer
// Or if manual: require 'path/to/PHPMailer/src/Exception.php';
// require 'path/to/PHPMailer/src/PHPMailer.php';
// require 'path/to/PHPMailer/src/SMTP.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $toEmail = "spiralsmoke903@gmail.com";
    $fromEmail = $_POST['email'];
    $message = $_POST['message'];

    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.mailtrap.io'; // Mailtrap SMTP server
        $mail->SMTPAuth = true;
        $mail->Username = 'your-mailtrap-username'; // Get this from Mailtrap
        $mail->Password = 'your-mailtrap-password'; // Get this from Mailtrap
        $mail->SMTPSecure = 'tls';
        $mail->Port = 2525; // Mailtrap port (2525, 465 or 587)

        // Recipients
        $mail->setFrom($fromEmail);
        $mail->addAddress($toEmail);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Test Email from Your App';
        $mail->Body    = nl2br($message);
        $mail->AltBody = $message;

        $mail->send();
        echo "Message has been sent successfully!";
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
?>

<!-- Simple HTML form -->
<!DOCTYPE html>
<html>
<head>
    <title>Email Test</title>
</head>
<body>
    <form method="post" action="">
        <label>Your Email:</label><br>
        <input type="email" name="email" required><br><br>
        
        <label>Message:</label><br>
        <textarea name="message" required></textarea><br><br>
        
        <input type="submit" value="Send Email">
    </form>
</body>
</html>