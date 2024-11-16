<?php
include('SiT_3/config.php');

$chatId = isset($_GET['chat_id']) ? intval($_GET['chat_id']) : 0;
if ($chatId <= 0) {
    die("Invalid chat ID.");
}

// Fetch existing messages for the chat
$messages = [];
$stmt = $conn->prepare("SELECT * FROM gc_messages WHERE chat_id = ? ORDER BY created_at ASC");
$stmt->bind_param('i', $chatId);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $messages[] = $row;
}

foreach ($messages as $msg) {
    echo '<div class="message">';
    echo '<strong>User ' . htmlspecialchars($msg['user_id']) . ':</strong>';
    echo '<p>' . htmlspecialchars($msg['message']) . '</p>';
    echo '<small>' . htmlspecialchars($msg['created_at']) . '</small>';
    echo '</div>';
}
?>
