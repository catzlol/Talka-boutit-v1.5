<?php
include('SiT_3/config.php');
include('SiT_3/header.php');

session_start();

// Ensure user is logged in
if (!$loggedIn) {
    header("Location: /");
    exit();
}

// Get the current user's ID and username
$currentUserId = $userRow->id;
$currentUsername = $userRow->username;

// Get the 'touser' ID from the URL parameter
if (!isset($_GET['touser']) || !is_numeric($_GET['touser'])) {
    echo "Invalid user.";
    exit();
}
$touserId = (int)$_GET['touser'];

// Check if the conversation already exists in the `userdms` table
$query = "SELECT * FROM userdms WHERE (touser = ? AND fromuser = ?) OR (touser = ? AND fromuser = ?)";
$stmt = $conn->prepare($query);
$stmt->bind_param('iiii', $touserId, $currentUserId, $currentUserId, $touserId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // If conversation doesn't exist, create a new entry
    $insertQuery = "INSERT INTO userdms (touser, fromuser, message, datetime, ishidden) VALUES (?, ?, ?, NOW(), 0)";
    $insertStmt = $conn->prepare($insertQuery);
    $initialMessage = "This is a new chat!";
    $insertStmt->bind_param('iis', $touserId, $currentUserId, $initialMessage);
    $insertStmt->execute();

    // Retrieve the newly created conversation for display
    $stmt->execute();
    $result = $stmt->get_result();
}

// Update unread messages to read for the current user
$updateQuery = "UPDATE userdms SET unread = 0 WHERE touser = ? AND unread = 1";
$updateStmt = $conn->prepare($updateQuery);
$updateStmt->bind_param('i', $currentUserId);
$updateStmt->execute();

// Fetch all messages for the conversation
$messages = [];
while ($row = $result->fetch_assoc()) {
    $messages[] = $row;
}

// Function to determine if the message is from the current user
function isCurrentUserMessage($message, $currentUserId) {
    return $message['fromuser'] === $currentUserId;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Direct Messages</title>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
        }
        #body {
            padding: 10px;
        }
        #messages {
            border: 1px solid #ccc;
            padding: 10px;
            height: 300px;
            overflow-y: scroll;
            background-color: #fff;
        }
        #chat-controls {
            margin-top: 10px;
        }
        #message-input {
            padding: 8px;
            width: 80%;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        #send-button {
            padding: 5px 10px;
            background-color: #4CAF50;
            color: #fff;
            border: none;
            cursor: pointer;
        }
        #jump-to-newest-button {
            padding: 5px 10px;
            background-color: #2196F3;
            color: #fff;
            border: none;
            cursor: pointer;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div id="body">
        <h1>Direct Messages</h1>
        <p style="color: red;">This is a Beta feature. If a user is harassing you, report their account.</p>
        <div id="messages">
            <?php foreach ($messages as $message): ?>
                <p id="message-<?php echo $message['ID']; ?>">
                    <strong><?php echo isCurrentUserMessage($message, $currentUserId) ? 'You' : htmlspecialchars($message['username']); ?>:</strong>
                    <?php echo htmlspecialchars($message['message']); ?>
                    <?php if (isCurrentUserMessage($message, $currentUserId)): ?>
                        <button onclick="deleteMessage(<?php echo $message['ID']; ?>)">Delete</button>
                    <?php endif; ?>
                </p>
            <?php endforeach; ?>
        </div>
        <button id="jump-to-newest-button">Jump to Newest Message</button>
        <div id="chat-controls">
            <input type="text" id="message-input" placeholder="Type a message...">
            <button id="send-button">Send</button>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            function fetchMessages() {
                $.getJSON('fetch_messages?touser=<?php echo $touserId; ?>&fromuser=<?php echo $currentUserId; ?>', function(response) {
                    if (response.success) {
                        $('#messages').empty();
                        response.messages.forEach(function(message) {
                            var sender = (message.fromuser == <?php echo $currentUserId; ?>) ? 'You' : message.username;
                            $('#messages').append(
                                '<p id="message-' + message.id + '">' +
                                '<strong>' + sender + ':</strong> ' + message.message + 
                                (message.fromuser == <?php echo $currentUserId; ?> ? 
                                '<button onclick="deleteMessage(' + message.id + ')">Delete</button>' : '') +
                                '</p>'
                            );
                        });
                        // Scroll to the bottom after appending new messages
                        $('#messages').scrollTop($('#messages')[0].scrollHeight);
                    } else {
                        console.log('Failed to fetch messages.');
                    }
                });
            }

            // Initial fetch and auto-update every 0.5 seconds
            fetchMessages();
            setInterval(fetchMessages, 500);

            // Function to send a message
            function sendMessage() {
                const message = $('#message-input').val().trim();
                if (message === '') return;

                $.ajax({
                    type: 'POST',
                    url: 'send_message',
                    data: {
                        touser: <?php echo $touserId; ?>,
                        fromuser: <?php echo $currentUserId; ?>,
                        message: message
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#messages').append('<p><strong>You:</strong> ' + message + '</p>');
                            $('#message-input').val('');
                            $('#messages').scrollTop($('#messages')[0].scrollHeight); // Scroll to the bottom after sending
                        } else {
                            alert('Failed to send message.');
                        }
                    },
                    error: function() {
                        alert('Failed to send message.');
                    },
                    dataType: 'json'
                });
            }

            // Send message when clicking the button
            $('#send-button').click(function() {
                sendMessage();
            });

            // Send message when pressing Enter in the input field
            $('#message-input').keypress(function(e) {
                if (e.which == 13) { // 13 is the Enter key code
                    sendMessage();
                }
            });

            // Jump to newest message
            $('#jump-to-newest-button').click(function() {
                $('#messages').scrollTop($('#messages')[0].scrollHeight);
            });
        });

        // Function to delete a message
        function deleteMessage(messageId) {
            if (confirm('Are you sure you want to delete this message?')) {
                $.ajax({
                    type: 'POST',
                    url: 'delete_message.php',
                    data: { id: messageId },
                    success: function(response) {
                        if (response.success) {
                            $('#message-' + messageId).remove();
                        } else {
                            alert('Failed to delete message.');
                        }
                    },
                    error: function() {
                        alert('Failed to delete message.');
                    },
                    dataType: 'json'
                });
            }
        }
    </script>
</body>
</html>
