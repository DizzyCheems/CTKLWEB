<?php
include 'config.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - City of Koronadal Public Library</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-image: url('images/books.jpeg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            min-height: 100vh;
        }
        .header-modern {
            background: linear-gradient(90deg, #1a2526 0%, #2c3e50 100%);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        .header-logo {
            height: 100px;
            width: 100px;
        }
        .header-title {
            font-size: 2rem;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        .nav-link {
            font-size: 1.25rem;
            font-weight: 500;
            padding: 0.5rem 1rem;
            transition: color 0.3s ease, transform 0.3s ease;
        }
        .nav-link:hover {
            color: #00ddeb;
            transform: translateY(-2px);
            display: inline-block;
        }
        main .container {
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 10px;
            padding: 20px;
        }
        .accordion-button {
            font-size: 1.25rem;
            font-weight: 600;
        }
        .accordion-body {
            font-size: 1.1rem;
            line-height: 1.6;
        }
        .contact-info {
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 10px;
            padding: 20px;
        }
        footer.bg-dark {
            background-color: #212529 !important;
        }
        .hierarchy-img, .schedule-img {
            max-width: 100%;
            height: auto;
        }
        .collection-img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 8px;
            cursor: pointer; /* Indicates the image is clickable */
            transition: opacity 0.3s ease; /* Smooth fade on hover */
        }
        .collection-img:hover {
            opacity: 0.8; /* Slight fade effect on hover */
        }
        #imageModal .modal-content {
            background: none;
            border: none;
        }
        #imageModal .modal-body {
            padding: 0;
        }
        #imageModal .modal-image {
            width: 100%;
            height: auto;
            max-height: 90vh; /* Limits height to 90% of viewport */
            object-fit: contain; /* Ensures full image is visible */
            opacity: 0; /* Starts hidden for fade-in */
            animation: fadeIn 0.5s forwards; /* Fade-in animation */
        }
        @keyframes fadeIn {
            to {
                opacity: 1;
            }
        }

                /* Contact Info Section */
                .contact-info {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 40px 0;
            text-align: center;
            border-radius: 10px;
            margin: 0 auto 3rem auto;
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
            margin: 3rem auto;
        }

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

        .card {
    background-color: #f9f9f9; /* Light background color */
    border: 1px solid #ddd; /* Light border */
    border-radius: 8px; /* Rounded corners */
    padding: 20px; /* Padding inside the card */
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); /* Subtle shadow */
    max-width: 600px; /* Max width of the card */
    margin: 20px auto; /* Center the card */
}

.section-title {
    font-size: 24px; /* Font size for the title */
    color: #333; /* Dark text color */
    margin: 0; /* Remove default margin */
}

    </style>
</head>
<body>
    <header class="header-modern text-white py-3">
        <div class="container d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <img src="images/logo.png" alt="Library Logo" class="header-logo me-3">
                <h1 class="header-title mb-0">About Us</h1>
            </div>
            <nav>
                <ul class="nav align-items-center">
                    <li class="nav-item"><a href="index.php" class="nav-link text-white">Home</a></li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item"><a href="#" class="nav-link text-white" data-bs-toggle="modal" data-bs-target="#contactModal">Contact</a></li>
                        <li class="nav-item"><a href="<?php echo $_SESSION['role'] === 'admin' ? 'admin.php' : 'user.php'; ?>" class="nav-link text-white">Dashboard</a></li>
                        <li class="nav-item"><a href="logout.php" class="nav-link text-white">Logout</a></li>
                    <?php else: ?>
                        <li class="nav-item"><a href="#" class="nav-link text-white" data-bs-toggle="modal" data-bs-target="#loginModal">Login</a></li>
                        <li class="nav-item"><a href="signup.php" class="nav-link text-white">Sign Up</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container my-5">
        <div class="accordion" id="aboutAccordion">
            <!-- Library History -->
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingHistory">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseHistory" aria-expanded="true" aria-controls="collapseHistory">
                        Library History
                    </button>
                </h2>
                <div id="collapseHistory" class="accordion-collapse collapse show" aria-labelledby="headingHistory" data-bs-parent="#aboutAccordion">
                    <div class="accordion-body">
                        <h3>HISTORY OF THE CITY OF KORONADAL LIBRARY</h3>
                        <p>The City of Koronadal Public Library, formerly The Koronadal Municipal Library (KML), was created since 1968 under the administration of Mayor Gerardo Calaliman. It grows to a functional library for the constituents of Koronadal after a trained librarian named Mr. Roque Sueno from Notre Dame Boys Department, was appointed as Library Custodian. He efficiently managed the library consisting of the basic references such as dictionaries, encyclopedia, handbooks, atlases, and other materials. Those big collection including 1,500 volumes donated by USIS Davao, the Asia Foundation and a private Catholic school in the area were highly beneficial until 1971.</p>
                        <p>The original location of the library was a small room on the ground floor of this old municipal building, now the old City Hall. As to furniture, it only had a long plywood table with two long benches, an office table for the Librarian and two book shelves for the existing collections. In 1970, this library was relocated to the second floor of the public market in order to house a growing collection but in the early part of 1971, it was razed by fire and unluckily the entire collection and records went down with it.</p>
                        <p>During the same year, Notre Dame schools operating in the locality donated books to start a new library, temporarily housed in the Mayor’s Office. When the public market was reconstructed, space was again provided for the library and was in full operation in 1972 when again the public market caught fire.</p>
                        <p>It re-opened and manned by non-librarian personnel with donated books from a private sectarian high school, and some general reference materials solicited by the late Sangguniang Bayan Member Dra. Amparo Y. Pingoy from the United States and other sources.</p>
                        <p>(Source: Master Thesis of Excelsa Laverez, RL, MLIS. Thesis Title: The Koronadal Municipal Library in South Cotabato: It’s History and Development)</p>
                        <p>It was only in the year 2015 (43 long years) when the city government under Mayor, now Congressman Dr. Peter B. Miguel, hired a full-fledged licensed and master librarian in the name of Erwin Historillo Lagustan from the University Libraries of the Notre Dame of Marbel University. Book solicitations were posted on social media sites asking for donations to fill in the library inventory. Luckily, lots of assorted books were received from benevolent donors both local and abroad comprising of award-winning novels, rare history books, general references books, pocket books, story books, and juvenile fictions. However, very limited books from the Philippines or authored by Filipinos were noticed.</p>
                        <p>Aside from the rich collections of books and magazines acquired, The City of Koronadal Public Library is one of the Tech4Ed Centers, now called the DIGITAL LITERACY TRANSFORMATION & LEARNING Center all over the country. It is a continuous recipient of quality computer stations complete with printer, WIFI transmitter, and CCTV camera from DICT or Department of Information & Communications Technology.</p>
                        <p>Additionally, this library was identified by the Central Bank of the Philippines as one of their Knowledge Resource Collection (or KRC) depository of printed materials of assorted kinds and are now available in this library for research purposes.</p>
                        <p>The City of Koronadal Library is formerly located in the third floor of the new City Hall at Room 306, in between the Commission on Audit (COA) and the City Accounting Offices (or CAcO), occupying a very limited single office space, solely managed by a master Librarian and a temporary Clerk, without any utility worker, and far from the heart of the city, hence proposal was conceptualized to convince the City Mayor, Atty. Eliordo U. Ogena, to enter into and sign a memorandum of agreement with the National Library of the Philippines or NLP.</p>
                        <p>In November 3, 2020, Resolution Number 183, Series of 2020 sponsored by then Councilor Prechie Louella G. Ogoy, co-sponsored by Councilors Annabelle G. Pingoy, James M. Lagasca, Gregorio O. Presga, and former SK Federation President Clarisse Mae T. Sorongon was duly approved by our Sangguniang Panglunsod authorizing the City Mayor Atty. Eliordo U. Ogena to enter into and sign a MOA with the National Library of the Philippines (or NLP) for the advancement and development of the City Library.</p>
                        <p>In effect, online MOA signing during the time of lockdowns for the first time in the history of Philippine public libraries, as suggested by the City Information Officer Romar A. Olivares, was executed between the NLP Director and LGU-Koronadal represented by our very own City Mayor Eliordo U. Ogena, paving the way for the fund-sourcing of the Special Project Division or SPD of the Mayor’s Office for the restoration and adaptive reuse of the old municipal building into a modern public city library right at the heart of the city.</p>
                        <p>In the same manner, City Mayor Eliordo U. Ogena issued Executive Order No. 51, Series of 2021 on September 19, 2022 “An Order Creating the City of Koronadal Library Board (CKLB) and Providing Its Composition and Functions” for this purpose. No meeting was called after its issuance to wait for the new set of government officials from the National Elections 2022. After the 2022 peaceful elections and having a new set of councilors, City of Koronadal Library Board starts to convene and the first-ever City Library Board Meeting happens at the former Special Project Unit Conference Room, now called Special Project Division, within the City Mayor’s Office on September 29, 2022. Since then, the City of Koronadal Library Board regularly convene every quarter of the year discussing matters for the advancement and development of a public city library right at the heart of the city.</p>
                        <p>Meanwhile, the Special Project Division (or SPD) of the City Mayor’s Office having a function of Fund Sourcing, luckily acquired grant from the Department of Public Works and Highways (or DPWH) for the restoration of the old and semi-burned vacant building.</p>
                        <p>The long wait was over on August 11, 2023 when the Department of Public Works and Highways (DPWH) turned-over the newly renovated old municipal building to the Local Government of Koronadal, which in turn the latter gave the go signal to the City Library to occupy the building and develop it into a modern public city library.</p>
                        <p>On January 27 and 28, 2024, the City Library started hauling all the accumulated resources to this newly renovated municipal building and started developing and acquiring needed books, equipment, furniture, and fixtures for reopening to the public. After nine months of continuous preparation, acquisition of new books and re-arranging the setup, the City of Koronadal Library Board (or CKLB) finally decides to open it to the public in October 4, 2024, before the Negosyo Festival launches its lined-up activities for the 24th Negosyo Festival in the City of Koronadal.</p>
                    </div>
                </div>
            </div>

            <!-- Hierarchy -->
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingHierarchy">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseHierarchy" aria-expanded="false" aria-controls="collapseHierarchy">
                        Hierarchy
                    </button>
                </h2>
                <div id="collapseHierarchy" class="accordion-collapse collapse" aria-labelledby="headingHierarchy" data-bs-parent="#aboutAccordion">
                    <div class="accordion-body d-flex justify-content-center">
                        <img src="images/personnelchart.png" alt="Library Personnel Chart" class="hierarchy-img">
                    </div>
                </div>
            </div>

            <!-- Mission-Vision -->
            <div class="accordion-item">
                <h2 class="accordion-header" id=" JohanheadingMissionVision">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseMissionVision" aria-expanded="false" aria-controls="collapseMissionVision">
                        Mission-Vision
                    </button>
                </h2>
                <div id="collapseMissionVision" class="accordion-collapse collapse" aria-labelledby="headingMissionVision" data-bs-parent="#aboutAccordion">
                    <div class="accordion-body">
                        <p><strong>Vision:</strong> An essential repository of knowledge and information for the educational, spiritual, cultural, economic, and social development of the people of Koronadal</p>
                        <p><strong>Mission:</strong> To provide opportunities and facilities for children, young people, adults, and senior citizens for their wholesome development.</p>
                    </div>
                </div>
            </div>

            <!-- Library Hours -->
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingHours">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseHours" aria-expanded="false" aria-controls="collapseHours">
                        Library Hours
                    </button>
                </h2>
                <div id="collapseHours" class="accordion-collapse collapse" aria-labelledby="headingHours" data-bs-parent="#aboutAccordion">
                    <div class="accordion-body d-flex justify-content-center">
                        <img src="images/schedules.png" alt="Library Schedule" class="schedule-img">
                    </div>
                </div>
            </div>

            <!-- Collections -->
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingCollections">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCollections" aria-expanded="false" aria-controls="collapseCollections">
                        Collections
                    </button>
                </h2>
                <div id="collapseCollections" class="accordion-collapse collapse" aria-labelledby="headingCollections" data-bs-parent="#aboutAccordion">
                    <div class="accordion-body">
                        <div class="row g-3">
                            <div class="col-6 col-md-4">
                                <img src="images/lib1.jpeg" alt="Library Interior 1" class="collection-img" data-bs-toggle="modal" data-bs-target="#imageModal">
                            </div>
                            <div class="col-6 col-md-4">
                                <img src="images/lib2.jpg" alt="Library Interior 2" class="collection-img" data-bs-toggle="modal" data-bs-target="#imageModal">
                            </div>
                            <div class="col-6 col-md-4">
                                <img src="images/lib3.jpeg" alt="Library Interior 3" class="collection-img" data-bs-toggle="modal" data-bs-target="#imageModal">
                            </div>
                            <div class="col-6 col-md-4">
                                <img src="images/newspapers.jpeg" alt="Newspaper Collection" class="collection-img" data-bs-toggle="modal" data-bs-target="#imageModal">
                            </div>
                            <div class="col-6 col-md-4">
                                <img src="images/lib4.jpeg" alt="Library Interior 4" class="collection-img" data-bs-toggle="modal" data-bs-target="#imageModal">
                            </div>
                            <div class="col-6 col-md-4">
                                <img src="images/inside.jpeg" alt="Library Inside View" class="collection-img" data-bs-toggle="modal" data-bs-target="#imageModal">
                            </div>
                            <div class="col-6 col-md-4">
                                <img src="images/books.jpeg" alt="Book Collection" class="collection-img" data-bs-toggle="modal" data-bs-target="#imageModal">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Image Modal -->
    <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body">
                    <img src="" alt="Enlarged Image" class="modal-image" id="enlargedImage">
                </div>
                <button type="button" class="btn-close position-absolute top-0 end-0 m-2" data-bs-dismiss="modal" aria-label="Close" style="z-index: 1050;"></button>
            </div>
        </div>
    </div>

    <!-- Contact Modal -->
    <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] !== 'admin'): ?>
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
                    <form id="inquiryForm" method="POST" action="submit_inquiry.php">
                        <div class="mb-3">
                            <label for="email" class="form-label">Your Email</label>
                            <?php
                            $stmt = $pdo->prepare("SELECT email FROM users WHERE id = ?");
                            $stmt->execute([$_SESSION['user_id']]);
                            $user_email = $stmt->fetchColumn();
                            if ($user_email === false || $user_email === null) {
                                $user_email = '';
                            }
                            ?>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?php echo htmlspecialchars($user_email ?? ''); ?>" 
                                   <?php echo $user_email !== '' ? 'readonly' : ''; ?> required>
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
    <?php endif; ?>


        <!-- Divider -->
        <div class="divider"></div>

        <!-- Top 5 Free Online Library Databases Section -->
        <section class="container my-5">
 
        <div class="card">
            <h2 class="section-title">Top 5 Free Online Library Databases</h2>
        </div> 
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


    <!-- Contact Us Section -->
    <div class="divider"></div>
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

    <footer class="bg-dark text-white text-center py-3 mt-4">
        <p class="mb-0">© 2025 City of Koronadal Public Library</p>
    </footer>

    <!-- Login Modal -->
    <?php if (!isset($_SESSION['user_id'])): ?>
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
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Add event listener to all collection images
        document.addEventListener('DOMContentLoaded', function () {
            const images = document.querySelectorAll('.collection-img');
            const modalImage = document.getElementById('enlargedImage');

            images.forEach(image => {
                image.addEventListener('click', function () {
                    modalImage.src = this.src; // Set the modal image source to the clicked image
                    modalImage.alt = this.alt; // Update alt text
                });
            });

            // Reset opacity when modal is closed to allow fade-in on next open
            const imageModal = document.getElementById('imageModal');
            imageModal.addEventListener('hidden.bs.modal', function () {
                modalImage.style.opacity = '0';
            });
        });
    </script>
</body>
</html>