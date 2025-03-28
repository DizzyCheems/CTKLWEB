<?php 
include 'config.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

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
    } else {
        $message = "Error uploading file.";
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
</head>
<body>
    <header class="bg-dark text-white py-3">
        <div class="container d-flex justify-content-between align-items-center">
            <h1 class="h3 mb-0">Admin Dashboard</h1>
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
                            <i class="bi bi-bell" style="font-size: 1.5rem;"></i>
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

    <main class="container my-5">
        <section class="card shadow-sm p-4">
            <h2 class="h4 mb-3">Upload PDF</h2>
            <?php if (isset($message)): ?>
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
                echo htmlspecialchars($row['filename']);
                echo "<span class='text-muted small'>Uploaded: {$row['upload_date']}</span>";
                echo "</li>";
            }
            ?>
            </ul>
        </section>
    </main>

    <footer class="bg-dark text-white text-center py-3 mt-4">
        <p class="mb-0">© 2025 City of Koronadal Public Library</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
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
                location.reload();
            }).catch(error => {
                console.error('Error:', error);
            });
        });
    </script>
</body>
</html>