<?php
include('SiT_3/config.php');
include('SiT_3/header.php');

// Ensure user is logged in
if (!$loggedIn) {
    header("Location: /");
    exit();
}

$currentUserId = $userRow->id;
$errors = [];
$success = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $groupName = trim($_POST['group_name']);
    $memberUsernames = array_map('trim', explode(',', $_POST['member_usernames']));

    if (empty($groupName)) {
        $errors[] = "Group name is required.";
    }

    if (empty($memberUsernames)) {
        $errors[] = "At least one member username is required.";
    }

    if (empty($errors)) {
        // Insert new group chat
        $stmt = $conn->prepare("INSERT INTO gc_chats (name) VALUES (?)");
        $stmt->bind_param('s', $groupName);
        if ($stmt->execute()) {
            $chatId = $stmt->insert_id;

            // Add the current user as the first member
            $stmt = $conn->prepare("INSERT INTO gc_members (chat_id, user_id) VALUES (?, ?)");
            $stmt->bind_param('ii', $chatId, $currentUserId);
            $stmt->execute();

            // Add other members
            $stmt = $conn->prepare("INSERT INTO gc_members (chat_id, user_id) VALUES (?, ?)");
            foreach ($memberUsernames as $username) {
                $stmt_user = $conn->prepare("SELECT id FROM beta_users WHERE username = ?");
                $stmt_user->bind_param('s', $username);
                $stmt_user->execute();
                $result_user = $stmt_user->get_result();

                if ($result_user->num_rows > 0) {
                    $user = $result_user->fetch_assoc();
                    $userId = $user['id'];
                    $stmt->bind_param('ii', $chatId, $userId);
                    $stmt->execute();
                } else {
                    $errors[] = "User '$username' not found.";
                }
            }

            if (empty($errors)) {
                $success = "Group chat '$groupName' created successfully.";
            } else {
                // Rollback chat creation if there were errors adding members
                $conn->query("DELETE FROM gc_members WHERE chat_id = $chatId");
                $conn->query("DELETE FROM gc_chats WHERE id = $chatId");
            }
        } else {
            $errors[] = "Failed to create group chat.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Group Chat</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
        }
        #body {
            padding: 10px;
        }
        .form-group {
            margin-bottom: 10px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
        }
        .form-group input[type="text"] {
            padding: 8px;
            width: 100%;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .form-group button {
            padding: 10px 15px;
            background-color: #4CAF50;
            color: #fff;
            border: none;
            cursor: pointer;
        }
        .error {
            color: red;
        }
        .success {
            color: green;
        }
    </style>
</head>
<body>
    <div id="body">
        <h1>Create Group Chat</h1>
        <?php if (!empty($errors)): ?>
            <div class="error">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="success">
                <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>
        <form action="gccreate" method="POST">
            <div class="form-group">
                <label for="group_name">Group Name:</label>
                <input type="text" id="group_name" name="group_name" required>
            </div>
            <div class="form-group">
                <label for="member_usernames">Member Usernames (comma-separated):</label>
                <input type="text" id="member_usernames" name="member_usernames" required>
            </div>
            <div class="form-group">
                <button type="submit">Create Group Chat</button>
            </div>
        </form>
    </div>
</body>
</html>
