<?php

include('/usr/home/catzlol12/domains/talkaboutit.ct8.pl/public_html/SiT_3/config.php');  
  

// Initialize the error array
$error = array();

// Add the specific error message
$error[] = "We're sorry, but your network has been blocked from accessing Talka-boutit for multiple or severe violations of our Terms of Service.";

// HTML structure
?>
<!DOCTYPE html>
<html>

<head>
    <title>Access Blocked</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .error-container {
            background-color: #EE3333;
            margin: 10px;
            padding: 20px;
            color: white;
            border-radius: 5px;
        }
    </style>
</head>

<body>
    <div class="error-container">
        <?php
        // Display the error messages
        foreach ($error as $errno) {
            echo htmlspecialchars($errno, ENT_QUOTES, 'UTF-8') . "<br>";
        }
        ?>
    </div>
</body>

</html>


