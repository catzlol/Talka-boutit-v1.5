<?php
include('SiT_3/config.php');

// Ensure user is logged in
if (!isset($_POST['touser']) || !isset($_POST['fromuser']) || !isset($_POST['message']) ||
    !is_numeric($_POST['touser']) || !is_numeric($_POST['fromuser']) || empty($_POST['message'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid parameters.']);
    exit();
}

$touserId = (int)$_POST['touser'];
$fromuserId = (int)$_POST['fromuser'];
$message = $_POST['message'];

// Sanitize message input
$message = htmlspecialchars($message);

// Insert message into the `userdms` table
$query = "INSERT INTO userdms (touser, fromuser, message, datetime, ishidden, unread) VALUES (?, ?, ?, NOW(), 0, 1)";
$stmt = $conn->prepare($query);
$stmt->bind_param('iis', $touserId, $fromuserId, $message);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Message sent successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to send message.']);
}

$stmt->close();
$conn->close();
?>
