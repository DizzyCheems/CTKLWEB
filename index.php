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
<style>
    .header-logo {
        height: 50px; /* Matches the h3 font size for balance */
        width: 60px; /* Maintains aspect ratio */
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
                    <li class="nav-item">
                        <a href="index.php" class="nav-link">Home</a>
                    </li>
                    <li class="nav-item">
                    <li class="nav-item"><a href="http://215.119.1.190" class="nav-link">OPAC</a></li>

                </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link" data-bs-toggle="modal" data-bs-target="#contactModal">Contact</a>
                    </li>
                    
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
                        <li class="nav-item">
                            <a href="logout.php" class="nav-link">Logout</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <style>
/* Updated CSS with 110px logo height and header position adjustment */
.navbar {
    background-color: transparent !important;
    padding: 0.5rem 1rem;    /* Reduced top padding for better balance */
    margin-top: -20px;      /* Moves the entire header up */
    position: relative;     /* Ensures proper positioning context */
}

.navbar-brand {
    font-size: 1.5rem;
    font-weight: bold;
    color: white;
}

.nav-link {
    color: white;
}

.nav-link:hover {
    color: #555;
}

.header-logo {
    height: 90px;         /* Maintained requested 110px */
    width: auto;          /* Maintains aspect ratio */
    object-fit: contain;  /* Prevents deformation */
    transition: all 0.3s ease;
}

@media (max-width: 768px) {
    .navbar-brand {
        font-size: 1.2rem;
    }
    
    .header-logo {
        height: 80px;     /* Reduced proportionally for tablets */
    }
    
    .navbar-collapse {
        padding-top: 1rem;
    }
}

@media (max-width: 576px) {
    .navbar-brand {
        font-size: 1rem;
    }
    
    .header-logo {
        height: 60px;     /* Reduced proportionally for mobile */
    }
}
</style>
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
        <div class="about-content">
            <h2>About Our Library</h2>
            <p>We provide a wide range of digital resources, books, and documents for all ages and interests. Whether you're a student, professional, or a hobbyist, our library is the perfect place for you to expand your knowledge.</p>
        </div>
    </section>

    <!-- Divider and Contact Info Section -->
    <div class="divider"></div> <!-- Divider before Contact Us -->
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

<style>
    /* Main content background image */
    .main-content {
        background-image: url('images/hallway.jpg');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        background-attachment: fixed; /* Optional: Makes the background fixed while scrolling */
        padding: 2rem 0; /* Add padding to the top and bottom */
        min-height: 100vh; /* Ensure it takes up the full viewport height */
    }

    /* About Section - Full width */
    .about-section {
        width: 100%; /* Full width of the parent container */
        margin: 0; /* Remove any default margins */
        padding: 2rem 0; /* Add vertical padding */
        background-color: rgba(255, 255, 255, 0.9); /* Keep the semi-transparent background */
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1); /* Subtle shadow for depth */
    }

    /* Content inside About Section - Constrain width for readability */
    .about-section .about-content {
        max-width: 1200px; /* Wider content area, but still constrained for readability */
        margin: 0 auto; /* Center the content */
        padding: 0 1rem; /* Add some padding on the sides for smaller screens */
    }

    .about-section h2 {
        font-size: 2rem;
        margin-bottom: 1rem;
        color: #333;
    }
    .about-section p {
        font-size: 1.1rem;
        color: #555;
        line-height: 1.6;
    }

    /* Divider before Contact Us */
    .divider {
        width: 80%; /* Slightly narrower than full width for a refined look */
        height: 2px; /* Thin divider */
        background-color: #ccc; /* Light gray divider */
        margin: 3rem auto; /* Center the divider with extra spacing to push Contact Us lower */
    }

    /* Contact Info Section - Positioned lower */
    .main-content .contact-info {
        background-color: rgba(255, 255, 255, 0.9); /* Semi-transparent white background */
        border-radius: 10px; /* Rounded corners */
        padding: 1.5rem; /* Padding inside the section */
        margin: 0 auto 3rem auto; /* Center the section with extra bottom margin */
        max-width: 800px; /* Limit the width for better readability */
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1); /* Subtle shadow for depth */
    }

    /* Style for the contact info section */
    .contact-info h2 {
        font-size: 2rem;
        margin-bottom: 1rem;
        color: #333;
    }
    .contact-info p {
        font-size: 1.1rem;
        color: #555;
        margin-bottom: 1rem;
    }
    .contact-info ul {
        list-style: none;
        padding: 0;
    }
    .contact-info ul li {
        font-size: 1.1rem;
        color: #555;
        margin-bottom: 0.5rem;
    }
    .contact-info ul li a {
        color: #007bff;
        text-decoration: none;
    }
    .contact-info ul li a:hover {
        text-decoration: underline;
    }

    /* Ensure the success message alert matches the style */
    .main-content .alert {
        max-width: 800px;
        margin: 1rem auto;
        font-size: 1.1rem;
        background-color: rgba(255, 255, 255, 0.9); /* Match the semi-transparent background */
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }
</style>

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