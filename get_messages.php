<?php
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    exit();
}

$sender_id = $_SESSION['user_id'];
$receiver_id = $_POST['receiver_id'];
$last_id = $_POST['last_id'];

$stmt = $pdo->prepare(
    "SELECT * FROM messages 
    WHERE id > ? AND 
    ((sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?))
    ORDER BY timestamp ASC"
);
$stmt->execute([$last_id, $sender_id, $receiver_id, $receiver_id, $sender_id]);
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($messages);
?>