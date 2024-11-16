<?php
include('/usr/home/catzlol12/domains/talkaboutit.ct8.pl/public_html/SiT_3/config.php');

header('Content-Type: application/json');
session_start();

// Default response
$response = array('sessionValid' => false);

if (isset($_SESSION['id'])) {
    $response['sessionValid'] = true;
}

echo json_encode($response);
?>
