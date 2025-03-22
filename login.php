<?php
session_start();
include 'config.php';

// Prevent logged-in users from accessing login page
if (isset($_SESSION['user_id'])) {
    header("Location: " . ($_SESSION['role'] === 'admin' ? 'admin.php' : 'user.php'));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Basic input validation
    if (empty($username) || empty($password)) {
        header("Location: index.php?error=All fields are required");
        exit;
    }

    try {
        // Query user by username and password
        $stmt = $pdo->prepare("SELECT id, username, password, role FROM users WHERE username = ? AND password = ?");
        $stmt->execute([$username, $password]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Check if user exists
        if ($user) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'] ?? 'user'; // Default to 'user' if null
            
            // Redirect based on role
            header("Location: " . ($user['role'] === 'admin' ? 'admin.php' : 'user.php'));
            exit;
        } else {
            header("Location: index.php?error=Invalid username or password");
            exit;
        }
    } catch (PDOException $e) {
        header("Location: index.php?error=Database error occurred");
        exit;
    }
} else {
    header("Location: index.php");
    exit;
}
?>