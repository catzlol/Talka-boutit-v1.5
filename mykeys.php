<?php
// Using the absolute path because its not being nice.
include('/usr/home/catzlol12/domains/talkaboutit.ct8.pl/public_html/SiT_3/config.php');
include('/usr/home/catzlol12/domains/talkaboutit.ct8.pl/public_html/SiT_3/header.php');

if (!isset($_SESSION['id'])) {
    header("Location: /login");
    exit();
}

$userID = $_SESSION['id'];
$error = [];

// Delete a key
function deleteKey($conn, $keyID, $userID) {
    $keyID = intval($keyID);
    $deleteSQL = "DELETE FROM `reg_keys` WHERE `id` = $keyID AND `made_by` = $userID";
    return $conn->query($deleteSQL);
}

// Check for key deletion
if (isset($_GET['delete'])) {
    $keyID = $_GET['delete'];
    if (deleteKey($conn, $keyID, $userID)) {
        header("Location: /mykeys");
        exit();
    } else {
        $error[] = 'Error deleting key.';
    }
}

// Fetch keys
$keySQL = "SELECT * FROM `reg_keys` WHERE `made_by` = $userID";
$keyResult = $conn->query($keySQL);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Keys List</title>
</head>
<body>
    <div id="body">
        <div id="box">
            <div id="subsect">
                <h3>Your Registration Keys</h3>
            </div>
            <?php if (!empty($error)): ?>
                <div style="background-color:#EE3333;margin:10px;padding:5px;color:white;">
                    <?php foreach ($error as $line): ?>
                        <?php echo $line . '<br>'; ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <table border="1" style="margin:10px;">
                <thead>
                    <tr>
                        <th>Key</th>
                        <th>Date Created</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($keyResult && $keyResult->num_rows > 0): ?>
                        <?php while ($row = $keyResult->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['key_content']); ?></td>
                                <td><?php echo htmlspecialchars($row['date_created']); ?></td>
                                <td><a href="?delete=<?php echo htmlspecialchars($row['id']); ?>" onclick="return confirm('Are you sure you want to delete this key?');">Delete</a></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3">No keys found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            <div style="margin:10px;border:1px solid #000;padding:10px;background-color:#FFF;clear:right;float:right;width:300px;">
                <h4>Create a New Key</h4>
                <a href="/createkey">Create Key</a>
            </div>
        </div>
    </div>
</body>
</html>
