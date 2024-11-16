<?php
include("../SiT_3/config.php");
include("../SiT_3/header.php");

if (!$loggedIn) {
    header('Location: ../');
    die();
}

if (isset($_POST['send'])) {

    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);
    $uid = $_SESSION['id'];
    $date = date('Y-m-d');

    $rid = mysqli_real_escape_string($conn, intval($_POST['recipient']));


    if (empty($title) || empty($message)) {
        die('Missing Parameter');
    }

    // Define the banned sites and corresponding message
    $bannedSites = array(
        'goatse.info',
        'pornhub.com',
        'example.com',
        'anotherbannedsite.com',
        'bmwforum.co',
        'catsnthing.com',
        // Add more banned sites here
    );

    // Check if the message contains any banned sites
    foreach ($bannedSites as $site) {
        if (stripos($message, $site) !== false) {
            // Log the ban in the moderation table
            $banContent = $conn->real_escape_string($site);
            $adminNote = "[AUTOMOD] You have been flagged for sending inappropriate links. If this is a false positive please contact staff.";
            $issued = time(); // Set issued to current timestamp
            $length = 4320; // 3 days in minutes

            // Format the date for logging
            $dateFormatted = date("Y-m-d H:i:s", $issued);

            $banSQL = "INSERT INTO `moderation` (`user_id`, `admin_id`, `offensive_content`, `admin_note`, `issued`, `length`, `active`)
                       VALUES ('$uid', '0', '$banContent', '$adminNote', '$dateFormatted', '$length', '1')";
            $banQuery = $conn->query($banSQL);

            // Redirect to the banned page
            header("Location: http://catzlol12.ct8.pl/banned/");
            exit();
        }
    }

    // Proceed with sending the message if no banned links were found
    $sendMessageSQL = "INSERT INTO `messages` (`id`, `author_id`, `recipient_id`, `date`, `title`, `message`, `read`) VALUES (NULL, '$uid', '$rid', '$date', '$title', '$message', '0')";
    $sendMessage = $conn->query($sendMessageSQL);

    if ($sendMessage) {
        header('Location: index');
    } else {
        echo $sendMessageSQL;
        die('There was an error trying to send your message');
    }
}

if (isset($_POST['recipient'])) {
    $recipient = mysqli_real_escape_string($conn, $_POST['recipient']);
    if (isset($_POST['title'])) {
        $title = mysqli_real_escape_string($conn, $_POST['title']);
    } else {
        $title = '';
    }
    if (isset($_POST['message'])) {
        $message = $_POST['message'];
    } else {
        $message = '';
    }
} else {
    header('Location: index');
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Compose - Brick Hill</title>
</head>

<body>
    <div id="body">
        <div id="box">
            <form style="margin:10px;" action="?" method="POST">
                <h4>Title</h4>
                <input type="text" name="title" value="<?php echo $title; ?>"><br>
                <h4>To</h4>
                <?php
                $id = $recipient;
                $sqlUser = "SELECT * FROM `beta_users` WHERE  `id` = '$id'";
                $userResult = $conn->query($sqlUser);
                $userRow = $userResult->fetch_assoc();

                echo '<input type="hidden" name="recipient" value="' . $userRow['id'] . '">' . $userRow['username'];
                ?><br>
                <h4>Message</h4>
                <textarea style="width: 540px; height: 150px;" name="message"><?php echo $message; ?></textarea><br>
                <input style="background-color:#77B9FF; color:#FFF; border:1px solid #000;" type="submit" name="send" value="Send">
            </form>
        </div>
    </div>
</body>
<?php
include("../SiT_3/footer.php");
?>

</html>
