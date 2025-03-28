<?php
include 'config.php';

// Check if the user is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

// Handle Mark as Read action
if (isset($_GET['mark_read'])) {
    $inquiry_id = $_GET['mark_read'];

    // Update the open_status to 0 for the specific inquiry
    $stmt = $pdo->prepare("UPDATE inquiry SET open_status = 0 WHERE id = ?");
    $stmt->execute([$inquiry_id]);

    // Redirect back to the notification list after updating the status
    header("Location: notificationlist.php");
    exit;
}

// Query to fetch all unread inquiries (open_status = 1)
$stmt = $pdo->prepare("SELECT * FROM inquiry ORDER BY created_at DESC");
$stmt->execute();
$inquiries = $stmt->fetchAll();
?>
