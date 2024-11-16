<?php
include('SiT_3/config.php');
include('SiT_3/header.php');

if (!$loggedIn) {
    header("Location: index");
    exit();
}

$error = array();
$cooldown = 30; // 30-second cooldown

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if the cooldown period has passed
    if (isset($_SESSION['last_avatar_update']) && time() - $_SESSION['last_avatar_update'] < $cooldown) {
        $error[] = "Please wait $cooldown seconds before updating your avatar again.";
    } else {
        if (isset($_POST['use_default'])) {
            // Copy the default avatar to the user's avatar folder
            $userId = $_SESSION['id'];
            $defaultAvatarPath = 'default.png';
            $userAvatarPath = "images/avatars/{$userId}.png";

            if (copy($defaultAvatarPath, $userAvatarPath)) {
                echo 'Default avatar set successfully!';
                $_SESSION['last_avatar_update'] = time(); // Update the last avatar update timestamp
                exit();
            } else {
                $error[] = 'Failed to set default avatar.';
            }
        } elseif (isset($_FILES['avatar'])) {
            $file = $_FILES['avatar'];

            // Check if the file is an image and has a PNG JPEG or GIF extension
            $allowedTypes = ['image/png', 'image/jpeg', 'image/gif'];
            $fileType = mime_content_type($file['tmp_name']);

            if (!in_array($fileType, $allowedTypes)) {
                $error[] = 'Invalid file type. Please upload a PNG, GIF or JPEG image.';
            }

            if (empty($error)) {
                // Get the user ID and construct the new file name
                $userId = $_SESSION['id'];
                $newFileName = "images/avatars/{$userId}.png";

                // Move the uploaded file to the avatars directory
                if (move_uploaded_file($file['tmp_name'], $newFileName)) {
                    echo 'Avatar uploaded successfully!';
                    $_SESSION['last_avatar_update'] = time(); // Update the last avatar update timestamp
                    exit();
                } else {
                    $error[] = 'Failed to move the uploaded file.';
                }
            }
        } else {
            $error[] = 'No file uploaded.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Upload Avatar</title>
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
            ?>
            <form action="" method="POST" enctype="multipart/form-data">
                <label>Upload Avatar (Square image recommended):</label><br>
                <input type="file" name="avatar" accept="image/png, image/jpeg, image/gif">
                <label><input type="checkbox" name="use_default"> Use Default Avatar</label><br>
                <input type="submit" value="Set Avatar">
            </form>
        </div>
    </div>
</body>
</html>
