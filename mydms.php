<?php
include('SiT_3/config.php');
include('SiT_3/header.php');

// Ensure user is logged in
if (!$loggedIn) {
    header("Location: /");
    exit();
}

// Get the current user's ID
$currentUserId = $userRow->id;

// Determine the current page number
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 10;
$offset = ($page - 1) * $perPage;

// Query to get conversations involving the current user, ordered by latest message time descending
$query = "
    SELECT
        CASE
            WHEN touser = ? THEN fromuser
            ELSE touser
        END AS other_user_id,
        MAX(datetime) AS latest_message_time
    FROM userdms
    WHERE touser = ? OR fromuser = ?
    GROUP BY other_user_id
    ORDER BY latest_message_time DESC
    LIMIT ? OFFSET ?
";
$stmt = $conn->prepare($query);
$stmt->bind_param('iiiii', $currentUserId, $currentUserId, $currentUserId, $perPage, $offset);
$stmt->execute();
$result = $stmt->get_result();

$conversations = [];
while ($row = $result->fetch_assoc()) {
    $conversations[] = $row;
}

// Prepare an array to hold the latest messages and usernames
$conversationDetails = [];

foreach ($conversations as $conversation) {
    $otherUserId = $conversation['other_user_id'];
    $latestMessageTime = $conversation['latest_message_time'];

    // Query to get the latest message for this conversation
    $messageQuery = "
        SELECT message, unread
        FROM userdms
        WHERE (touser = ? AND fromuser = ?) OR (touser = ? AND fromuser = ?)
        AND datetime = ?
    ";
    $messageStmt = $conn->prepare($messageQuery);
    $messageStmt->bind_param('iiiis', $otherUserId, $currentUserId, $currentUserId, $otherUserId, $latestMessageTime);
    $messageStmt->execute();
    $messageResult = $messageStmt->get_result();
    $latestMessageRow = $messageResult->fetch_assoc();
    $latestMessage = $latestMessageRow['message'] ?? 'No message available';
    $isUnread = $latestMessageRow['unread'] ?? 0;

    // Query to get the username of the other user
    $usernameQuery = "SELECT username FROM beta_users WHERE id = ?";
    $usernameStmt = $conn->prepare($usernameQuery);
    $usernameStmt->bind_param('i', $otherUserId);
    $usernameStmt->execute();
    $usernameResult = $usernameStmt->get_result();
    $username = $usernameResult->fetch_assoc()['username'];

    $conversationDetails[] = [
        'username' => $username,
        'latest_message' => $latestMessage,
        'other_user_id' => $otherUserId,
        'is_unread' => $isUnread
    ];
}

// Query to get group chats involving the current user
$groupChatsQuery = "
    SELECT c.id, c.name
    FROM gc_members m
    JOIN gc_chats c ON m.chat_id = c.id
    WHERE m.user_id = ?
";
$groupChatsStmt = $conn->prepare($groupChatsQuery);
$groupChatsStmt->bind_param('i', $currentUserId);
$groupChatsStmt->execute();
$groupChatsResult = $groupChatsStmt->get_result();

$groupChats = [];
while ($row = $groupChatsResult->fetch_assoc()) {
    $groupChats[] = $row;
}

// Query to get the total number of conversations
$totalConversationsQuery = "
    SELECT COUNT(DISTINCT CASE
        WHEN touser = ? THEN fromuser
        ELSE touser
    END) AS total
    FROM userdms
    WHERE touser = ? OR fromuser = ?
";
$totalStmt = $conn->prepare($totalConversationsQuery);
$totalStmt->bind_param('iii', $currentUserId, $currentUserId, $currentUserId);
$totalStmt->execute();
$totalResult = $totalStmt->get_result();
$totalRow = $totalResult->fetch_assoc();
$totalConversations = $totalRow['total'];
$totalPages = ceil($totalConversations / $perPage);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Direct Messages</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
        }
        #body {
            padding: 10px;
        }
        .conversation-item, .group-chat-item {
            border: 1px solid #ccc;
            padding: 10px;
            margin-bottom: 10px;
            background-color: #fff;
        }
        .conversation-item h3, .group-chat-item h3 {
            margin: 0;
            font-size: 1.2em;
        }
        .conversation-item p, .group-chat-item p {
            margin: 5px 0;
        }
        .enter-button, .view-button {
            background-color: #3498db;
            color: #fff;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
        }
        .notification {
            background-color: #f9c2c2;
            color: #d32f2f;
            border: 1px solid #d32f2f;
            padding: 10px;
            margin: 10px 0;
        }
        .create-button {
            background-color: #2ecc71;
            color: #fff;
            border: none;
            padding: 10px;
            cursor: pointer;
            text-align: center;
        }
        .pagination {
            text-align: center;
            margin: 20px 0;
        }
        .pagination a {
            padding: 5px 10px;
            margin: 0 5px;
            border: 1px solid #ddd;
            text-decoration: none;
            color: #3498db;
        }
        .pagination a.active {
            background-color: #3498db;
            color: #fff;
            border: 1px solid #3498db;
        }
    </style>
</head>
<body>
    <div id="body">
        <h1>My Direct Messages</h1>
        <?php if (count($conversationDetails) > 0): ?>
            <?php foreach ($conversationDetails as $conversation): ?>
                <div class="conversation-item">
                    <h3>DM with <?php echo htmlspecialchars($conversation['username']); ?></h3>
                    <p>Latest Message: <?php echo htmlspecialchars($conversation['latest_message']); ?></p>
                    <?php if ($conversation['is_unread'] == 1): ?>
                        <p class="notification">You have unread messages!</p>
                    <?php else: ?>
                        <p>No unread messages</p>
                    <?php endif; ?>
                    <form action="/dms" method="GET">
                        <input type="hidden" name="touser" value="<?php echo htmlspecialchars($conversation['other_user_id']); ?>">
                        <input type="submit" value="Enter DM" class="enter-button">
                    </form>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>You have no direct messages.</p>
        <?php endif; ?>

        <h1>Group Chats</h1>
        <?php if (count($groupChats) > 0): ?>
            <?php foreach ($groupChats as $chat): ?>
                <div class="group-chat-item">
                    <h3>Group Chat: <?php echo htmlspecialchars($chat['name']); ?></h3>
                    <form action="/gc" method="GET">
                        <input type="hidden" name="chat_id" value="<?php echo htmlspecialchars($chat['id']); ?>">
                        <input type="submit" value="Enter Group Chat" class="view-button">
                    </form>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>You are not a member of any group chats.</p>
        <?php endif; ?>

        <div>
            <a href="/gccreate" class="create-button">Create Group Chat</a>
        </div>

        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?page=<?php echo $page - 1; ?>">&laquo; Previous</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?page=<?php echo $i; ?>" class="<?php echo ($i == $page) ? 'active' : ''; ?>">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>

            <?php if ($page < $totalPages): ?>
                <a href="?page=<?php echo $page + 1; ?>">Next &raquo;</a>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Function to request notification permission
        function requestNotificationPermission() {
            if (Notification.permission === 'granted') {
                // Permission already granted
                return;
            }

            if (Notification.permission !== 'denied') {
                // Request permission
                Notification.requestPermission().then(permission => {
                    if (permission === 'granted') {
                        console.log('Notification permission granted.');
                    } else {
                        console.log('Notification permission denied.');
                    }
                });
            }
        }

        // Function to show a notification
        function showNotification(title, options) {
            if (Notification.permission === 'granted') {
                new Notification(title, options);
            }
        }

        // Function to check for new messages
        function checkForNewMessages() {
            fetch('/check_new_messages')
                .then(response => response.json())
                .then(data => {
                    if (data.newMessages) {
                        showNotification('New Direct Message', {
                            body: 'You have a new message!',
                            icon: 'notification_icon.png' // Optional: path to an icon
                        });
                    }
                })
                .catch(error => console.error('Error checking for new messages:', error));
        }

        // Request notification permission on page load
        requestNotificationPermission();

        // Periodically check for new messages every 5 seconds
        setInterval(checkForNewMessages, 5000);
    </script>
</body>
</html>
