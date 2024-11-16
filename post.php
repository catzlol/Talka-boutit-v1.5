<?php
include('SiT_3/config.php');

// Check if the user is logged in
if (!$loggedIn) {
    // Return an error response
    http_response_code(403); // Forbidden
    echo "Error: You are not logged in.";
    exit;
}

// Check if the message is set in the POST data
if (isset($_POST['message'])) {
    // Get the message from the POST data
    $message = $_POST['message'];

    // Get the current username (assuming you store it in your user system)
    $username = $usernameFromYourSystem; // Replace with the actual way you retrieve the username

    // Get the current date and time
    $dateTime = date("Y-m-d H:i:s");

    // Format the message with username, date, and time
    $formattedMessage = "[" . $dateTime . "] " . $username . ": " . $message . "\n";

    // Append the formatted message to the livechat.txt file
    file_put_contents('livechat.txt', $formattedMessage, FILE_APPEND);
    
    // Return a success response
    echo "Message posted successfully.";
} else {
    // Return an error response
    http_response_code(400); // Bad Request
    echo "Error: Message not provided.";
}
?>
