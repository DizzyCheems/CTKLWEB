<?php 
include 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: index.php");
    exit;
}

$search_query = isset($_GET['search']) ? $_GET['search'] : '';
$perPage = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $perPage;

if ($search_query) {
    $stmt = $pdo->prepare("SELECT * FROM pdf_files WHERE filename LIKE ? ORDER BY upload_date DESC LIMIT $start, $perPage");
    $stmt->execute(['%' . $search_query . '%']);
    $pdfs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmtTotal = $pdo->prepare("SELECT COUNT(*) FROM pdf_files WHERE filename LIKE ?");
    $stmtTotal->execute(['%' . $search_query . '%']);
    $totalRecords = $stmtTotal->fetchColumn();
} else {
    $stmt = $pdo->prepare("SELECT * FROM pdf_files ORDER BY upload_date DESC LIMIT $start, $perPage");
    $stmt->execute();
    $pdfs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmtTotal = $pdo->query("SELECT COUNT(*) FROM pdf_files");
    $totalRecords = $stmtTotal->fetchColumn();
}

$previewDir = 'uploads/previews/';
$defaultPreview = 'uploads/previews/default_preview.png';
foreach ($pdfs as &$pdf) {
    $pdf['preview'] = ($pdf['preview_image'] && file_exists($pdf['preview_image'])) 
        ? $pdf['preview_image'] 
        : $defaultPreview;
}
unset($pdf);

$totalPages = ceil($totalRecords / $perPage);

// Get count of unread notifications (assuming 'inquiry' should be 'messages' based on your system)
$stmt_notifications = $pdo->prepare("SELECT COUNT(*) FROM messages WHERE receiver_id = ? AND sender_id IN (SELECT id FROM users WHERE role = 'admin') AND id > (SELECT COALESCE(MAX(last_read_message_id), 0) FROM user_notifications WHERE user_id = ?)");
$stmt_notifications->execute([$_SESSION['user_id'], $_SESSION['user_id']]);
$notification_count = $stmt_notifications->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
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
        header.bg-dark {
            background-color: #212529 !important;
        }
        main .container,
        main .card {
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 10px;
        }
        .contact-info {
            background-color: rgba(255, 255, 255, 0.9) !important;
            border-radius: 10px;
        }
        footer.bg-dark {
            background-color: #212529 !important;
        }
        .card-hover {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-10px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }
        .card-img-container {
            position: relative;
            width: 100%;
            padding-top: 141.4%;
            background-color: #f8f9fa;
            overflow: hidden;
        }
        .card-img-top {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 80%;
            height: auto;
            object-fit: contain;
            object-position: center;
        }
        .card-body {
            padding: 1rem;
        }
        .card-footer {
            padding: 0.75rem 1rem;
        }
        #pdfPreviewModal .modal-dialog {
            max-width: 80%;
        }
        #pdfPreviewModal .modal-body {
            padding: 0;
        }
        #pdfIframe {
            width: 100%;
            height: 80vh;
            border: none;
        }
        .fa-bell {
            font-size: 1.5rem;
            transition: transform 0.3s ease;
        }
        .nav-link:hover .fa-bell {
            transform: scale(1.1);
        }
        .badge {
            font-size: 0.7rem;
            padding: 0.25em 0.5em;
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
    </style>
</head>
<body>
    <header class="header-modern text-white py-3">
        <div class="container d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <img src="images/logo.png" alt="Library Logo" class="header-logo me-3">
                <h1 class="header-title mb-0">User Dashboard</h1>   
            </div>
            <nav>
                <ul class="nav align-items-center">
                    <li class="nav-item"><a href="index.php" class="nav-link text-white">Home</a></li>
                    <li class="nav-item"><a href="#" class="nav-link text-white" data-bs-toggle="modal" data-bs-target="#contactModal">Contact</a></li>
                    <li class="nav-item"><a href="logout.php" class="nav-link text-white">Logout</a></li>
                    <li class="nav-item position-relative">
                        <a href="user_notificationlist.php" class="nav-link text-white" title="Notifications">
                            <i class="fas fa-bell"></i>
                            <?php if ($notification_count > 0): ?>
                                <span class="badge bg-danger rounded-pill position-absolute top-0 start-100 translate-middle">
                                    <?php echo $notification_count; ?>
                                </span>
                            <?php endif; ?>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container my-5">
        <section class="card shadow-sm p-4 mb-4">
            <h2 class="h4 mb-3">Search PDFs</h2>
            <form method="get" action="" class="mb-4">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Search by filename..." value="<?php echo htmlspecialchars($search_query); ?>">
                    <button type="submit" class="btn btn-primary">Search</button>
                </div>
            </form>
            
            <h3 class="h5 mb-3">Available PDFs</h3>
            <?php if (empty($pdfs)): ?>
                <p class="text-muted">No PDFs found.</p>
            <?php else: ?>
                <div class="row row-cols-1 row-cols-md-3 g-4">
                <?php foreach ($pdfs as $pdf): ?>
                    <div class="col">
                        <div class="card h-100 shadow-sm card-hover">
                            <div class="card-img-container">
                                <img src="<?php echo htmlspecialchars($pdf['preview']); ?>" class="card-img-top" alt="PDF Cover Page">
                            </div>
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($pdf['filename']); ?></h5>
                                <p class="card-text text-muted small">Uploaded: <?php echo $pdf['upload_date']; ?></p>
                            </div>
                            <div class="card-footer bg-white border-0 d-flex gap-2">
                                <button class="btn btn-outline-primary w-50" data-bs-toggle="modal" data-bs-target="#pdfPreviewModal" data-pdf="uploads/<?php echo htmlspecialchars($pdf['filename']); ?>">View PDF</button>
                                <a href="uploads/<?php echo htmlspecialchars($pdf['filename']); ?>" download class="btn btn-primary w-50">Download</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- Pagination controls -->
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center mt-4">
                    <li class="page-item <?php echo ($page == 1) ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?search=<?php echo urlencode($search_query); ?>&page=<?php echo $page - 1; ?>" tabindex="-1">Previous</a>
                    </li>
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                            <a class="page-link" href="?search=<?php echo urlencode($search_query); ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>
                    <li class="page-item <?php echo ($page == $totalPages) ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?search=<?php echo urlencode($search_query); ?>&page=<?php echo $page + 1; ?>">Next</a>
                    </li>
                </ul>
            </nav>
        </section>
    </main>

    <!-- PDF Preview Modal -->
    <div class="modal fade" id="pdfPreviewModal" tabindex="-1" aria-labelledby="pdfPreviewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="pdfPreviewModalLabel">PDF Preview</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <iframe id="pdfIframe" src="" title="PDF Preview"></iframe>
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
                            // Fetch user's email from the database
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

    <!-- Contact Info Div -->
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Handle PDF preview modal
        const pdfPreviewModal = document.getElementById('pdfPreviewModal');
        pdfPreviewModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const pdfUrl = button.getAttribute('data-pdf');
            const iframe = document.getElementById('pdfIframe');
            iframe.src = pdfUrl;
        });

        pdfPreviewModal.addEventListener('hidden.bs.modal', function () {
            const iframe = document.getElementById('pdfIframe');
            iframe.src = '';
        });
    </script>
</body>
</html>