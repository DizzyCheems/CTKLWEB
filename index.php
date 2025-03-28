<?php include 'config.php'; ?>
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
            background-color: #f4f7fc; /* Soft background for the page */
        }

        main {
            flex-grow: 1;
            padding-top: 60px; /* Ensure content is not hidden behind fixed navbar */
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
            min-height: 100px; /* Ensure header has some height */
        }

        header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5); /* Overlay for readability */
            z-index: 1;
        }

        header .container {
            position: relative;
            z-index: 2; /* Ensure content is above overlay */
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

        /* About Section */
        .about-section {
            background-color: white;
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

        /* Contact Info Section */
        .contact-info {
            background-color: #ecf0f1;
            padding: 40px 0;
            text-align: center;
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

        /* General Style for Nav Links */
        .nav-link {
            color: white !important;  /* Make text color white */
            transition: transform 0.3s ease, color 0.3s ease;  /* Smooth transition for hover effect */
        }

        /* Hover Effect: Popping effect on hover */
        .nav-link:hover {
            transform: scale(1.1);  /* Slightly scale the link to make it "pop" */
            color: #f0f0f0;  /* Slight change in color to make it stand out (optional) */
        }

        /* Optional: Style for the dropdown toggle */
        .navbar-nav .nav-item.dropdown .nav-link {
            color: white !important;  /* Ensures the dropdown toggle has white text */
        }

        /* Dropdown menu items */
        .dropdown-menu .dropdown-item {
            color: black !important;  /* Optional: Make the dropdown items more readable */
            transition: background-color 0.3s ease;
        }

        /* Hover effect for dropdown items */
        .dropdown-menu .dropdown-item:hover {
            background-color: #f8f9fa;  /* Light background on hover for dropdown items */
            color: #333;  /* Darker text color when hovered */
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <div class="container d-flex justify-content-between align-items-center">
            <a class="navbar-brand" href="index.php"><h1>City of Koronadal Public Library</h1></a>
            <nav>
                <!--OPAC LIBRARY 215.119.1.190-->
                <ul class="nav">
                <li class="nav-item"><a href="index.php" class="nav-link">Home</a></li>
                <li class="nav-item"><a href="http://215.119.1.190" class="nav-link" onclick="return checkLibraryAccess()">OPAC</a></li>

<script>
function checkLibraryAccess() {
    // This is a basic check - in a real implementation, you'd need server-side validation
    const allowedIPRange = '215.119.1.'; // Koronadal City Public Library IP range
    const currentIP = window.location.hostname; // This is just an example, actual IP detection needs server-side
    
    if (!currentIP.startsWith(allowedIPRange)) {
        document.body.innerHTML = '<h1>This Feature is Only Available at the Koronadal City Public Library</h1>';
        return false; // Prevents the link from being followed
    }
    return true; // Allows the link to be followed
}
</script>               
               
                <li class="nav-item"><a href="#" class="nav-link" data-bs-toggle="modal" data-bs-target="#contactModal">Contact</a></li>
                    <?php if (!isset($_SESSION['user_id'])): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="authDropdown" data-bs-toggle="dropdown">Account</a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="authDropdown">
                                <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#loginModal">Login</a></li>
                                <li><a class="dropdown-item" href="signup.php">Sign Up</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item"><a href="user.php" class="nav-link">Dashboard</a></li>
                        <li class="nav-item"><a href="logout.php" class="nav-link">Logout</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>
    
    <!-- Hero Section -->
    <div class="hero-section">
        <div class="overlay"></div>
        <h1>Welcome to the City of Koronadal Public Library</h1>
        <p>Your gateway to a wealth of knowledge and digital resources.</p>
        <a href="signup.php" class="btn-cta">Join Now</a>
    </div>
    
    <!-- Main Content -->
    <main>
        <!-- Check for success message in the URL -->
<?php if (isset($_GET['success'])): ?>
    <div id="successMessage" class="alert alert-success" role="alert">
        <?php echo htmlspecialchars($_GET['success']); ?>
    </div>

    <script>
        // Set a timer to hide the success message after 2 seconds
        setTimeout(function() {
            var successMessage = document.getElementById('successMessage');
            if (successMessage) {
                successMessage.style.display = 'none';
            }
        }, 2000);  // 2000 milliseconds = 2 seconds
    </script>
<?php endif; ?>
        <!-- About Section -->
        <section class="about-section">
            <h2>About Our Library</h2>
            <p>We provide a wide range of digital resources, books, and documents for all ages and interests. Whether you're a student, professional, or a hobbyist, our library is the perfect place for you to expand your knowledge.</p>
        </section>

        <!-- Contact Info Section -->
        <div class="contact-info">
            <h2>Contact Us</h2>
            <p>If you have any questions or need assistance, please feel free to contact us:</p>
            <ul>
                <li>Email: <a href="mailto:koronadal.library@example.com">koronadal.library@example.com</a></li>
                <li>Phone: +63 123 456 7890</li>
                <li>Address: City of Koronadal Public Library, Koronadal City, South Cotabato</li>
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
                <!--<?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo htmlspecialchars($_GET['error']); ?>
                    </div>
                <?php endif; ?>
                <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success" role="alert">
                        <?php echo htmlspecialchars($_GET['success']); ?>
                    </div>
                <?php endif; ?>-->
                
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
                <p>Email: <a href="mailto:koronadal.library@example.com">koronadal.library@example.com</a></p>
                <p>Phone: +63 123 456 7890</p>
                <p>Address: City of Koronadal Public Library, Koronadal City, South Cotabato</p>
                
                <!-- Inquiry Form -->
                <form id="inquiryForm" method="POST" action="submit_inquiry.php">
                    <div class="mb-3">
                        <label for="email" class="form-label">Your Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
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