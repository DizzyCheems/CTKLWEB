<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $password_repeat = $_POST['password_repeat'];

    // Basic validation
    if (empty($email) || empty($password) || empty($password_repeat)) {
        $error = "All fields are required";
    } elseif ($password !== $password_repeat) {
        $error = "Passwords do not match";
    } else {
        // Check if email already exists
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
        $stmt->execute([$email]);
        if ($stmt->fetchColumn() > 0) {
            $error = "Email already registered";
        } else {
            // Hash the password and insert new user
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, 'user')");
            $stmt->execute([$email, $hashed_password]);
            header("Location: index.php?signup_success=You have successfully registered! You can now login.");
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - City of Koronadal Public Library</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        html, body { height: 100%; margin: 0; }
        body {
            display: flex;
            flex-direction: column;
            background-image: url('images/facade.jpeg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
        }
        main { flex-grow: 1; }
        footer { margin-top: auto; }
        header.bg-dark, footer.bg-dark { background-color: #212529 !important; }
        .card {
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        .form-label { color: #333; }
        .form-control { border-color: #ced4da; background-color: #fff; }
        .form-control:focus { border-color: #007bff; box-shadow: 0 0 0 0.25rem rgba(0, 123, 255, 0.25); }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
            transition: background-color 0.3s ease, border-color 0.3s ease;
        }
        .btn-primary:hover { background-color: #0056b3; border-color: #004085; }
        .card a { color: #007bff; text-decoration: none; }
        .card a:hover { text-decoration: underline; }
        .alert { background-color: rgba(255, 255, 255, 0.9); border-radius: 5px; }
        .header-logo { height: 100px; width: 100px; }
    </style>
</head>
<body>
    <header class="bg-dark text-white py-3">
        <div class="container d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <img src="images/logo.png" alt="Library Logo" class="header-logo me-3">
                <h1 class="header-title mb-0">City of Koronadal Public Library</h1>
            </div>
            <nav>
                <ul class="nav">
                    <li class="nav-item"><a href="index.php" class="nav-link text-white">Home</a></li>
                </ul>
            </nav>
        </div>
    </header>    
    <main class="container my-5">
        <section class="card shadow-sm p-4 mx-auto" style="max-width: 400px;">
            <h2 class="h4 mb-3 text-center">Sign Up</h2>
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            <form action="signup.php" method="post">
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" name="email" id="email" class="form-control" placeholder="Enter your email" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" name="password" id="password" class="form-control" placeholder="Enter your password" required>
                </div>
                <div class="mb-3">
                    <label for="password_repeat" class="form-label">Confirm Password</label>
                    <input type="password" name="password_repeat" id="password_repeat" class="form-control" placeholder="Confirm your password" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Sign Up</button>
            </form>
            <p class="text-center mt-3">Already have an account? <a href="index.php">Login here</a></p>
        </section>
    </main>
    <footer class="bg-dark text-white text-center py-3 mt-4">
        <p class="mb-0">© 2025 City of Koronadal Public Library</p>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>