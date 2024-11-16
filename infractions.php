<?php
include('SiT_3/config.php');
include('SiT_3/header.php');

if(!$loggedIn) {
    header("Location: ../");
    die();
}

$userID = $userRow->{'id'}; // Get the user ID from the session

// Query to check for infractions in the moderation table
$infractionSQL = "SELECT * FROM `moderation` WHERE `user_id` = '$userID'";
$infractionResult = $conn->query($infractionSQL);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Infractions - Brick Hill</title>
</head>
<body>
    <div id="body">
        <div id="box" style="width:100%;">
            <div id="subsect">
                <h4 style="padding-left:10px;">Infractions</h4>
            </div>
            <div id="settingsInfo">
                <h6 id="settingsUpperThird">Your Infractions</h6>
                <?php
                if ($infractionResult->num_rows > 0) {
                    // If there are infractions
                    $numInfractions = $infractionResult->num_rows;
                    echo "<p>You have $numInfractions infractions:</p>";
                    echo "<ul>";
                    while ($row = $infractionResult->fetch_assoc()) {
                        echo "<li><strong>Date Issued:</strong> " . date('Y-m-d H:i:s', strtotime($row['issued'])) . " | <strong>Admin Note:</strong> " . htmlspecialchars($row['admin_note']) . "</li>";
                    }
                    echo "</ul>";
                } else {
                    // If there are no infractions
                    echo "<p>You do not have any infractions.</p>";
                }
                ?>
            </div>
        </div>
    </div>
    <?php
    include("../SiT_3/footer.php");
    ?>
</body>
</html>
