<?php
include('SiT_3/config.php');
include('SiT_3/header.php');

session_start();

// Ensure user is logged in
if (!$loggedIn) {
    header("Location: /");
    exit();
}

$errors = [];
$success = "";
$chatId = isset($_GET['chat_id']) ? intval($_GET['chat_id']) : 0;
$currentUserId = $userRow->id;

if ($chatId <= 0) {
    die("Invalid chat ID.");
}

// Fetch existing messages for the chat
$messages = [];
$stmt = $conn->prepare("SELECT m.*, u.username FROM gc_messages m JOIN beta_users u ON m.user_id = u.id WHERE m.chat_id = ? ORDER BY m.created_at ASC");
$stmt->bind_param('i', $chatId);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $messages[] = $row;
}

// Handle form submission for sending messages
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = trim($_POST['message']);
    if (!empty($message)) {
        $stmt = $conn->prepare("INSERT INTO gc_messages (chat_id, user_id, message) VALUES (?, ?, ?)");
        $stmt->bind_param('iis', $chatId, $currentUserId, $message);
        if ($stmt->execute()) {
            $success = "Message sent successfully.";
        } else {
            $errors[] = "Failed to send message.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Group Chat</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
        }
        #body {
            padding: 10px;
        }
        .messages {
            border: 1px solid #ccc;
            padding: 10px;
            height: 300px;
            overflow-y: scroll;
            background-color: #fff; /* Set the background of messages to white */
        }
        .message {
            margin-bottom: 10px;
            background-color: #f9f9f9; /* Background color for individual messages */
            padding: 10px;
            border-radius: 4px;
        }
        .form-group {
            margin-top: 10px;
        }
        .form-group input[type="text"] {
            padding: 8px;
            width: calc(100% - 100px);
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .form-group button {
            padding: 8px;
            background-color: #4CAF50;
            color: #fff;
            border: none;
            cursor: pointer;
        }
        .error {
            color: red;
        }
        .success {
            color: green;
        }
        .scroll-to-bottom {
            padding: 5px;
            text-align: right;
        }
    </style>
    <script>
        function fetchMessages() {
            const xhr = new XMLHttpRequest();
            xhr.open('GET', 'gc_fetch_messages?chat_id=<?php echo $chatId; ?>', true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    document.getElementById('messages').innerHTML = xhr.responseText;
                }
            };
            xhr.send();
        }

        function sendMessage() {
            const messageInput = document.getElementById('message');
            const message = messageInput.value;
            if (message.trim() !== '') {
                const xhr = new XMLHttpRequest();
                const formData = new FormData();
                formData.append('message', message);
                formData.append('chat_id', '<?php echo $chatId; ?>');
                xhr.open('POST', 'gc_send_message', true);
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        messageInput.value = '';
                        fetchMessages();
                        setTimeout(scrollToBottom, 100); // Add a slight delay before scrolling to the bottom
                    } else {
                        console.error('Error sending message:', xhr.statusText);
                    }
                };
                xhr.send(formData);
            }
        }

        function scrollToBottom() {
            const messagesDiv = document.getElementById('messages');
            messagesDiv.scrollTop = messagesDiv.scrollHeight;
        }

        window.onload = function() {
            fetchMessages();
            setInterval(fetchMessages, 500);
            scrollToBottom(); // Scroll to bottom on page load
        };
    </script>
</head>
<body>
    <div id="body">
        <h1>Group Chat</h1>
        <?php if (!empty($errors)): ?>
            <div class="error">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="success">
                <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>
        <div class="messages" id="messages">
            <?php foreach ($messages as $msg): ?>
                <div class="message">
                    <strong><?php echo htmlspecialchars($msg['username']); ?>:</strong>
                    <p><?php echo htmlspecialchars($msg['message']); ?></p>
                    <small><?php echo htmlspecialchars($msg['created_at']); ?></small>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="scroll-to-bottom">
            <button onclick="scrollToBottom()">Jump to Latest Message</button>
        </div>
        <form id="messageForm" onsubmit="sendMessage(); return false;">
            <div class="form-group">
                <input type="text" id="message" name="message" placeholder="Type your message here..." required>
                <button type="submit">Send</button>
            </div>
        </form>
    </div>
</body>
</html>
