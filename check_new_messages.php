<?php
// Start the session
session_start();

// Include configuration and database connection
include('SiT_3/config.php');

// Ensure user is logged in
if (!isset($_SESSION['user_id']) || !$_SESSION['user_id']) {
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

// Get the current user's ID from session
$currentUserId = $_SESSION['user_id'];

// Prepare the SQL query to check for new messages
$query = "
    SELECT COUNT(*) AS new_messages_count
    FROM userdms
    WHERE touser = ? AND unread = 1
";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $currentUserId);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

// Prepare JSON response
$response = ['newMessages' => $row['new_messages_count'] > 0];

// Output JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Direct Messages</title>
    <style>
        /* Add your styles here */
    </style>
</head>
<body>
    <h1>Direct Messages</h1>

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
            fetch('/check_new_messages.php')  // Ensure the URL is correct
                .then(response => response.json()) // Parse JSON directly
                .then(data => {
                    console.log('Response data:', data); // Log the response data
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
