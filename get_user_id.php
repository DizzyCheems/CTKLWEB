<?php
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    exit();
}

$username = $_POST['username'];
$stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? AND role != 'admin'");
$stmt->execute([$username]);
$user_id = $stmt->fetchColumn();

echo $user_id;
?>