<?php
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php?error=Please log in to send a message");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $message = trim($_POST['message']);
    $sender_id = $_SESSION['user_id'];

    // Basic validation
    if (empty($email) || empty($message)) {
        header("Location: index.php?error=All fields are required");
        exit;
    }

    try {
        // Check current user's email
        $stmt = $pdo->prepare("SELECT email FROM users WHERE id = ?");
        $stmt->execute([$sender_id]);
        $current_email = $stmt->fetchColumn();

        // If email is NULL or empty, update it with the provided email
        if ($current_email === false || $current_email === null || $current_email === '') {
            $stmt = $pdo->prepare("UPDATE users SET email = ? WHERE id = ?");
            $stmt->execute([$email, $sender_id]);
        } elseif ($current_email !== $email) {
            header("Location: index.php?error=Provided email does not match your account");
            exit;
        }

        // Get an admin to send the message to (e.g., the first admin)
        $stmt = $pdo->prepare("SELECT id FROM users WHERE role = 'admin' LIMIT 1");
        $stmt->execute();
        $receiver_id = $stmt->fetchColumn();

        if (!$receiver_id) {
            header("Location: index.php?error=No admin available to receive your message");
            exit;
        }

        // Insert the message into the messages table
        $stmt = $pdo->prepare("INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
        $stmt->execute([$sender_id, $receiver_id, $message]);

        // Redirect to the chatting page after submission
        header("Location: user_notificationlist.php?success=Your message has been sent!");
        exit;

    } catch (PDOException $e) {
        header("Location: index.php?error=Database error occurred: " . $e->getMessage());
        exit;
    }
} else {
    header("Location: index.php");
    exit;
}
?>