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
    $target_dir = "Uploads/";
    $preview_dir = "Uploads/previews/";
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
        $pdf_path = "Uploads/" . $file['filename'];
        $preview_path = $file['preview_image'];

        if (file_exists($pdf_path)) unlink($pdf_path);
        if ($preview_path && file_exists($preview_path)) unlink($preview_path);

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

    $new_filename = preg_replace('/[^A-Za-z0-9\-\_\.]/', '', $new_filename);
    if (pathinfo($new_filename, PATHINFO_EXTENSION) !== 'pdf') $new_filename .= '.pdf';

    $stmt = $pdo->prepare("SELECT filename, preview_image FROM pdf_files WHERE id = ?");
    $stmt->execute([$id]);
    $file = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($file) {
        $old_pdf_path = "Uploads/" . $file['filename'];
        $new_pdf_path = "Uploads/" . $new_filename;
        $old_preview_path = $file['preview_image'];
        $new_preview_filename = pathinfo($new_filename, PATHINFO_FILENAME) . '.png';
        $new_preview_path = "Uploads/previews/" . $new_preview_filename;

        if (file_exists($old_pdf_path)) rename($old_pdf_path, $new_pdf_path);
        if ($old_preview_path && file_exists($old_preview_path)) rename($old_preview_path, $new_preview_path);

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

// Data for Charts (default: last 12 months for monthly, last 7 days for daily)
$monthlyVisits = [];
for ($i = 11; $i >= 0; $i--) {
    $month = date('Y-m', strtotime("-$i months"));
    $stmt = $pdo->prepare("SELECT COUNT(DISTINCT user_id) as visits FROM active_sessions WHERE DATE_FORMAT(last_active, '%Y-%m') = ?");
    $stmt->execute([$month]);
    $monthlyVisits[$month] = $stmt->fetchColumn();
}

$dailyTraffic = [];
for ($i = 6; $i >= 0; $i--) {
    $day = date('Y-m-d', strtotime("-$i days"));
    $stmt = $pdo->prepare("SELECT COUNT(*) as traffic FROM active_sessions WHERE DATE(last_active) = ?");
    $stmt->execute([$day]);
    $dailyTraffic[$day] = $stmt->fetchColumn();
}

// User Role Distribution
$roleDistribution = [];
$stmt = $pdo->prepare("SELECT role, COUNT(*) as count FROM users GROUP BY role");
$stmt->execute();
$roleDistribution = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <style>
        body {
            background-image: url('images/hallway.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            min-height: 100vh;
        }
        main .container,
        main .card {
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 10px;
        }
        .dashboard-cards .container {
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 10px;
        }
        footer.bg-dark {
            background-color: #212529 !important;
        }
        .header-modern {
            background: linear-gradient(90deg, #1a2526 0%, #2c3e50 100%);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        .header-logo {
            height: 2.5rem;
            width: auto;
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
        .nav-link i.bi-bell {
            font-size: 1.75rem;
            transition: color 0.3s ease;
        }
        .nav-link:hover i.bi-bell {
            color: #00ddeb;
        }
        .badge {
            font-size: 0.75rem;
            padding: 0.3rem 0.5rem;
        }
        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }
        .action-buttons .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
        .chart-container {
            position: relative;
            margin: auto;
            height: 400px;
            width: 80%;
            display: flex;
            justify-content: center;
        }
        .btn-export {
            background-color: #3498db;
            color: white;
            font-weight: 600;
            border: none;
            padding: 8px 16px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
            margin-top: 20px;
        }
        .btn-export:hover {
            background-color: #2980b9;
        }
        .export-button-container {
            text-align: center;
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
        <!-- User Analytics Section with Charts -->
        <section class="card shadow-sm p-4 mb-4">
            <h2 class="h4 mb-3">User Analytics</h2>
            <!-- Date Range Filters -->
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="dateFrom" class="form-label">Date From</label>
                    <input type="date" class="form-control" id="dateFrom" name="dateFrom">
                </div>
                <div class="col-md-4">
                    <label for="dateTo" class="form-label">Date To</label>
                    <input type="date" class="form-control" id="dateTo" name="dateTo">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button class="btn btn-primary w-100" id="filterButton">Filter</button>
                </div>
            </div>

            <!-- Tabbed Layout -->
            <ul class="nav nav-tabs" id="analyticsTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="monthlyVisits-tab" data-bs-toggle="tab" data-bs-target="#monthlyVisits" type="button" role="tab" aria-controls="monthlyVisits" aria-selected="true">Monthly Visits</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="dailyTraffic-tab" data-bs-toggle="tab" data-bs-target="#dailyTraffic" type="button" role="tab" aria-controls="dailyTraffic" aria-selected="false">Daily Traffic</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="roleDistribution-tab" data-bs-toggle="tab" data-bs-target="#roleDistribution" type="button" role="tab" aria-controls="roleDistribution" aria-selected="false">Role Distribution</button>
                </li>
            </ul>
            <div class="tab-content mt-3" id="analyticsTabsContent">
                <div class="tab-pane fade show active" id="monthlyVisits" role="tabpanel" aria-labelledby="monthlyVisits-tab">
                    <div class="chart-container">
                        <canvas id="monthlyVisitsChart"></canvas>
                    </div>
                </div>
                <div class="tab-pane fade" id="dailyTraffic" role="tabpanel" aria-labelledby="dailyTraffic-tab">
                    <div class="chart-container">
                        <canvas id="dailyTrafficChart"></canvas>
                    </div>
                </div>
                <div class="tab-pane fade" id="roleDistribution" role="tabpanel" aria-labelledby="roleDistribution-tab">
                    <div class="chart-container">
                        <canvas id="roleDistributionChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="export-button-container">
                <button class="btn-export" onclick="exportCharts()">Export Charts to PDF</button>
            </div>
        </section>

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
        // SweetAlert for actions
        <?php if ($alert): ?>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: '<?php echo $alert['type']; ?>',
                    title: '<?php echo $alert['type'] === 'success' ? 'Success!' : 'Error!'; ?>',
                    text: '<?php echo $alert['message']; ?>',
                    confirmButtonText: 'OK',
                    timer: 3000,
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
                location.reload();
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

        // Edit Modal Data Population
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

        // Delete Modal Data Population
        const deleteModal = document.getElementById('deleteModal');
        deleteModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');

            const deleteId = document.getElementById('deleteId');
            deleteId.value = id;
        });

        // Chart.js Scripts
        let monthlyChart, dailyChart, roleChart;

        // Initialize Charts
        function initializeCharts() {
            const monthlyVisitsCtx = document.getElementById('monthlyVisitsChart').getContext('2d');
            monthlyChart = new Chart(monthlyVisitsCtx, {
                type: 'bar',
                data: {
                    labels: <?php echo json_encode(array_keys($monthlyVisits)); ?>,
                    datasets: [{
                        label: 'User Visits',
                        data: <?php echo json_encode(array_values($monthlyVisits)); ?>,
                        backgroundColor: 'rgba(54, 162, 235, 0.5)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });

            const dailyTrafficCtx = document.getElementById('dailyTrafficChart').getContext('2d');
            dailyChart = new Chart(dailyTrafficCtx, {
                type: 'line',
                data: {
                    labels: <?php echo json_encode(array_keys($dailyTraffic)); ?>,
                    datasets: [{
                        label: 'Daily Traffic',
                        data: <?php echo json_encode(array_values($dailyTraffic)); ?>,
                        fill: false,
                        borderColor: 'rgba(75, 192, 192, 1)',
                        tension: 0.1
                    }]
                },
                options: {
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });

            const roleDistributionCtx = document.getElementById('roleDistributionChart').getContext('2d');
            roleChart = new Chart(roleDistributionCtx, {
                type: 'pie',
                data: {
                    labels: <?php echo json_encode(array_keys($roleDistribution)); ?>,
                    datasets: [{
                        label: 'User Roles',
                        data: <?php echo json_encode(array_values($roleDistribution)); ?>,
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.5)',
                            'rgba(54, 162, 235, 0.5)',
                            'rgba(255, 206, 86, 0.5)',
                            'rgba(75, 192, 192, 0.5)'
                        ],
                        borderColor: [
                            'rgba(255, 99, 132, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(75, 192, 192, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        }
                    }
                }
            });
        }

        // Call initializeCharts on page load
        document.addEventListener('DOMContentLoaded', initializeCharts);

        // Filter Button Event Listener
        document.getElementById('filterButton').addEventListener('click', function () {
            const dateFrom = document.getElementById('dateFrom').value;
            const dateTo = document.getElementById('dateTo').value;

            if (!dateFrom || !dateTo) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Missing Dates',
                    text: 'Please select both Date From and Date To.',
                    confirmButtonText: 'OK'
                });
                return;
            }

            // Fetch filtered data and update charts
            fetch(`fetch_analytics.php?dateFrom=${dateFrom}&dateTo=${dateTo}`)
                .then(response => response.json())
                .then(data => {
                    // Update Monthly Visits Chart
                    monthlyChart.data.labels = data.monthly.labels;
                    monthlyChart.data.datasets[0].data = data.monthly.data;
                    monthlyChart.update();

                    // Update Daily Traffic Chart
                    dailyChart.data.labels = data.daily.labels;
                    dailyChart.data.datasets[0].data = data.daily.data;
                    dailyChart.update();

                    // Role Distribution doesn't change with date filter
                })
                .catch(error => {
                    console.error('Error fetching analytics data:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to fetch analytics data.',
                        confirmButtonText: 'OK'
                    });
                });
        });

        // jsPDF Export Function for All Charts
        function exportCharts() {
            const monthlyCanvas = document.getElementById('monthlyVisitsChart');
            const dailyCanvas = document.getElementById('dailyTrafficChart');
            const roleCanvas = document.getElementById('roleDistributionChart');
            const monthlyImgData = monthlyCanvas.toDataURL('image/png');
            const dailyImgData = dailyCanvas.toDataURL('image/png');
            const roleImgData = roleCanvas.toDataURL('image/png');
            const { jsPDF } = window.jspdf;
            const pdf = new jsPDF('p', 'mm', 'a4');

            // Page 1: Monthly User Visits
            pdf.setFontSize(18);
            pdf.text('Monthly User Visits Report', 20, 20);
            pdf.addImage(monthlyImgData, 'PNG', 20, 30, 170, 100);
            pdf.setFontSize(10);
            pdf.text('City of Koronadal Public Library - Report Generated on ' + new Date().toLocaleDateString(), 20, 280);

            // Page 2: Daily User Traffic
            pdf.addPage();
            pdf.setFontSize(18);
            pdf.text('Daily User Traffic Report', 20, 20);
            pdf.addImage(dailyImgData, 'PNG', 20, 30, 170, 100);
            pdf.setFontSize(10);
            pdf.text('City of Koronadal Public Library - Report Generated on ' + new Date().toLocaleDateString(), 20, 280);

            // Page 3: User Role Distribution
            pdf.addPage();
            pdf.setFontSize(18);
            pdf.text('User Role Distribution Report', 20, 20);
            pdf.addImage(roleImgData, 'PNG', 20, 30, 170, 100);
            pdf.setFontSize(10);
            pdf.text('City of Koronadal Public Library - Report Generated on ' + new Date().toLocaleDateString(), 20, 280);

            // Save PDF
            pdf.save('Library_Analytics_Report.pdf');
        }
    </script>
</body>
</html>