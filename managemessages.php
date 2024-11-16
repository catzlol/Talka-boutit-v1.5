<?php
include('SiT_3/config.php');
include('SiT_3/header.php');

// Ensure user is logged in and has admin power
if (!$loggedIn || $power < 1) {
    header("Location: /");
    exit();
}

// Function to sanitize input
function sanitizeInput($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

// Initialize variables
$searchTerm = '';
$searchById = false;
$messages = [];
$perPage = 10; // Messages per page
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $perPage;

// Handle search
if (isset($_POST['search'])) {
    $searchTerm = sanitizeInput($_POST['search']);
    if (is_numeric($searchTerm)) {
        // Search by ID
        $query = "SELECT * FROM userdms WHERE ID = ? ORDER BY datetime DESC LIMIT ? OFFSET ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('iii', $searchTerm, $perPage, $offset);
        $searchById = true;
    } else {
        // Search by message content
        $query = "SELECT * FROM userdms WHERE message LIKE ? ORDER BY datetime DESC LIMIT ? OFFSET ?";
        $stmt = $conn->prepare($query);
        $searchTerm = "%{$searchTerm}%";
        $stmt->bind_param('sii', $searchTerm, $perPage, $offset);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $messages[] = $row;
    }
} else {
    // Fetch all messages if no search term
    $query = "SELECT * FROM userdms ORDER BY datetime DESC LIMIT ? OFFSET ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ii', $perPage, $offset);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $messages[] = $row;
    }
}

// Get total number of messages for pagination
$countQuery = $searchById ? "SELECT COUNT(*) AS total FROM userdms WHERE ID = ?" : "SELECT COUNT(*) AS total FROM userdms WHERE message LIKE ?";
$countStmt = $conn->prepare($countQuery);
if ($searchById) {
    $countStmt->bind_param('i', $searchTerm);
} else {
    $countStmt->bind_param('s', $searchTerm);
}
$countStmt->execute();
$countResult = $countStmt->get_result();
$totalMessages = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalMessages / $perPage);

// Handle message deletion
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $messageId = (int)$_GET['delete'];
    $deleteQuery = "DELETE FROM userdms WHERE ID = ?";
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bind_param('i', $messageId);
    if ($stmt->execute()) {
        header("Location: managemessages");
        exit();
    } else {
        echo "<p>Error deleting message.</p>";
    }
}

// Handle message editing
if (isset($_POST['edit']) && isset($_POST['message_id']) && isset($_POST['new_message'])) {
    $messageId = (int)$_POST['message_id'];
    $newMessage = sanitizeInput($_POST['new_message']);
    $updateQuery = "UPDATE userdms SET message = ? WHERE ID = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param('si', $newMessage, $messageId);
    if ($stmt->execute()) {
        header("Location: managemessages");
        exit();
    } else {
        echo "<p>Error updating message.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Messages</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
        }
        #body {
            padding: 10px;
            background-color: #fff;
            width: 900px;
            margin: auto;
            margin-top: 10px;
        }
        #message-list {
            border: 1px solid #ccc;
            padding: 10px;
            background-color: #fff;
        }
        .message-item {
            border-bottom: 1px solid #eee;
            padding: 5px 0;
        }
        .message-item:last-child {
            border-bottom: none;
        }
        .delete-button, .edit-button {
            background-color: #e74c3c;
            color: #fff;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            margin-right: 5px;
        }
        .edit-button {
            background-color: #3498db;
        }
        input[type="text"], input[type="number"] {
            padding: 5px;
            border: 1px solid #ccc;
            margin-right: 5px;
        }
        input[type="submit"] {
            background-color: #2ecc71;
            color: #fff;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
        }
        .edit-form {
            display: inline-block;
            margin-left: 10px;
        }
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 10px;
        }
        .pagination a {
            padding: 5px 10px;
            margin: 0 5px;
            border: 1px solid #ccc;
            color: #333;
            text-decoration: none;
        }
        .pagination a.active {
            background-color: #77B9FF;
            color: #fff;
        }
    </style>
</head>
<body>
    <div id="body">
        <h1>Manage Messages</h1>
        <form method="POST" action="">
            <input type="text" name="search" placeholder="Search by message content or ID..." value="<?php echo htmlspecialchars($searchTerm); ?>">
            <input type="submit" value="Search">
        </form>
        <div id="message-list">
            <?php if (count($messages) > 0): ?>
                <?php foreach ($messages as $message): ?>
                    <div class="message-item">
                        <strong>ID: <?php echo htmlspecialchars($message['ID']); ?> | From User ID <?php echo htmlspecialchars($message['fromuser']); ?> to User ID <?php echo htmlspecialchars($message['touser']); ?>:</strong>
                        <p><?php echo htmlspecialchars($message['message']); ?> (<?php echo htmlspecialchars($message['datetime']); ?>)</p>
                        <a href="?delete=<?php echo htmlspecialchars($message['ID']); ?>" class="delete-button">Delete</a>
                        <form method="POST" action="" class="edit-form">
                            <input type="hidden" name="message_id" value="<?php echo htmlspecialchars($message['ID']); ?>">
                            <input type="text" name="new_message" placeholder="New message">
                            <input type="submit" name="edit" value="Edit" class="edit-button">
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No messages found.</p>
            <?php endif; ?>
        </div>

        <!-- Pagination -->
        <div class="pagination">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?page=<?php echo $i; ?>" class="<?php echo $i === $page ? 'active' : ''; ?>"><?php echo $i; ?></a>
            <?php endfor; ?>
        </div>
    </div>
</body>
</html>
