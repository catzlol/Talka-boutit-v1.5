<?php
include('SiT_3/config.php');
include('SiT_3/header.php');

// Ensure user is logged in
if (!$loggedIn) {
    header("Location: /");
    exit();
}

$currentUserId = $userRow->id; // Get the current user ID from the session

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $chatId = intval($_POST['chat_id']);
    $message = trim($_POST['message']);

    if (!empty($message)) {
        // Insert the message into gc_messages
        $stmt = $conn->prepare("INSERT INTO gc_messages (chat_id, user_id, message) VALUES (?, ?, ?)");
        $stmt->bind_param('iis', $chatId, $currentUserId, $message);

        if ($stmt->execute()) {
            echo "Message sent successfully.";
        } else {
            echo "Failed to send message.";
        }
    } else {
        echo "Message cannot be empty.";
    }
}
?>
