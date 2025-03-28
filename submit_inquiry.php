<?php
// Include the database connection from config.php
include('config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate user input
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $message = htmlspecialchars($_POST['message']);  // To prevent XSS attacks

    // Check if the email is valid
    if (filter_var($email, FILTER_VALIDATE_EMAIL) && !empty($message)) {
        try {
            // Prepare the SQL statement for insertion
            $stmt = $pdo->prepare("INSERT INTO inquiry (email, message) VALUES (?, ?)");
            // Execute the query with the sanitized user input
            $stmt->execute([$email, $message]);

            // Redirect to index.php with a success message
            header('Location: index.php?success=Inquiry%20submitted%20successfully.');
            exit;  // Ensure no further code is executed after the redirect
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    } else {
        echo "Please provide a valid email and a message.";
    }
}
?>
