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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .card-hover {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-10px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }
        .card-img-top {
            object-fit: cover;
            height: 200px;
        }
        .card-body {
            padding: 1rem;
        }
        .card-footer {
            padding: 0.75rem 1rem;
        }
        /* Add styling for the PDF modal */
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
    </style>
</head>
<body>
    <header class="bg-dark text-white py-3">
        <div class="container d-flex justify-content-between align-items-center">
            <h1 class="h3 mb-0">User Dashboard</h1>
            <nav>
                <ul class="nav">
                    <li class="nav-item"><a href="index.php" class="nav-link text-white">Home</a></li>
                    <li class="nav-item"><a href="#" class="nav-link text-white" data-bs-toggle="modal" data-bs-target="#contactModal">Contact</a></li>
                    <li class="nav-item"><a href="logout.php" class="nav-link text-white">Logout</a></li>
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
                            <img src="<?php echo htmlspecialchars($pdf['preview']); ?>" class="card-img-top" alt="PDF Cover Page">
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

    <!-- Contact Info Div -->
    <div class="contact-info bg-light py-4">
        <div class="container">
            <h2 class="h4 mb-3">Contact Us</h2>
            <p>If you have any questions or need assistance, please feel free to contact us:</p>
            <ul class="list-unstyled">
                <li class="mb-2">Email: <a href="mailto:koronadal.library@example.com">koronadal.library@example.com</a></li>
                <li class="mb-2">Phone: +63 123 456 7890</li>
                <li class="mb-2">Address: City of Koronadal Public Library, Koronadal City, South Cotabato</li>
            </ul>
        </div>
    </div>

    <footer class="bg-dark text-white text-center py-3 mt-4">
        <p class="mb-0">© 2025 City of Koronadal Public Library</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Handle PDF preview modal
        const pdfPreviewModal = document.getElementById('pdfPreviewModal');
        pdfPreviewModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget; // Button that triggered the modal
            const pdfUrl = button.getAttribute('data-pdf'); // Get the PDF URL
            const iframe = document.getElementById('pdfIframe');
            iframe.src = pdfUrl; // Set the iframe source to the PDF URL
        });

        // Reset iframe src when modal is hidden to prevent memory leaks
        pdfPreviewModal.addEventListener('hidden.bs.modal', function () {
            const iframe = document.getElementById('pdfIframe');
            iframe.src = ''; // Clear the source
        });
    </script>
</body>
</html>