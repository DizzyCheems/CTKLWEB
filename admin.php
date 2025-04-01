<?php 
include 'config.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

// Initialize variables for SweetAlert
$alert = null;

// Handle PDF Upload (Create)
if (isset($_POST['upload'])) {
    $target_dir = "uploads/";
    $preview_dir = "uploads/previews/";
    if (!file_exists($target_dir)) mkdir($target_dir, 0777, true);
    if (!file_exists($preview_dir)) mkdir($preview_dir, 0777, true);

    $filename = basename($_FILES["pdfFile"]["name"]);
    $target_file = $target_dir . $filename;
    $preview_filename = pathinfo($filename, PATHINFO_FILENAME) . '.png';
    $preview_file = $preview_dir . $preview_filename;

    if (move_uploaded_file($_FILES["pdfFile"]["tmp_name"], $target_file)) {
        $stmt = $pdo->prepare("INSERT INTO pdf_files (filename, preview_image) VALUES (?, ?)");
        $stmt->execute([$filename, null]);
        $message = "File uploaded successfully! Generating preview...";

        if (isset($_POST['previewImage'])) {
            $previewData = $_POST['previewImage'];
            $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $previewData));
            file_put_contents($preview_file, $imageData);

            $stmt = $pdo->prepare("UPDATE pdf_files SET preview_image = ? WHERE filename = ?");
            $stmt->execute([$preview_file, $filename]);
            $message = "File and preview uploaded successfully!";
        }
        $alert = ['type' => 'success', 'message' => $message];
    } else {
        $message = "Error uploading file.";
        $alert = ['type' => 'error', 'message' => $message];
    }
}

// Handle PDF Delete (Delete)
if (isset($_POST['delete'])) {
    $id = $_POST['id'];
    $stmt = $pdo->prepare("SELECT filename, preview_image FROM pdf_files WHERE id = ?");
    $stmt->execute([$id]);
    $file = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($file) {
        $pdf_path = "uploads/" . $file['filename'];
        $preview_path = $file['preview_image'];

        // Delete the files from the server
        if (file_exists($pdf_path)) {
            unlink($pdf_path);
        }
        if ($preview_path && file_exists($preview_path)) {
            unlink($preview_path);
        }

        // Delete the record from the database
        $stmt = $pdo->prepare("DELETE FROM pdf_files WHERE id = ?");
        $stmt->execute([$id]);
        $alert = ['type' => 'success', 'message' => 'File deleted successfully!'];
    } else {
        $alert = ['type' => 'error', 'message' => 'Error: File not found.'];
    }
}

// Handle PDF Update (Update)
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $new_filename = $_POST['new_filename'];

    // Sanitize the new filename to avoid issues
    $new_filename = preg_replace('/[^A-Za-z0-9\-\_\.]/', '', $new_filename);
    if (pathinfo($new_filename, PATHINFO_EXTENSION) !== 'pdf') {
        $new_filename .= '.pdf';
    }

    $stmt = $pdo->prepare("SELECT filename, preview_image FROM pdf_files WHERE id = ?");
    $stmt->execute([$id]);
    $file = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($file) {
        $old_pdf_path = "uploads/" . $file['filename'];
        $new_pdf_path = "uploads/" . $new_filename;
        $old_preview_path = $file['preview_image'];
        $new_preview_filename = pathinfo($new_filename, PATHINFO_FILENAME) . '.png';
        $new_preview_path = "uploads/previews/" . $new_preview_filename;

        // Rename the PDF file
        if (file_exists($old_pdf_path)) {
            rename($old_pdf_path, $new_pdf_path);
        }

        // Rename the preview image if it exists
        if ($old_preview_path && file_exists($old_preview_path)) {
            rename($old_preview_path, $new_preview_path);
        }

        // Update the database
        $stmt = $pdo->prepare("UPDATE pdf_files SET filename = ?, preview_image = ? WHERE id = ?");
        $stmt->execute([$new_filename, $new_preview_path, $id]);
        $alert = ['type' => 'success', 'message' => 'File updated successfully!'];
    } else {
        $alert = ['type' => 'error', 'message' => 'Error: File not found.'];
    }
}

// Get counts for dashboard cards
$userCount = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$pdfCount = $pdo->query("SELECT COUNT(*) FROM pdf_files")->fetchColumn();
$loggedInCount = $pdo->query("SELECT COUNT(*) FROM active_sessions WHERE last_active > DATE_SUB(NOW(), INTERVAL 15 MINUTE)")->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.min.js"></script>
    <!-- Include SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            background-image: url('images/hallway.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            min-height: 100vh;
        }
        /* Apply semi-transparent background to main content areas only */
        main .container,
        main .card {
            background-color: rgba(255, 255, 255, 0.9); /* Semi-transparent white background */
            border-radius: 10px;
        }
        /* Apply semi-transparent background to the container in the dashboard cards section */
        .dashboard-cards .container {
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 10px;
        }
        /* Ensure the footer remains solid dark */
        footer.bg-dark {
            background-color: #212529 !important; /* Bootstrap's bg-dark color */
        }
        /* Header styles */
        .header-modern {
            background: linear-gradient(90deg, #1a2526 0%, #2c3e50 100%); /* Modern gradient background */
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3); /* Subtle shadow for depth */
            position: sticky; /* Makes the header sticky */
            top: 0;
            z-index: 1000;
        }
        .header-logo {
            height: 2.5rem; /* Matches the larger text size */
            width: auto; /* Maintains aspect ratio */
        }
        .header-title {
            font-size: 2rem; /* Larger title */
            font-weight: 600; /* Bold for a modern look */
            letter-spacing: 0.5px; /* Slight spacing for readability */
        }
        .nav-link {
            font-size: 1.25rem; /* Larger navigation links */
            font-weight: 500; /* Medium weight for a modern feel */
            padding: 0.5rem 1rem; /* More padding for better click area */
            transition: color 0.3s ease, transform 0.3s ease; /* Smooth hover effects */
        }
        .nav-link:hover {
            color: #00ddeb; /* Modern cyan hover color */
            transform: translateY(-2px); /* Slight lift on hover */
            display: inline-block; /* Needed for transform to work */
        }
        /* Style for the notification bell */
        .nav-link i.bi-bell {
            font-size: 1.75rem; /* Larger bell icon to match the larger text */
            transition: color 0.3s ease; /* Smooth color transition on hover */
        }
        .nav-link:hover i.bi-bell {
            color: #00ddeb; /* Match the hover color of the nav links */
        }
        /* Style for the badge */
        .badge {
            font-size: 0.75rem; /* Slightly larger for readability */
            padding: 0.3rem 0.5rem; /* Adjust padding for better appearance */
        }
        /* Style for action buttons */
        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }
        .action-buttons .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
    </style>
</head>
<body>
    <header class="header-modern text-white py-3">
        <div class="container d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <img src="images/logo.png" alt="Library Logo" class="header-logo me-3">
                <h1 class="header-title mb-0">Admin Dashboard</h1>
            </div>
            <nav>
                <ul class="nav">
                    <li class="nav-item"><a href="index.php" class="nav-link text-white">Home</a></li>
                    <li class="nav-item"><a href="logout.php" class="nav-link text-white">Logout</a></li>
                    <?php 
                    $stmt = $pdo->prepare("SELECT COUNT(*) FROM inquiry WHERE open_status = 1");
                    $stmt->execute();
                    $unreadCount = $stmt->fetchColumn();
                    ?>
                    <li class="nav-item">
                        <a href="notificationlist.php" class="nav-link text-white position-relative">
                            <i class="bi bi-bell"></i>
                            <?php if ($unreadCount > 0): ?>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    <?php echo $unreadCount; ?>
                                </span>
                            <?php endif; ?>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="dashboard-cards">
        <div class="container my-4">
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">Total Users</h5>
                            <p class="card-text display-4"><?php echo $userCount; ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">Total PDF Files</h5>
                            <p class="card-text display-4"><?php echo $pdfCount; ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">Currently Logged In</h5>
                            <p class="card-text display-4"><?php echo $loggedInCount; ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <main class="container my-5">
        <section class="card shadow-sm p-4">
            <h2 class="h4 mb-3">Upload PDF</h2>
            <?php if (isset($message) && !$alert): ?>
                <p class="<?php echo strpos($message, 'Error') === false ? 'text-success' : 'text-danger'; ?>"><?php echo $message; ?></p>
            <?php endif; ?>
            <form id="uploadForm" method="post" enctype="multipart/form-data">
                <div class="mb-3">
                    <input type="file" name="pdfFile" id="pdfFile" class="form-control" accept=".pdf" required>
                </div>
                <button type="submit" name="upload" class="btn btn-primary">Upload</button>
            </form>
            <canvas id="pdfCanvas" style="display:none;"></canvas>
        </section>
        
        <section class="card shadow-sm p-4 mt-4">
            <h2 class="h4 mb-3">Uploaded Files</h2>
            <ul class="list-group">
            <?php
            $stmt = $pdo->query("SELECT * FROM pdf_files ORDER BY upload_date DESC");
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<li class='list-group-item d-flex justify-content-between align-items-center'>";
                echo "<span>" . htmlspecialchars($row['filename']) . "</span>";
                echo "<div class='d-flex align-items-center gap-3'>";
                echo "<span class='text-muted small'>Uploaded: {$row['upload_date']}</span>";
                echo "<div class='action-buttons'>";
                echo "<button class='btn btn-sm btn-warning' data-bs-toggle='modal' data-bs-target='#editModal' data-id='{$row['id']}' data-filename='" . htmlspecialchars($row['filename']) . "'>Edit</button>";
                echo "<button class='btn btn-sm btn-danger' data-bs-toggle='modal' data-bs-target='#deleteModal' data-id='{$row['id']}'>Delete</button>";
                echo "</div>";
                echo "</div>";
                echo "</li>";
            }
            ?>
            </ul>
        </section>
    </main>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit PDF Filename</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" action="">
                    <div class="modal-body">
                        <input type="hidden" name="id" id="editId">
                        <div class="mb-3">
                            <label for="newFilename" class="form-label">New Filename</label>
                            <input type="text" class="form-control" id="newFilename" name="new_filename" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="update" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" action="">
                    <div class="modal-body">
                        <input type="hidden" name="id" id="deleteId">
                        <p>Are you sure you want to delete this file? This action cannot be undone.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="delete" class="btn btn-danger">Delete</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <footer class="bg-dark text-white text-center py-3 mt-4">
        <p class="mb-0">© 2025 City of Koronadal Public Library</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Display SweetAlert if an action was performed
        <?php if ($alert): ?>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: '<?php echo $alert['type']; ?>',
                    title: '<?php echo $alert['type'] === 'success' ? 'Success!' : 'Error!'; ?>',
                    text: '<?php echo $alert['message']; ?>',
                    confirmButtonText: 'OK',
                    timer: 3000, // Auto-close after 3 seconds
                    timerProgressBar: true
                });
            });
        <?php endif; ?>

        // Handle PDF Upload
        document.getElementById('uploadForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const fileInput = document.getElementById('pdfFile');
            const file = fileInput.files[0];
            if (!file) return;

            const arrayBuffer = await file.arrayBuffer();
            const pdf = await pdfjsLib.getDocument({ data: arrayBuffer }).promise;
            const page = await pdf.getPage(1);

            const canvas = document.getElementById('pdfCanvas');
            const context = canvas.getContext('2d');
            const viewport = page.getViewport({ scale: 1.0 });
            canvas.width = 200;
            canvas.height = viewport.height * (200 / viewport.width);
            await page.render({
                canvasContext: context,
                viewport: viewport
            }).promise;

            const previewImage = canvas.toDataURL('image/png');
            const formData = new FormData();
            formData.append('pdfFile', file);
            formData.append('previewImage', previewImage);
            formData.append('upload', true);

            fetch('admin.php', {
                method: 'POST',
                body: formData
            }).then(response => response.text()).then(text => {
                console.log('Upload response:', text);
                location.reload(); // Reload to trigger SweetAlert
            }).catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'An error occurred while uploading the file.',
                    confirmButtonText: 'OK'
                });
            });
        });

        // Handle Edit Modal Data Population
        const editModal = document.getElementById('editModal');
        editModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');
            const filename = button.getAttribute('data-filename');

            const editId = document.getElementById('editId');
            const newFilename = document.getElementById('newFilename');

            editId.value = id;
            newFilename.value = filename;
        });

        // Handle Delete Modal Data Population
        const deleteModal = document.getElementById('deleteModal');
        deleteModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');

            const deleteId = document.getElementById('deleteId');
            deleteId.value = id;
        });
    </script>
</body>
</html>