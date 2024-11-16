<?php
include('SiT_3/config.php');
include('SiT_3/header.php');

// Ensure user is logged in
if (!$loggedIn) {
    header("Location: /"); // Redirect to the login page or any other appropriate page
    exit();
}

// Get the username and power from the userRow variable
$username = $userRow->{'username'};
$power = $userRow->{'power'};

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['clearChat'])) {
        // Clear the chat by overwriting the file with an empty string
        file_put_contents('livechat.txt', 'Start of chat log');
        // Output a success message (you can handle this response as needed)
        echo "Chat cleared successfully!";
        exit();
    } elseif (isset($_POST['message']) && !empty($_POST['message'])) {
$message = htmlspecialchars($_POST['message'], ENT_QUOTES, 'UTF-8');
        $timestamp = date("Y-m-d H:i:s");

        // Format the message with username and timestamp
        $formattedMessage = "[$timestamp] $username: $message" . PHP_EOL;

        // Append the message to livechat.txt to show newest messages at the bottom
        file_put_contents('livechat.txt', file_get_contents('livechat.txt') . $formattedMessage);

        // Output a success message (you can handle this response as needed)
        echo "Message posted successfully!";
        exit();
    } else {
        // Output an error message (you can handle this response as needed)
        echo "Error: Message is empty!";
        exit();
    }
}
// Read the content of livechat.txt
$chatLog = file_get_contents('livechat.txt');
// Explode the content into an array of lines
$messages = explode(PHP_EOL, $chatLog);
// Reverse the array to show newest messages at the top
$messages = array_reverse($messages);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Live Chat</title>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <style>
        /* Add your custom styles here */
        body {
            margin: 0;
            font-family: Arial, sans-serif;
        }

        #body {
            padding: 10px;
        }

        #chat-box {
            max-height: 300px;
            overflow-y: auto;
            border: 1px solid #ccc;
            padding: 10px;
            background-color: #fff; /* Set white background */
            /* Add a scroll bar to the chat box */
            scrollbar-width: thin;
            scrollbar-color: #888 #ddd;
        }

        #chat-box::-webkit-scrollbar {
            width: 12px;
        }

        #chat-box::-webkit-scrollbar-thumb {
            background-color: #888;
            border-radius: 6px;
        }

        #chat-box::-webkit-scrollbar-track {
            background-color: #ddd;
        }

        #chat-form {
            margin-top: 10px;
            display: flex;
            align-items: center;
        }

        #message {
            flex: 1;
            padding: 5px;
            margin-right: 5px;
        }

        #send-button {
            padding: 5px 10px;
            background-color: #4caf50;
            color: #fff;
            border: none;
            cursor: pointer;
        }

        /* Add style for the clear chat button */
        #clear-chat-button {
            margin-top: 10px;
            padding: 5px 10px;
            background-color: #f44336;
            color: #fff;
            border: none;
            cursor: pointer;
        }
    </style>
</head>

<body>
    <div id="body">
        <h1>Live Chat</h1>
        <?php
        // Display clear chat button only if the user is an admin
        if ($power >= 1) {
            echo '<button id="clear-chat-button">Clear Chat</button>';
        }
        ?>
        <div id="chat-box">
            <?php
            foreach ($messages as $message) {
    echo nl2br(htmlspecialchars($message, ENT_QUOTES, 'UTF-8'));
}
            ?>
        </div>
        <div>
            <form id="chat-form">
                <input type="text" id="message" placeholder="Type your message">
                <button id="send-button" type="submit">Send</button>
            </form>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            function updateChat() {
                // Fetch and update the chat messages from livechat.txt using AJAX
                $.get("livechat.txt", function (data) {
                    // Explode the content into an array of lines
                    var messages = data.split("\n");
                    // Reverse the array to show newest messages at the top
                    messages = messages.reverse();
                    // Join the array back into a string
                    var formattedMessages = messages.join("<br>");
                    // Update the chat box
                    $("#chat-box").html(formattedMessages);
                });
            }

            // Initial chat update
            updateChat();

            // Auto-update every 5 seconds (adjust as needed)
            setInterval(updateChat, 5000);

            // Handle form submission
            $("#chat-form").submit(function (e) {
                e.preventDefault();
                // Get the message from the input field
                var message = $("#message").val();
                // Send an AJAX POST request to the same script
                $.post("", { message: message, username: "<?php echo $username; ?>" }, function (response) {
                    // Log the response to the console (for debugging)
                    console.log(response);
                    // Clear the input field after submitting
                    $("#message").val("");
                    // Update the chat immediately after posting
                    updateChat();
                });
            });

            // Handle click on the clear chat button
            $("#clear-chat-button").click(function () {
                // Send an AJAX POST request to clear the chat
                $.post("", { clearChat: true }, function (response) {
                    // Log the response to the console (for debugging)
                    console.log(response);
                    // Update the chat immediately after clearing
                    updateChat();
                });
            });
        });
    </script>

</body>

</html>
