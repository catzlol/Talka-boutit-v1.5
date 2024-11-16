<?php
include('SiT_3/config.php');
include('SiT_3/header.php');

if (!$loggedIn) {
    header("Location: index");
    exit();
}

// Check if the user has admin power
if ($power < 1) {
    header("Location: index");
    exit();
}

// Check if the ID parameter is set in the URL
if (isset($_GET['id'])) {
    $boardId = mysqli_real_escape_string($conn, $_GET['id']);

    // Check if the board exists
    $boardCheckSQL = "SELECT * FROM `forum_boards` WHERE `id`='$boardId'";
    $boardCheckResult = $conn->query($boardCheckSQL);

    if ($boardCheckResult && $boardCheckResult->num_rows > 0) {
        // Delete the board
        $deleteBoardSQL = "DELETE FROM `forum_boards` WHERE `id`='$boardId'";
        $deleteBoardResult = $conn->query($deleteBoardSQL);

        if ($deleteBoardResult) {
            header("Location: index");
            exit();
        } else {
            $error = 'Error deleting forum board.';
        }
    } else {
        $error = 'Forum board not found.';
    }
} else {
    $error = 'Invalid request.';
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Delete Forum Board</title>
</head>

<body>
    <div id="body">
        <div id="box" style="padding: 10px;">
            <?php
            if (isset($error)) {
                echo '<div style="background-color:#EE3333;margin:10px;padding:5px;color:white;">' . $error . '</div>';
            }
            ?>
            <h3>Delete Forum Board</h3>
            <p>Are you sure you want to delete this forum board?</p>
            <form action="" method="POST">
                <input type="submit" name="confirm" value="Yes, Delete">
                <a href="index">Cancel</a>
            </form>
        </div>
    </div>
</body>

</html>
