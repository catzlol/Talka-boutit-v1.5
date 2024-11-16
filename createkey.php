<?php
// Absolute paths again, because its being unkind.
include('/usr/home/catzlol12/domains/talkaboutit.ct8.pl/public_html/SiT_3/config.php');
include('/usr/home/catzlol12/domains/talkaboutit.ct8.pl/public_html/SiT_3/header.php');

if (!isset($_SESSION['id'])) {
    header("Location: /login");
    exit();
}

$userID = $_SESSION['id'];
$error = [];
$canCreateKey = true;

// Check if the user used a registration key
$checkUsedKeySQL = "SELECT `usedkey` FROM `beta_users` WHERE `id` = '$userID' LIMIT 1";
$checkUsedKeyResult = $conn->query($checkUsedKeySQL);

if ($checkUsedKeyResult && $checkUsedKeyResult->num_rows > 0) {
    $user = $checkUsedKeyResult->fetch_assoc();
    if ($user['usedkey'] == 1) {
        $canCreateKey = false;
        $error[] = "You cannot create a key because you were invited using one.";
    }
}

// Function to generate a random key
function generateRandomKey($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%^&*()_+-=';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($canCreateKey) {
        // Validate checkboxes
        if (empty($_POST['agree1']) || empty($_POST['agree2']) || empty($_POST['agree3']) || empty($_POST['agree4'])) {
            $error[] = 'You must agree to all the terms to create a key.';
        }

        if (empty($error)) {
            // Generate key
            $keyContent = generateRandomKey();

            // Insert key into reg_keys table
            $keySQL = "INSERT INTO `reg_keys` (`key_content`, `made_by`) VALUES ('$keyContent', '$userID')";
            if ($conn->query($keySQL)) {
                $success = "Key successfully created. The key is: $keyContent";
            } else {
                $error[] = 'Database error while creating key.';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Registration Key</title>
</head>
<body>
    <div id="body">
        <div id="box">
            <h3>Create a Registration Key</h3>
            <p>If the site's account creation page requires a Registration Key, then it means the site is privatized at the moment and you will not be able to create an account without one. With this page, you can create a Registration Key for friends, or anyone you know to sign up. The key can be used endlessly unless removed. You cannot create a registration key if you signed up with one.</p>
            
            <?php if (!empty($error)): ?>
                <div style="background-color:#EE3333;margin:10px;padding:5px;color:white;">
                    <?php foreach ($error as $line): ?>
                        <?php echo $line . '<br>'; ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($success)): ?>
                <div style="background-color:#33EE33;margin:10px;padding:5px;color:white;">
                    <?php echo $success; ?>
                </div>
            <?php endif; ?>

            <?php if ($canCreateKey): ?>
                <form action="" method="POST">
                    <h4>Terms and Conditions:</h4>
                    <input type="checkbox" name="agree1" required> I will not use this to give a banned user a workaround.<br>
                    <input type="checkbox" name="agree2" required> I will not use this to create alternative accounts of my own, or for another user.<br>
                    <input type="checkbox" name="agree3" required> I understand that my keys will be invalidated if a moderator deems that they are being abused.<br>
                    <input type="checkbox" name="agree4" required> I understand that I am responsible for the behavior of the users invited, and a moderator may take disciplinary action against my account if invitees demonstrate poor conduct.<br><br>
                    
                    <input type="submit" value="Generate Key">
                </form>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
