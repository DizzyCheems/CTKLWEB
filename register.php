<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Basic validation
    if (empty($email) || empty($password)) {
        header("Location: index.php?signup_error=All fields are required");
        exit;
    }

    // Check if email already exists
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
    $stmt->execute([$email]);
    if ($stmt->fetchColumn() > 0) {
        header("Location: index.php?signup_error=Email already registered");
        exit;
    }

    // Insert new user (assuming email as username)
    $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, 'user')");
    $stmt->execute([$email, $password]); // Note: In production, hash the password with password_hash()

    header("Location: index.php?signup_success=Registration successful! Please login.");
    exit;
}
?>