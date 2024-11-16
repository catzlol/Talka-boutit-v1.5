<?php
session_name("BRICK-SESSION");
session_start();
include('/usr/home/catzlol12/domains/talkaboutit.ct8.pl/public_html/SiT_3/config.php');

if (!isset($_SESSION['id'])) {
    header("Location: /login/");
    exit();
}

$currentID = $_SESSION['id'];

$bannedSQL = "SELECT * FROM `moderation` WHERE `active`='yes' AND `user_id`='$currentID'";
$banned = $conn->query($bannedSQL);

if ($banned->num_rows == 0) {
    header("Location: /"); // If you aren't banned you get sent back 
    exit();
}

$bannedRow = $banned->fetch_assoc();
$banID = $bannedRow['id'];
$currentDate = strtotime($curDate);
$banEnd = strtotime($bannedRow['issued']) + ($bannedRow['length'] * 60);

// Time
function getTimeDifference($startDate, $endDate) {
    $diff = $endDate - $startDate;
    $units = [
        'year'  => 365 * 24 * 60 * 60,
        'month' => 30 * 24 * 60 * 60,
        'week'  => 7 * 24 * 60 * 60,
        'day'   => 24 * 60 * 60,
        'hour'  => 60 * 60,
        'minute'=> 60,
        'second'=> 1,
    ];

    foreach ($units as $unit => $value) {
        if ($diff >= $value) {
            $quantity = floor($diff / $value);
            return $quantity . ' ' . ($quantity > 1 ? $unit . 's' : $unit);
        }
    }

    return 'less than a second';
}

?>

<html>
<head>
    <link rel="stylesheet" href="/style.css" type="text/css">
    <style>
        .admin-note {
            border: 1px solid;
            width: 400px;
            max-width: 100%;
            background-color: #F9FBFF;
            margin-bottom: 10px;
            padding: 10px;
            overflow: auto; /* Enable scrolling if the content exceeds the box height */
        }
    </style>
    <title>
        <?php
        if ($bannedRow['length'] == 0) {
            echo "Moderation Warning";
        } else {
            echo "Banned - Talka-boutit";
        }
        ?>
    </title>
</head>
<body>
    <div id="body">
        <div id="box">
            <div style="margin:10px;">
                <?php
                if ($bannedRow['length'] == 0) {
                    echo '<h3>Account Warning</h3>';
                } else {
                    echo '<h3>Account Blocked</h3>';
                }
                ?>
                <p class="intro">
                    <p>&nbsp; The Talka-Boutit team has decided to
                        <?php
                        if ($bannedRow['length'] == 0) {
                            echo 'warn the account';
                        } else {
                            echo 'block the account';
                        }
                        ?>
                    </p>
                    &nbsp; <?php if ($loggedIn) {
                                echo  $userRow->{'username'} . '</a></div>';
                            } ?>
                    <div style="padding-left: 10px;">
                        Date: <?php echo gmdate('m/d/Y', strtotime($bannedRow['issued'])); ?> <br>

                     <?php if (!empty(trim($bannedRow['offensive_content']))) { ?>
    Offensive Content:<br>
    <div class="admin-note">
        <?php echo $bannedRow['offensive_content']; ?>
    </div>
<?php } ?>

                        Moderator Note:<br>
                        <div class="admin-note">
                            <?php
                            if (!empty($bannedRow['admin_note'])) {
                                echo $bannedRow['admin_note'];
                            } else {
                                echo 'Your account has been ';
                                if ($bannedRow['length'] == 0) {
                                    echo 'warned by an admin for a violation of the Community Guidelines.';
                                } else {
                                    echo 'blocked by an admin for a violation of the Community Guidelines.';
                                }
                            }
                            ?>
                        </div>

                        Community Guidelines:
                        <ul style="padding-left: 10px;">
                            <li>• Be respectful to all users and staff of the website.</li>
                            <li>• Be civil, and don't post inappropriate things on the forums. </li>
                            <li>• Follow the Terms of Service, and the community guidelines, so the site is safe for everyone.</li>
                            <li>• Don't be rude to others who use the website.</li>
                        </ul>

                        <p style="padding-left: 10px;">You may submit an appeal to <a href="mailto:appeals@catzlol12.ct8.pl">appeals@catzlol12.ct8.pl</a> if you feel you have been wrongfully banned.</p>

                        <?php
                        $permanentBanThreshold = 36792000; // Approximately 1.17 years in seconds
                        if ($bannedRow['length'] >= $permanentBanThreshold) {
                            echo '<p style="padding-left: 10px;">Your account has been permanently disabled.</p>';
                        } elseif ($currentDate >= $banEnd) {
                            if (isset($_POST['unban'])) {
                                $unbanSQL = "UPDATE `moderation` SET `active`='no' WHERE `id`='$banID'";
                                $unban = $conn->query($unbanSQL);
                                header("Refresh:0");
                            }
                        ?>
                            <p style="padding-left: 10px;">You can now reactivate your account</p>
                            <form action="" method="POST">
                                <input type="submit" name="unban" value="Reactivate my account">
                            </form>
                        <?php
                        } else {
                            // Calculate the remaining time until the ban ends
                            $remainingTime = getTimeDifference($currentDate, $banEnd);
                            $reactivationDate = date('m-d-Y H:i:s', $banEnd);
                            echo '<p style="padding-left: 10px;">Your account has been disabled for ' . $remainingTime . '. You may reactivate on ' . $reactivationDate . '.</p>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
