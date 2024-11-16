<?php
header('Content-Type: application/json');
include('/usr/home/catzlol12/domains/talkaboutit.ct8.pl/public_html/SiT_3/config.php'); // Absolute path to config.php
session_start();

$response = array('banned' => false);

if (isset($_SESSION['id'])) {
    $currentUserID = $_SESSION['id'];
    $bannedSQL = "SELECT * FROM `moderation` WHERE `active`='yes' AND `user_id`='$currentUserID'";
    $banned = $conn->query($bannedSQL);

    if ($banned->num_rows > 0) {
        $response['banned'] = true;
    }
}

echo json_encode($response);
?>
