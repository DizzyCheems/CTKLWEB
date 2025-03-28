<?php
session_start();
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'library_db';

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit;
}

// Create tables if they don't exist
$sql_pdf = "CREATE TABLE IF NOT EXISTS pdf_files (
    id INT AUTO_INCREMENT PRIMARY KEY,
    filename VARCHAR(255) NOT NULL,
    preview_image VARCHAR(255) NULL,
    upload_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
$sql_users = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";
$sql_sessions = "CREATE TABLE IF NOT EXISTS active_sessions (
    session_id VARCHAR(255) PRIMARY KEY,
    user_id INT NOT NULL,
    last_active TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
)";

$pdo->exec($sql_pdf);
$pdo->exec($sql_users);
$pdo->exec($sql_sessions);

// Add last_activity column if it doesn't exist
$check_column = $pdo->query("SHOW COLUMNS FROM users LIKE 'last_activity'");
if ($check_column->rowCount() == 0) {
    $alter_users = "ALTER TABLE users 
        ADD COLUMN last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP";
    $pdo->exec($alter_users);
}

// Insert default admin
$admin_exists = $pdo->query("SELECT COUNT(*) FROM users WHERE username = 'admin'")->fetchColumn();
if (!$admin_exists) {
    $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
    $stmt->execute(['admin', 'admin123', 'admin']);
}

// Manage active sessions
if (isset($_SESSION['user_id'])) {
    $session_id = session_id();
    $user_id = $_SESSION['user_id'];
    
    // Update or insert session
    $stmt = $pdo->prepare("
        INSERT INTO active_sessions (session_id, user_id, last_active) 
        VALUES (?, ?, NOW())
        ON DUPLICATE KEY UPDATE last_active = NOW()
    ");
    $stmt->execute([$session_id, $user_id]);
    
    // Clean up old sessions (older than 15 minutes)
    $pdo->query("DELETE FROM active_sessions WHERE last_active < DATE_SUB(NOW(), INTERVAL 15 MINUTE)");
}
?>