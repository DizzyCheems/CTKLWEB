<?php
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
// Get all users except current user
$stmt = $pdo->prepare("SELECT id, username FROM users WHERE id != ?");
$stmt->execute([$user_id]);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat - City of Koronadal Public Library</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        .chat-container {
            max-width: 800px;
            margin: 20px auto;
        }
        .users-list {
            height: 500px;
            overflow-y: auto;
            background-color: rgba(255, 255, 255, 0.9);
        }
        .chat-box {
            height: 500px;
            overflow-y: auto;
            background-color: rgba(255, 255, 255, 0.9);
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
        .message-them {
            text-align: left;
            color: #333;
        }
    </style>
</head>
<body>
    <header class="bg-dark text-white py-3">
        <div class="container d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <img src="images/logo.png" alt="Library Logo" class="header-logo me-3">
                <h1 class="header-title mb-0">City of Koronadal Public Library</h1>
            </div>
            <nav>
                <ul class="nav">
                    <li class="nav-item"><a href="user.php" class="nav-link text-white">Home</a></li>
                    <li class="nav-item"><a href="logout.php" class="nav-link text-white">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container my-5">
        <div class="chat-container card shadow-sm p-0">
            <div class="row g-0">
                <div class="col-4 users-list p-3">
                    <h4>Users</h4>
                    <?php foreach ($users as $user): ?>
                        <div class="mb-2">
                            <a href="#" class="text-decoration-none" onclick="selectUser(<?php echo $user['id']; ?>)">
                                <?php echo htmlspecialchars($user['username']); ?>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="col-8">
                    <div class="chat-box" id="chat-box"></div>
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

    <footer class="bg-dark text-white text-center py-3 mt-4">
        <p class="mb-0">© 2025 City of Koronadal Public Library</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        let selectedUser = null;
        let lastMessageId = 0;

        function selectUser(userId) {
            selectedUser = userId;
            $("#receiver_id").val(userId);
            $("#chat-box").empty(); // Clear previous messages
            lastMessageId = 0; // Reset last message ID
            loadMessages();
        }

        function loadMessages() {
            if (!selectedUser) return;

            $.ajax({
                url: 'get_messages.php',
                method: 'POST',
                data: {
                    receiver_id: selectedUser,
                    last_id: lastMessageId
                },
                success: function(data) {
                    let messages = JSON.parse(data);
                    messages.forEach(function(msg) {
                        let messageClass = msg.sender_id == <?php echo $user_id; ?> ? 'message-me' : 'message-them';
                        $("#chat-box").append(
                            '<div class="' + messageClass + '">' +
                            '<strong>' + (msg.sender_id == <?php echo $user_id; ?> ? 'Me' : 'Them') + ':</strong> ' + 
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
            if (!selectedUser) {
                alert("Please select a user to chat with");
                return;
            }

            $.ajax({
                url: 'send_message.php',
                method: 'POST',
                data: {
                    receiver_id: selectedUser,
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