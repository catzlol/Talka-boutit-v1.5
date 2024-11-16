<?php
include('SiT_3/config.php');
include('SiT_3/header.php');

if (!$loggedIn) {
    header("Location: index");
}

$error = array();
$ipAddress = '';

if ($power >= 1) {
    if (isset($_GET['id'])) {
        $user = mysqli_real_escape_string($conn, $_GET['id']);
        $admin = $_SESSION['id'];

        // Fetch the user's IP address from the beta_users table
        $ipQuery = "SELECT ip FROM `beta_users` WHERE id = '$user'";
        $ipResult = $conn->query($ipQuery);
        if ($ipResult && $ipResult->num_rows > 0) {
            $ipRow = $ipResult->fetch_assoc();
            $ipAddress = $ipRow['ip'];
        }

        if (isset($_POST['submit'])) {
            $note = isset($_POST['no_moderation_note']) ? '' : str_replace("'", "\'", $_POST['note']);
            $length = mysqli_real_escape_string($conn, $_POST['length']);
            $offensiveContent = '';

            // Fetch offensive content from forum posts
            if (isset($_POST['forum_post_id']) && is_numeric($_POST['forum_post_id'])) {
                $forumPostId = mysqli_real_escape_string($conn, $_POST['forum_post_id']);
                $forumPostSQL = "SELECT title, body, date FROM `forum_threads` WHERE id = '$forumPostId'";
                $forumPostResult = $conn->query($forumPostSQL);
                if ($forumPostResult && $forumPostResult->num_rows > 0) {
                    $forumPostRow = $forumPostResult->fetch_assoc();
                    $offensiveContent .= 'Forum Post - Title: ' . $forumPostRow['title'] . PHP_EOL .
                        'Body: ' . $forumPostRow['body'] . PHP_EOL .
                        'Date: ' . $forumPostRow['date'] . PHP_EOL;
                }
            }

            // Fetch offensive content from subtalks
            elseif (isset($_POST['subtalk_id']) && is_numeric($_POST['subtalk_id'])) {
                $subtalkId = mysqli_real_escape_string($conn, $_POST['subtalk_id']);
                $subtalkSQL = "SELECT name, description FROM `forum_boards` WHERE id = '$subtalkId'";
                $subtalkResult = $conn->query($subtalkSQL);
                if ($subtalkResult && $subtalkResult->num_rows > 0) {
                    $subtalkRow = $subtalkResult->fetch_assoc();
                    $offensiveContent .= 'Subtalk - Name: ' . $subtalkRow['name'] . PHP_EOL .
                        'Description: ' . $subtalkRow['description'] . PHP_EOL;
                }
            }

            // Fetch offensive content from messages
            elseif (isset($_POST['message_id']) && is_numeric($_POST['message_id'])) {
                $messageId = mysqli_real_escape_string($conn, $_POST['message_id']);
                $messageSQL = "SELECT title, message, date FROM `messages` WHERE id = '$messageId'";
                $messageResult = $conn->query($messageSQL);
                if ($messageResult && $messageResult->num_rows > 0) {
                    $messageRow = $messageResult->fetch_assoc();
                    $offensiveContent .= 'Message - Title: ' . $messageRow['title'] . PHP_EOL .
                        'Message: ' . $messageRow['message'] . PHP_EOL .
                        'Date: ' . $messageRow['date'] . PHP_EOL;
                }
            }

            // Include additional offensive content if provided
            $offensiveContent .= isset($_POST['offensive_content']) ? ($_POST['offensive_content'] . PHP_EOL) : '';

            // Ban the user
            $banSQL = "INSERT INTO `moderation` (`id`,`user_id`,`admin_id`,`admin_note`,`issued`,`length`,`active`,`offensive_content`) VALUES (NULL ,'$user','$admin','$note', '$curDate','$length','yes', '$offensiveContent')";
            $ban = $conn->query($banSQL);

            // Log admin action
            $action = 'Banned user ' . $user . ' for ' . $length . ' minutes';
            $date = date('d-m-Y H:i:s');
            $adminSQL = "INSERT INTO `admin` (`id`,`admin_id`,`action`,`time`) VALUES (NULL ,  '$admin',  '$action',  '$date')";
            $admin = $conn->query($adminSQL);

            // IP Ban
            if (isset($_POST['ip_ban']) && !empty($_POST['ip_ban'])) {
                $userIP = $_POST['ip_ban'];
                $ipBanSQL = "INSERT INTO `ipbans` (`id`, `ip`, `date`, `moderator`, `user_id`) VALUES (NULL, '$userIP', '$curDate', '$admin', '$user')";
                $ipBan = $conn->query($ipBanSQL);
            }

            header("Location: index");
        }

        // Unban functionality
        if (isset($_POST['unban'])) {
            // Remove user ban
            $unbanSQL = "UPDATE `moderation` SET `active`='no' WHERE `user_id`='$user'";
            $unban = $conn->query($unbanSQL);

            // Remove IP ban if it exists
            $removeIpBanSQL = "DELETE FROM `ipbans` WHERE `user_id`='$user'";
            $removeIpBan = $conn->query($removeIpBanSQL);

            header("Location: index");
        }
    } else {
        $error[] = 'Invalid user ID';
    }
} else {
    header("Location: index");
}
?>

<!DOCTYPE html>
<head>
    <title>Ban User</title>
</head>

<body>
    <div id="body">
        <div id="box" style="padding:10px;">
            <?php
            if (!empty($error)) {
                echo '<div style="background-color:#EE3333;margin:10px;padding:5px;color:white;">';
                foreach ($error as $errno) {
                    echo $errno . "<br>";
                }
                echo '</div>';
            }
            if (isset($_POST['submit']) && empty($error)) {
                echo '<h3>User has been banned</h3>';
            }
            ?>
            <form action="" method="POST">
                <!-- IP Ban Option -->
                <label>IP Ban (Optional):</label>
                <input type="text" name="ip_ban" placeholder="Enter IP to ban" value="<?php echo htmlspecialchars($ipAddress); ?>"><br><br>
                
                <!-- Ban User Section -->
                Ban user <?php echo $_GET['id']; ?><br>
                <label>Moderator Note:</label><br>
                <textarea name="note" style="width:300px;height:140px;"></textarea>
                <br>
                <input type="checkbox" name="no_moderation_note" value="1"> No Moderation Note<br>
                <label>Offensive Content (Optional):</label><br>
                <textarea name="offensive_content" style="width:300px;height:140px;"><?php echo htmlspecialchars($content); ?></textarea>
                <br>
                <label>Forum Post ID (Optional):</label>
                <input type="text" name="forum_post_id"><br>
                <label>Subtalk ID (Optional):</label>
                <input type="text" name="subtalk_id"><br>
                <label>Message ID (Optional):</label>
                <input type="text" name="message_id"><br>
                <label>Ban Length (Minutes):</label>
                <input type="text" name="length"><br>
                <input type="submit" name="submit" value="Ban User">
            </form>

            <!-- Form for unbanning user -->
            <form action="" method="POST">
                <input type="hidden" name="unban" value="1">
                <input type="submit" value="Remove Ban">
            </form>

            <ul>
                Ban Length
                <li>• 0 (Warning) </li>
                <li>• 60 (1 hour) </li>
                <li>• 1440 (1 day) </li>
                <li>• 4320 (3 days)</li>
                <li>• 10080 (1 week)</li>
                <li>• 20160 (2 weeks)</li>
                <li>• 2147483647 (Forever)</li>
            </ul>
        </div>
    </div>
</body>

</html>
