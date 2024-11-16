<?php 
  include('../SiT_3/config.php');
  include('../SiT_3/header.php');
  
  if($loggedIn) {header("Location: /index"); die();}
  
  $error = array();
  if (isset($_POST['ln'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    
    $usernameL = strtolower(mysqli_real_escape_string($conn, $username));
    
    $checkUsernameSQL = "SELECT * FROM `beta_users` WHERE `beta_users`.`usernameL` = '$usernameL'";
    $checkUsername = $conn->query($checkUsernameSQL);
    
    if ($checkUsername->num_rows > 0) {
      
      $username = mysqli_real_escape_string($conn, $username);
      
      $userReqRow = (object) $checkUsername->fetch_assoc();
      
      $userPass = $userReqRow->{'password'};
      
      if (password_verify($password, $userPass)) { //logged in
        $_SESSION['id'] = $userReqRow->{'id'};
        $userID = $_SESSION['id'];
        $userIP = $_SERVER['REMOTE_ADDR'];
        $logSQL = "INSERT INTO `log` (`id`,`action`,`date`) VALUES (NULL,'User $userID logged in from $userIP',CURRENT_TIMESTAMP)";
        $log = $conn->query($logSQL);
        header('Location: ../ ');
        die();
      } else {
        $error[] = "Incorrect username or password.";
      }
      
    } else {
      $error[] = "Username is incorrect, or does not exist.";
      
    }
    
  }
?>
<!DOCTYPE html>
  <head>
    <title>Talka-boutit</title>
  </head>
  <body>
    <div id="body">
      <div style="overflow:auto;">
        <div id="column" style="width:197px;float:left;">
          <div id="box" style="padding:10px;">
            <?php
            if(!empty($error)) {
              echo '<div style="color:#EE3333;">';
              foreach($error as $line) {
                echo $line.'<br>';
              }
              echo '</div>';
            }
            ?>
            <form action="" method="POST">
              <strong>Username:</strong><br>
              <input style="margin-top:4px;margin-bottom:4px;" type="text" name="username"><br>
              <strong>Password:</strong><br>
              <input style="margin-top:4px;" type="password" name="password"><br>
              <center>
                <input style="text-align:center;margin-top:8px;width:64px;height:24px;" type="submit" name="ln" value="Login">
                <h5 onclick="alert('That\'s a shame');">Forgot password?</h5>
                <a href="/register/"><input style="text-align:center;width:64px;height:24px;" type="button" value="Register"></a>
              </center>
            </form>
          </div>
        </div>
        <div id="column" style="width:77%;float:right;">
          <div id="box">
            <h3>Talka-boutit, the free forum service!</h3>
            
            <h4>Please login to your account. If you don't have an account, create one!</h4>
            <h5></h5>
            <h4></h4>
            <h5></h5>
            <h4></h4>
            <h5></h5>
          </div>
        </div>
      </div>
      
      </div>
    </div>
    <?php
      include('../SiT_3/footer.php');
    ?>
  </body>
</html>