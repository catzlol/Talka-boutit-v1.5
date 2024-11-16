<?php
include('SiT_3/config.php');
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$chatId = isset($data['chat_id']) ? intval($data['chat_id']) : 0;
$userId = isset($data['user_id']) ? intval($data['user_id']) : 0;
$message = isset($data['message']) ? trim($data['message']) : '';

if ($chatId <= 0 || $userId <= 0 || $message === '') {
    echo json_encode(['success' => false]);
    exit();
}

$stmt = $conn->prepare("INSERT INTO gc_messages (chat_id, user_id, message, datetime) VALUES (?, ?, ?, NOW())");
$stmt->bind_param('iis', $chatId, $userId, $message);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false]);
}
?>
