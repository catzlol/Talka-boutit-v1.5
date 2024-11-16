<?php
include('SiT_3/config.php');
include('SiT_3/header.php');

if (!$loggedIn) {
    exit();
}

$error = array();

if (isset($_POST['submit'])) {
    if (isset($_POST['name']) && isset($_POST['description'])) {
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $description = mysqli_real_escape_string($conn, $_POST['description']);

        // Validate forum board name using regular expression
        if (!preg_match('/^[a-zA-Z0-9\/\-\.\sáéíóúÁÉÍÓÚàèìòùÀÈÌÒÙäëïöüÄËÏÖÜâêîôûÂÊÎÔÛãñõÃÑÕ]+$/', $name)) {
            $error[] = "Invalid characters in forum board name.";
        } elseif (substr($name, 0, 2) === 't/') {
            // Append "Made by (user)" to the end of the description
            $description .= " Made by " . htmlspecialchars($userRow->username, ENT_QUOTES, 'UTF-8');

            // Insert the new forum board into the database
            $insertBoardSQL = "INSERT INTO `forum_boards` (`id`, `name`, `description`, `userid`) 
                               VALUES (NULL, '$name', '$description', '$userRow->id')";
            $insertBoard = $conn->query($insertBoardSQL);

            if ($insertBoard) {
                ob_clean(); // Clear output buffer
                header("Location: /"); // Redirect to the index page or wherever you want
                exit();
            } else {
                $error[] = "Error creating forum board. Please try again.";
            }
        } else {
            $error[] = "Forum board name must start with 't/'.";
        }
    } else {
        $error[] = "Please fill in all the fields.";
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Create Forum Board</title>
</head>

<body>
    <div id="body">
        <div id="box" style="padding: 10px;">
            <?php
            if (!empty($error)) {
                echo '<div style="background-color:#EE3333;margin:10px;padding:5px;color:white;">';
                foreach ($error as $errno) {
                    echo htmlspecialchars($errno, ENT_QUOTES, 'UTF-8') . "<br>";
                }
                echo '</div>';
            }
            ?>
            <form action="" method="POST">
                <label>Forum Board Name:</label><br>
                <input type="text" name="name"><br>
                <label>Description:</label><br>
                <textarea name="description" style="width:300px;height:140px;"></textarea><br>
                <input type="submit" name="submit" value="Create Forum Board">
            </form>
        </div>
        <div id="sidebar" style="float: right; width: 25%; padding: 10px; background-color: #F9FBFF;">
            <h3>Forum Board Creation Rules:</h3>
            <ul>
                <li>The forum board name must start with 't/'.</li>
                <li>Provide a meaningful description for your forum board.</li>
                <li>Follow community guidelines when creating boards.</li>
                <li>Allowed characters: a-z, 0-9, /, dashes, periods, and accents.</li>
            </ul>
        </div>
    </div>
</body>

</html>
