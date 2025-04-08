<?php
include 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] === 'admin') {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get all admins who have messaged this user
$stmt = $pdo->prepare("
    SELECT DISTINCT u.id, u.username 
    FROM users u
    INNER JOIN messages m ON (m.sender_id = u.id OR m.receiver_id = u.id)
    WHERE (m.sender_id = ? OR m.receiver_id = ?) AND u.role = 'admin'
    ORDER BY u.username
");
$stmt->execute([$user_id, $user_id]);
$admins = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get count of unread notifications
$stmt_notifications = $pdo->prepare("SELECT COUNT(*) FROM messages WHERE receiver_id = ? AND sender_id IN (SELECT id FROM users WHERE role = 'admin') AND id > (SELECT COALESCE(MAX(last_read_message_id), 0) FROM user_notifications WHERE user_id = ?)");
$stmt_notifications->execute([$_SESSION['user_id'], $_SESSION['user_id']]);
$notification_count = $stmt_notifications->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - City of Koronadal Public Library</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .chat-container {
            max-width: 800px;
            margin: 20px auto;
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 5px;
            padding: 15px;
        }
        .admins-list {
            height: 500px;
            overflow-y: auto;
            background-color: rgba(255, 255, 255, 0.9);
            padding: 15px;
            border-right: 1px solid #ccc;
        }
        .chat-box {
            height: 500px;
            overflow-y: auto;
            padding: 15px;
        }
        .message-input {
            width: 100%;
            min-height: 100px;
        }
        .message-me { 
            text-align: right;
            color: #007bff;
        }
        .message-admin {
            text-align: left;
            color: #333;
        }
        .success-message {
            background-color: rgba(40, 167, 69, 0.95);
            color: white;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            margin-bottom: 15px;
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
                    <li class="nav-item"><a href="user.php" class="nav-link text-white">Home</a></li>
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
        <div class="chat-container card shadow-sm">
            <?php if (isset($_GET['success'])): ?>
                <div class="success-message"><?php echo htmlspecialchars($_GET['success']); ?></div>
            <?php endif; ?>
            <div class="row g-0">
                <div class="col-4 admins-list">
                    <h4>Admins</h4>
                    <?php if (empty($admins)): ?>
                        <p>No messages from admins yet.</p>
                    <?php else: ?>
                        <?php foreach ($admins as $admin): ?>
                            <div class="mb-2">
                                <a href="#" class="text-decoration-none" onclick="selectAdmin(<?php echo $admin['id']; ?>)">
                                    <?php echo htmlspecialchars($admin['username']); ?>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <div class="col-8">
                    <div class="chat-box" id="chat-box">
                        <p>Select an admin to view conversation.</p>
                    </div>
                    <form id="message-form" class="p-3">
                        <input type="hidden" id="receiver_id" name="receiver_id">
                        <div class="mb-3">
                            <textarea id="message" name="message" class="form-control message-input" placeholder="Type your message..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Send</button>
                    </form>
                </div>
            </div>
        </div>
    </main>

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

    <footer class="bg-dark text-white text-center py-3 mt-4">
        <p class="mb-0">© 2025 City of Koronadal Public Library</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        let selectedAdmin = null;
        let lastMessageId = 0;

        function selectAdmin(adminId) {
            selectedAdmin = adminId;
            $("#receiver_id").val(adminId);
            $("#chat-box").empty();
            lastMessageId = 0;
            loadMessages();
        }

        function loadMessages() {
            if (!selectedAdmin) return;

            $.ajax({
                url: 'get_messages.php',
                method: 'POST',
                data: {
                    receiver_id: selectedAdmin,
                    last_id: lastMessageId
                },
                success: function(data) {
                    let messages = JSON.parse(data);
                    messages.forEach(function(msg) {
                        let messageClass = msg.sender_id == <?php echo $user_id; ?> ? 'message-me' : 'message-admin';
                        $("#chat-box").append(
                            '<div class="' + messageClass + '">' +
                            '<strong>' + (msg.sender_id == <?php echo $user_id; ?> ? 'Me' : 'Admin') + ':</strong> ' + 
                            msg.message + 
                            '<br><small>' + msg.timestamp + '</small>' +
                            '</div><hr>'
                        );
                        lastMessageId = Math.max(lastMessageId, msg.id);
                    });
                    $("#chat-box").scrollTop($("#chat-box")[0].scrollHeight);
                }
            });
        }

        $("#message-form").submit(function(e) {
            e.preventDefault();
            if (!selectedAdmin) {
                alert("Please select an admin to chat with");
                return;
            }

            $.ajax({
                url: 'send_message.php',
                method: 'POST',
                data: {
                    receiver_id: selectedAdmin,
                    message: $("#message").val()
                },
                success: function() {
                    $("#message").val('');
                    loadMessages();
                }
            });
        });

        // Auto-refresh messages every 2 seconds
        setInterval(loadMessages, 2000);
    </script>
</body>
</html>