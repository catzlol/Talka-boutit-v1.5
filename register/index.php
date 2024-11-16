<?php
include('../SiT_3/config.php');
include('../SiT_3/header.php');

if (isset($_SESSION['id'])) {
    header("Location: /index");
    exit();
}

$error = [];

// Check if a registration key is required
$keyRequired = false;
$keyCheckSQL = "SELECT `required` FROM `regkeyreq` LIMIT 1";
$keyCheckResult = $conn->query($keyCheckSQL);
if ($keyCheckResult && $keyCheckResult->num_rows > 0) {
    $keyRequired = (int)$keyCheckResult->fetch_assoc()['required'] === 1;
}

function validateUsername($username) {
    if (strlen($username) < 3 || strlen($username) > 26 || !ctype_alnum(str_replace(['-', '_', '.', ' '], '', $username))) {
        return 'Username must be 3-26 alphanumeric characters (including [ , ., -, _]).';
    }
    if (preg_match('/(\s{2,}|\.\.|--|__)/', $username)) {
        return 'Spaces, periods, hyphens, and underscores must be separated.';
    }
    return '';
}

function validateRegistrationKey($conn, $key) {
    $key = mysqli_real_escape_string($conn, $key);
    $keySQL = "SELECT * FROM `reg_keys` WHERE `key_content` = '$key' LIMIT 1";
    $keyResult = $conn->query($keySQL);
    if ($keyResult && $keyResult->num_rows > 0) {
        return true;
    }
    return false;
}

function markKeyAsUsed($conn, $key, $userID) {
    $key = mysqli_real_escape_string($conn, $key);
    // Insert a new key entry
    $insertKeySQL = "INSERT INTO `reg_keys` (`key_content`, `used_by`) VALUES ('$key', '$userID')";
    $conn->query($insertKeySQL);
}

function registerUser($conn, $username, $password, $email, $birth_date, $usedkey) {
    $usernameL = strtolower(mysqli_real_escape_string($conn, $username));
    $passwordHash = password_hash($password, PASSWORD_BCRYPT);

    // Check for existing username
    $checkUsernameSQL = "SELECT * FROM `beta_users` WHERE `usernameL` = '$usernameL'";
    $checkUsername = $conn->query($checkUsernameSQL);
    if ($checkUsername->num_rows > 0) {
        return 'Username taken.';
    }

    // Insert user
    $uid = bin2hex(random_bytes(20));
    $createUserSQL = "INSERT INTO `beta_users` (`username`, `usernameL`, `password`, `IP`, `birth`, `date`, `unique_key`, `usedkey`) VALUES ('$username', '$usernameL', '$passwordHash', '{$_SERVER['REMOTE_ADDR']}', '$birth_date', NOW(), '$uid', '$usedkey')";
    
    if ($conn->query($createUserSQL)) {
        $userID = $conn->insert_id;

        // Insert email
        $emailSQL = "INSERT INTO `emails` (`user_id`, `email`, `verified`, `date`) VALUES ('$userID', '$email', 'no', NOW())";
        $conn->query($emailSQL);

        $_SESSION['id'] = $userID;
        return null; // Success
    } else {
        return 'Database error';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim(mysqli_real_escape_string($conn, $_POST['username']));
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $confirmPassword = mysqli_real_escape_string($conn, $_POST['passwordConfirm']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $birth_year = intval($_POST['year']);
    $birth_month = intval($_POST['month']);
    $regKey = isset($_POST['regKey']) ? trim(mysqli_real_escape_string($conn, $_POST['regKey'])) : '';

    if ($password !== $confirmPassword) {
        $error[] = 'Passwords do not match!';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error[] = 'Please enter a valid email!';
    }

    if (date('Y') - $birth_year < 1 || date('Y') - $birth_year > 124) {
        $error[] = "You must be between 1 and 124 years old.";
    } else {
        $birth_date = "$birth_year-$birth_month-01";
    }

    $usernameError = validateUsername($username);
    if ($usernameError) {
        $error[] = $usernameError;
    }

    if ($keyRequired && (empty($regKey) || !validateRegistrationKey($conn, $regKey))) {
        $error[] = 'Key is either incorrect or has been used already.';
    }

    if (empty($error)) {
        $usedkey = $keyRequired ? 1 : 0; // Set usedkey based on key requirement
        $registrationError = registerUser($conn, $username, $password, $email, $birth_date, $usedkey);
        if ($registrationError) {
            $error[] = $registrationError;
        } else {
            if ($keyRequired && !empty($regKey)) {
                markKeyAsUsed($conn, $regKey, $_SESSION['id']);
            }
            // Redirect after successful registration
            header('Location: /uploadavatars/');
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Talka-boutit Registration</title>
</head>
<body>
    <div id="body">
        <div id="box">
            <div id="subsect">
                <h3>Create an account on Talkaboutit</h3>
            </div>
            <?php if (!empty($error)): ?>
                <div style="background-color:#EE3333;margin:10px;padding:5px;color:white;">
                    <?php foreach ($error as $line): ?>
                        <?php echo $line . '<br>'; ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <form action="" method="POST" style="float:left;margin-left:10px;">
                <h4>Username:</h4>
                <h6>How will people recognize you?</h6>
                <input style="margin-left:5px;" type="text" name="username" required><br>
                <h4 style="margin-top:10px;">Email:</h4>
                <h6>This must be valid so we can contact you!</h6>
                <input style="margin-left:5px;" type="email" name="email" required><br>
                <h4 style="margin-top:10px;">Birthday:</h4>
                <h6>For your safety, please enter your date of birth.</h6>
                <select style="margin-left:5px;" name="year" required>
                    <?php for ($y = 1; $y <= 124; $y++): ?>
                        <option value="<?php echo date('Y') - $y; ?>"><?php echo date('Y') - $y; ?></option>
                    <?php endfor; ?>
                </select>
                <select style="margin-left:5px;" name="month" required>
                    <?php for ($m = 1; $m <= 12; $m++): ?>
                        <option value="<?php echo $m; ?>"><?php echo DateTime::createFromFormat('!m', $m)->format('F'); ?></option>
                    <?php endfor; ?>
                </select><br>
                <h4 style="margin-top:10px;">Password:</h4>
                <h6>Only you will know this!</h6>
                <input style="margin-left:5px;" type="password" name="password" required><br>
                <h6>Please retype your password.</h6>
                <input style="margin-left:5px;" type="password" name="passwordConfirm" required><br>

                <!-- Registration Key Section -->
                <?php if ($keyRequired): ?>
                    <h4 style="margin-top:10px;">Registration Key:</h4>
                    <h6>Enter your registration key to sign up.</h6>
                    <input style="margin-left:5px;" type="text" name="regKey" required><br>
                <?php endif; ?>

                <br><h6>By signing up, you agree to the <a href="/terms/">Terms</a></h6>
                <input style="margin:10px 0px 0px 5px;text-align:center;width:64px;height:24px;" type="submit" value="Register">
            </form>
            <div style="margin:10px;border:1px solid #000;padding:10px;background-color:#FFF;clear:right;float:right;width:300px;">
                <h4>Already have an account?</h4>
                <a href="/login">Login!</a>
            </div>
        </div>
    </div>
</body>
</html>
