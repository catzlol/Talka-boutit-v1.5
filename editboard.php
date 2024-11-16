<?php
include('SiT_3/config.php');
include('SiT_3/header.php');

if (!$loggedIn) {
    header("Location: /");
}

// Check if the user has admin powers
if ($power < 2) {
    header("Location: /");
}

$error = array();

if (isset($_POST['submit'])) {
    // Check if the ID is provided in the form
    if (isset($_POST['boardId'])) {
        $boardId = mysqli_real_escape_string($conn, $_POST['boardId']);
        // Redirect to deleteboard.php without any specific action
header("Location: https://catzlol12.ct8.pl/deleteboard?id=" . $boardId);
        exit(); // Ensure that no further code is executed after the redirect
    } else {
        $error[] = 'Invalid board ID';
    }
}

?>

<!DOCTYPE html>
<head>
    <title>Delete Subtalk</title>
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
            <form action="" method="POST">
                <h3>Delete Subtalk</h3>
                <label>Subtalk ID:</label>
                <input type="text" name="boardId" required>
                <input type="submit" name="submit" value="Delete">
            </form>
        </div>
    </div>
</body>

</html>
