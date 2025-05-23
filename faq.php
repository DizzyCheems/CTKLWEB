<?php 
include 'config.php'; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FAQs - City of Koronadal Public Library</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        /* Basic layout setup */
        html, body {
            height: 100%;
            font-family: 'Poppins', sans-serif;
        }

        body {
            display: flex;
            flex-direction: column;
            background-color: #f4f7fc;
        }

        main {
            flex-grow: 1;
            padding-top: 60px;
        }

        footer {
            margin-top: auto;
        }

        /* Header with Background Image */
        header {
            position: relative;
            background: url('images/bg.jpg') no-repeat center center;
            background-size: cover;
            padding: 20px 0;
            color: white;
            min-height: 100px;
        }

        header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1;
        }

        header .container {
            position: relative;
            z-index: 2;
        }

        .navbar-nav .nav-link {
            color: white !important;
            font-weight: 600;
        }

        .navbar-nav .nav-link:hover {
            color: #f39c12 !important;
        }

        .navbar-brand h1 {
            font-size: 2rem;
            margin: 0;
        }

        /* Main content background image */
        .main-content {
            background-image: url('images/hallway.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            padding: 2rem 0;
            position: relative;
            padding-bottom: 4rem;
        }

        /* FAQ Section */
        .faq-section {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 40px 0;
            text-align: left;
            border-radius: 10px;
            margin: 2rem auto;
            max-width: 800px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .faq-section h2 {
            font-size: 2rem;
            margin-bottom: 20px;
            font-weight: 600;
            text-align: center;
        }

        .faq-section .accordion-button {
            font-size: 1.1rem;
            font-weight: 500;
        }

        .faq-section .accordion-body {
            font-size: 1rem;
            color: #555;
        }

        /* Footer */
        footer {
            background-color: #2c3e50;
            color: white;
            padding: 20px 0;
            text-align: center;
        }

        footer p {
            margin: 0;
        }

        /* Navbar styles */
        .navbar {
            background-color: transparent !important;
            padding: 0.5rem 1rem;
            margin-top: -20px;
            position: relative;
        }

        .navbar-brand {
            font-size: 1.5rem;
            font-weight: bold;
            color: white;
        }

        .nav-link {
            color: white !important;
            transition: transform 0.3s ease, color 0.3s ease;
        }

        .nav-link:hover {
            transform: scale(1.1);
            color: #f0f0f0 !important;
        }

        .header-logo {
            height: 90px;
            width: auto;
            object-fit: contain;
            transition: all 0.3s ease;
        }

        @media (max-width: 768px) {
            .header-logo { height: 80px; }
            .navbar-brand { font-size: 1.2rem; }
        }

        @media (max-width: 576px) {
            .header-logo { height: 60px; }
            .navbar-brand { font-size: 1rem; }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <nav class="navbar navbar-expand-lg">
            <div class="container">
                <div class="d-flex align-items-center">
                    <img src="images/logo.png" alt="Library Logo" class="header-logo me-3">
                    <a class="navbar-brand" href="index.php">City of Koronadal Public Library</a>
                </div>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
                        aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                    <ul class="navbar-nav">
                        <li class="nav-item"><a href="index.php" class="nav-link">Home</a></li>
                        <li class="nav-item"><a href="http://61.245.13.173:7500" class="nav-link text-white">OPAC</a></li>
                        <li class="nav-item"><a href="about_us.php" class="nav-link text-white">About Us</a></li>
                        <li class="nav-item"><a href="faq.php" class="nav-link text-white">FAQs</a></li>
                        <?php if(isset($_SESSION['user_id'])): ?>
                            <li class="nav-item"><a href="#" class="nav-link" data-bs-toggle="modal" data-bs-target="#contactModal">Contact</a></li>
                        <?php endif; ?>
                        <?php if (!isset($_SESSION['user_id'])): ?>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="authDropdown" 
                                   data-bs-toggle="dropdown" aria-expanded="false">Account</a>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="authDropdown">
                                    <li><a class="dropdown-item" href="#" data-bs-toggle="modal" 
                                           data-bs-target="#loginModal">Login</a></li>
                                    <li><a class="dropdown-item" href="signup.php">Sign Up</a></li>
                                </ul>
                            </li>
                        <?php else: ?>
                            <li class="nav-item">
                                <a href="<?php echo (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') ? 'admin.php' : 'user.php'; ?>" 
                                   class="nav-link">Dashboard</a>
                            </li>
                            <li class="nav-item"><a href="logout.php" class="nav-link">Logout</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <!-- Main Content -->
    <main class="main-content">
        <!-- FAQ Section -->
        <section class="faq-section">
            <h2>Frequently Asked Questions</h2>
            <div class="accordion" id="faqAccordion">
                <div class="accordion-item">
                    <h3 class="accordion-header" id="faqHeading1">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" 
                                data-bs-target="#faqCollapse1" aria-expanded="true" aria-controls="faqCollapse1">
                            How do I become a member of the library?
                        </button>
                    </h3>
                    <div id="faqCollapse1" class="accordion-collapse collapse show" aria-labelledby="faqHeading1" 
                         data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            To become a member, click the "Join Now" button on the homepage or visit the "Sign Up" page to create an account. Follow the instructions to complete your registration.
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h3 class="accordion-header" id="faqHeading2">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" 
                                data-bs-target="#faqCollapse2" aria-expanded="false" aria-controls="faqCollapse2">
                            What resources are available at the library?
                        </button>
                    </h3>
                    <div id="faqCollapse2" class="accordion-collapse collapse" aria-labelledby="faqHeading2" 
                         data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            We offer a wide range of digital resources, books, and documents for all ages and interests. You can access our Online Public Access Catalog (OPAC) to explore our collection.
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h3 class="accordion-header" id="faqHeading3">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" 
                                data-bs-target="#faqCollapse3" aria-expanded="false" aria-controls="faqCollapse3">
                            How can I contact the library for assistance?
                        </button>
                    </h3>
                    <div id="faqCollapse3" class="accordion-collapse collapse" aria-labelledby="faqHeading3" 
                         data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            You can reach us via email at <a href="mailto:city.of.koronadal.library@gmail.com">city.of.koronadal.library@gmail.com</a>, call us at (083) 825 5503, or visit our Contact Us section for more details.
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h3 class="accordion-header" id="faqHeading4">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" 
                                data-bs-target="#faqCollapse4" aria-expanded="false" aria-controls="faqCollapse4">
                            Are there any fees for using the library?
                        </button>
                    </h3>
                    <div id="faqCollapse4" class="accordion-collapse collapse" aria-labelledby="faqHeading4" 
                         data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Membership and access to most of our resources are free. Some services may have minimal fees; please contact us for specific details.
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Login Modal -->
    <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="loginModalLabel">Login</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="login.php" method="POST">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Login</button>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Contact Modal -->
    <div class="modal fade" id="contactModal" tabindex="-1" aria-labelledby="contactModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="contactModalLabel">Contact Us</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Email: <a href="mailto:city.of.koronadal.library@gmail.com">city.of.koronadal.library@gmail.com</a></p>
                    <p>Phone: (083) 825 5503</p>
                    <p>Address: Old City Hall Building, Gensan Drive corner Morales Avenue (Roundball), Poblacion Zone II, Koronadal, Philippines, 9506</p>
                    <p>Facebook: <a href="https://www.facebook.com/KorCityLib" target="_blank">KorCityLib</a></p>
                    <form id="inquiryForm" method="POST" action="submit_inquiry.php">
                        <div class="mb-3">
                            <label for="email" class="form-label">Your Email</label>
                            <?php
                            // Fetch user's email from the database if logged in
                            if (isset($_SESSION['user_id'])) {
                                $stmt = $pdo->prepare("SELECT email FROM users WHERE id = ?");
                                $stmt->execute([$_SESSION['user_id']]);
                                $user_email = $stmt->fetchColumn();
                                // If email is NULL, allow user to input it
                                if ($user_email === false || $user_email === null) {
                                    $user_email = '';
                                }
                            }
                            ?>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?php echo htmlspecialchars($user_email ?? ''); ?>" 
                                   <?php echo isset($_SESSION['user_id']) && $user_email !== '' ? 'readonly' : ''; ?> required>
                        </div>
                        <div class="mb-3">
                            <label for="message" class="form-label">Your Message</label>
                            <textarea class="form-control" id="message" name="message" rows="3" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Submit Inquiry</button>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <p>© 2025 City of Koronadal Public Library. All rights reserved.</p>
    </footer>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>