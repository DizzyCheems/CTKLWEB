<?php
include 'config.php';

if (isset($_SESSION['user_id'])) {
    $session_id = session_id();
    $stmt = $pdo->prepare("DELETE FROM active_sessions WHERE session_id = ?");
    $stmt->execute([$session_id]);
}

session_destroy();
header("Location: index.php");
exit;
?>