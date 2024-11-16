<?php
include('SiT_3/config.php');

// Ensure user is logged in
if (!isset($_GET['touser']) || !isset($_GET['fromuser']) || !is_numeric($_GET['touser']) || !is_numeric($_GET['fromuser'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid parameters.']);
    exit();
}

$touserId = (int)$_GET['touser'];
$fromuserId = (int)$_GET['fromuser'];

// Fetch messages and usernames
$query = "SELECT userdms.*, beta_users.username 
          FROM userdms 
          LEFT JOIN beta_users ON userdms.fromuser = beta_users.id
          WHERE (userdms.touser = ? AND userdms.fromuser = ?) OR (userdms.touser = ? AND userdms.fromuser = ?)
          ORDER BY userdms.datetime ASC";
$stmt = $conn->prepare($query);
$stmt->bind_param('iiii', $touserId, $fromuserId, $fromuserId, $touserId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $messages = [];
    while ($row = $result->fetch_assoc()) {
        // Format message content with hyperlinks
        $messageContent = htmlspecialchars($row['message']); // Sanitize message input
        $messageContent = preg_replace('/(https?:\/\/\S+)/', '<a href="$1" target="_blank">$1</a>', $messageContent); // Convert URLs to clickable links

        // Format datetime to 12-hour format with AM/PM
        $datetime = date('Y-m-d h:i:s A', strtotime($row['datetime']));

        // Construct message array with message ID and formatted datetime
        $message = [
            'id' => $row['ID'],
            'message' => $messageContent . '<br><small>ID: ' . $row['ID'] . ' - Date: ' . $datetime . '</small>',
            'fromuser' => $row['fromuser'],
            'username' => ($row['fromuser'] == $fromuserId) ? 'You' : '<a href="profile.php?id=' . $row['fromuser'] . '">' . $row['username'] . '</a>', // Display 'You' for current user and hyperlink username
            'deleteButton' => ($row['fromuser'] == $fromuserId) ? '<button onclick="deleteMessage(' . $row['ID'] . ')">Delete</button>' : ''
        ];
        $messages[] = $message;
    }
    echo json_encode(['success' => true, 'messages' => $messages]);
} else {
    echo json_encode(['success' => true, 'messages' => []]); // No messages found
}

$stmt->close();
$conn->close();
?>
