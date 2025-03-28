<?php
include 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

// Handle Mark as Read action
if (isset($_GET['mark_read'])) {
    $inquiry_id = $_GET['mark_read'];

    // Update the open_status to 0 for the specific inquiry
    $stmt = $pdo->prepare("UPDATE inquiry SET open_status = 0 WHERE id = ?");
    $stmt->execute([$inquiry_id]);

    // Redirect to the same page to refresh the notification status
    header("Location: notificationlist.php");
    exit; // Make sure to stop further script execution
}

// Pagination setup
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Search setup
$searchQuery = isset($_GET['search']) ? $_GET['search'] : '';
$whereClause = '';
if ($searchQuery) {
    $whereClause = "WHERE email LIKE :search OR message LIKE :search";
}

// Query to fetch inquiries with pagination and search
$stmt = $pdo->prepare("SELECT * FROM inquiry $whereClause ORDER BY created_at DESC LIMIT $limit OFFSET $offset");
if ($searchQuery) {
    $stmt->bindValue(':search', "%$searchQuery%");
}
$stmt->execute();
$inquiries = $stmt->fetchAll();

// Query to get the total number of inquiries (for pagination)
$totalStmt = $pdo->prepare("SELECT COUNT(*) FROM inquiry $whereClause");
if ($searchQuery) {
    $totalStmt->bindValue(':search', "%$searchQuery%");
}
$totalStmt->execute();
$totalInquiries = $totalStmt->fetchColumn();
$totalPages = ceil($totalInquiries / $limit);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notification List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        /* Flexbox Layout to keep footer at the bottom */
        html, body {
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        body {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        main {
            flex-grow: 1;
        }

        .notification-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .notification-card {
            padding: 15px;
            border-radius: 10px;
            background-color: #fff;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .notification-card:hover {
            box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
            transform: translateY(-5px);
        }

        .notification-card.read {
            background-color: #f5f5f5;
            opacity: 0.6;
        }

        .notification-card .close-btn {
            position: absolute;
            top: 5px;
            right: 5px;
            font-size: 16px;
            color: #888;
            cursor: pointer;
        }

        .notification-card .close-btn:hover {
            color: #000;
        }
    </style>
</head>
<body>

<header class="bg-dark text-white py-3">
    <div class="container d-flex justify-content-between align-items-center">
        <h1 class="h3 mb-0">Notifications</h1>
        <nav>
            <ul class="nav">
                <li class="nav-item"><a href="admin.php" class="nav-link text-white">Admin</a></li>
                <li class="nav-item"><a href="logout.php" class="nav-link text-white">Logout</a></li>
            </ul>
        </nav>
    </div>
</header>

<main class="container my-5">
    <section class="card shadow-sm p-4">
        <h2 class="h4 mb-3">All Inquiries</h2>

        <!-- Search Bar -->
        <form action="notificationlist.php" method="get" class="mb-4">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Search inquiries..." value="<?php echo htmlspecialchars($searchQuery); ?>">
                <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i></button>
            </div>
        </form>

        <?php if (count($inquiries) > 0): ?>
            <div class="notification-container">
                <?php foreach ($inquiries as $inquiry): ?>
                    <div class="notification-card <?php echo $inquiry['open_status'] == 0 ? 'read' : ''; ?>" data-bs-toggle="modal" data-bs-target="#messageModal" onclick="openModal('<?php echo htmlspecialchars($inquiry['email']); ?>', '<?php echo htmlspecialchars($inquiry['message']); ?>')">
                        <div class="close-btn" onclick="closeNotification(this)">&times;</div>
                        <strong>Email:</strong> <?php echo htmlspecialchars($inquiry['email']); ?><br>
                        <strong>Message:</strong> <?php echo substr(htmlspecialchars($inquiry['message']), 0, 50); ?>...
                        <?php if ($inquiry['open_status'] == 1): ?>
                            <a href="notificationlist.php?mark_read=<?php echo $inquiry['id']; ?>" class="btn btn-sm btn-success mt-2">Mark as Read</a>
                        <?php else: ?>
                            <span class="badge bg-secondary mt-2">Read</span>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>No inquiries available.</p>
        <?php endif; ?>

        <!-- Pagination -->
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center mt-4">
                <li class="page-item <?php echo $page == 1 ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo max(1, $page - 1); ?>&search=<?php echo urlencode($searchQuery); ?>">Previous</a>
                </li>
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($searchQuery); ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?php echo $page == $totalPages ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo min($totalPages, $page + 1); ?>&search=<?php echo urlencode($searchQuery); ?>">Next</a>
                </li>
            </ul>
        </nav>

    </section>
</main>

<!-- Modal for displaying the message -->
<div class="modal fade" id="messageModal" tabindex="-1" aria-labelledby="messageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="messageModalLabel">Inquiry Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="text" class="form-control" id="email" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="message" class="form-label">Message</label>
                        <textarea class="form-control" id="message" rows="5" readonly></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<footer class="bg-dark text-white text-center py-3 mt-4">
    <p class="mb-0">© 2025 City of Koronadal Public Library</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Close Notification Card
    function closeNotification(element) {
        element.closest('.notification-card').style.display = 'none';
    }

    // Open Modal and populate with inquiry details
    function openModal(email, message) {
        document.getElementById('email').value = email;
        document.getElementById('message').value = message;
    }
</script>

</body>
</html>
