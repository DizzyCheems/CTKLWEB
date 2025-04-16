<?php 
include 'config.php'; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>City of Koronadal Public Library</title>
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

        /* Hero Section */
        .hero-section {
            position: relative;
            background: url('images/roundball.png') no-repeat center center;
            background-size: cover;
            padding: 100px 0;
            color: white;
            text-align: center;
            min-height: 400px;
        }

        .hero-section .overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.4);
        }

        .hero-section h1 {
            font-size: 3rem;
            font-weight: 600;
            margin-bottom: 20px;
            z-index: 2;
            position: relative;
        }

        .hero-section p {
            font-size: 1.25rem;
            margin-bottom: 40px;
            z-index: 2;
            position: relative;
        }

        .btn-cta {
            background-color: #f39c12;
            color: white;
            font-weight: 600;
            border: none;
            padding: 12px 25px;
            border-radius: 5px;
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: background-color 0.3s ease;
            z-index: 2;
            position: relative;
        }

        .btn-cta:hover {
            background-color: #e67e22;
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
            padding-bottom: 4rem; /* Added to ensure space before footer */
        }

        /* Success Message Styling */
        .success-message {
            max-width: 600px;
            margin: 1rem auto;
            padding: 1rem 1.5rem;
            font-size: 1.1rem;
            background-color: rgba(40, 167, 69, 0.95);
            color: white;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            text-align: center;
            position: fixed;
            top: 80px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1000;
            animation: fadeInOut 2s ease forwards;
        }

        @keyframes fadeInOut {
            0% { opacity: 0; transform: translate(-50%, -20px); }
            20% { opacity: 1; transform: translate(-50%, 0); }
            80% { opacity: 1; transform: translate(-50%, 0); }
            100% { opacity: 0; transform: translate(-50%, -20px); }
        }

        /* About Section */
        .about-section {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 40px 0;
            text-align: center;
        }

        .about-section h2 {
            font-weight: 600;
            font-size: 2.5rem;
            margin-bottom: 20px;
        }

        .about-section p {
            font-size: 1.15rem;
            line-height: 1.6;
            color: #555;
        }

        /* FAQ Section */
        .faq-section {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 40px 0;
            text-align: left;
            border-radius: 10px;
            margin: 0 auto 2rem auto;
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

        /* Contact Info Section */
        .contact-info {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 40px 0;
            text-align: center;
            border-radius: 10px;
            margin: 0 auto 2rem auto;
            max-width: 800px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .contact-info h2 {
            font-size: 2rem;
            margin-bottom: 20px;
            font-weight: 600;
        }

        .contact-info ul {
            list-style-type: none;
            padding: 0;
        }

        .contact-info ul li {
            font-size: 1.1rem;
            margin-bottom: 10px;
            color: #555;
        }

        .contact-info ul li a {
            color: #3498db;
            text-decoration: none;
        }

        .contact-info ul li a:hover {
            text-decoration: underline;
        }

        /* Divider */
        .divider {
            width: 80%;
            height: 2px;
            background-color: #ccc;
            margin: 2rem auto;
        }

        /* Modal */
        .modal-header {
            background-color: #2c3e50;
            color: white;
        }

        .modal-footer {
            background-color: #f4f7fc;
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

<style>
        /* Additional styling for new sections */
        .section-title {
            font-size: 2rem;
            font-weight: 600;
            margin-bottom: 20px;
            color: white;
        }

        .updates-list, .databases-list {
            list-style-type: none;
            padding: 0;
        }

        .updates-list li, .databases-list li {
            margin-bottom: 10px;
            padding: 15px;
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .updates-list li h5, .databases-list li h5 {
            margin: 0;
            font-size: 1.25rem;
            font-weight: 500;
        }

        .updates-list li small, .databases-list li p {
            color: #555;
        }

        .databases-list li a {
            color: #3498db;
            text-decoration: none;
        }

        .databases-list li a:hover {
            text-decoration: underline;
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
    
    <!-- Hero Section -->
    <div class="hero-section">
        <div class="overlay"></div>
        <h1>Welcome to the City of Koronadal Public Library</h1>
        <p>Your gateway to a wealth of knowledge and digital resources.</p>
        <a href="signup.php" class="btn-cta">Join Now</a>
    </div>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Check for success message in the URL -->
        <?php if (isset($_GET['signup_success'])): ?>
            <div id="successMessage" class="success-message" role="alert">
                <?php echo htmlspecialchars($_GET['signup_success']); ?>
            </div>
        <?php endif; ?>

        <!-- Divider before FAQ -->
        <div class="divider"></div>

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



        <!-- What's New in the Library Section -->
        <section class="container my-5">
            <h2 class="section-title">What's New in the Library</h2>
            <ul class="updates-list">
                <?php if (!empty($libraryUpdates)): ?>
                    <?php foreach ($libraryUpdates as $update): ?>
                        <li>
                            <h5><?php echo htmlspecialchars($update['title']); ?></h5>
                            <small><?php echo date('F j, Y', strtotime($update['date'])); ?></small>
                            <p><?php echo htmlspecialchars($update['description']); ?></p>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li>No updates available at the moment. Please check back later!</li>
                <?php endif; ?>
            </ul>
        </section>

        <!-- Divider -->
        <div class="divider"></div>

<!-- Top 5 Free Online Library Databases Section -->
<section class="container my-5">
    <h2 class="section-title">Top 5 Free Online Library Databases</h2>
    <ul class="databases-list">
        <li>
            <h5><a href="https://www.worldcat.org/" target="_blank">WorldCat</a></h5>
            <p>A global catalog of library collections, providing access to millions of books, articles, and multimedia resources.</p>
        </li>
        <li>
            <h5><a href="https://www.doaj.org/" target="_blank">Directory of Open Access Journals (DOAJ)</a></h5>
            <p>A community-curated online directory that indexes and provides access to high-quality, open-access, peer-reviewed journals.</p>
        </li>
        <li>
            <h5><a href="https://eric.ed.gov/" target="_blank">ERIC (Education Resources Information Center)</a></h5>
            <p>A digital library of education-related resources, including research papers, articles, and educational material.</p>
        </li>
        <li>
            <h5><a href="https://www.proquest.com/" target="_blank">ProQuest</a></h5>
            <p>A platform offering access to a vast collection of dissertations, theses, and academic research across various disciplines.</p>
        </li>
        <li>
            <h5><a href="https://www.gutenberg.org/" target="_blank">Project Gutenberg</a></h5>
            <p>An extensive collection of free eBooks, including classic literature and works in the public domain.</p>
        </li>
    </ul>
</section>

        <!-- Divider before Contact -->
        <div class="divider"></div>

        <!-- Contact Info Section -->
        <div class="contact-info">
            <h2>Contact Us</h2>
            <p>If you have any questions or need assistance, please feel free to contact us:</p>
            <ul>
                <li>Email: <a href="mailto:city.of.koronadal.library@gmail.com">city.of.koronadal.library@gmail.com</a></li>
                <li>Phone: (083) 825 5503</li>
                <li>Address: Old City Hall Building, Gensan Drive corner Morales Avenue (Roundball), Poblacion Zone II, Koronadal, Philippines, 9506</li>
                <li>Facebook: <a href="https://www.facebook.com/KorCityLib" target="_blank">KorCityLib</a></li>
            </ul>
        </div>
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const successMessage = document.getElementById('successMessage');
            if (successMessage) {
                setTimeout(() => {
                    successMessage.style.display = 'none';
                }, 2000); // Hide after 2 seconds
            }
        });
    </script>


</body>
</html>