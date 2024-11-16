


<?php


include('config.php');



$membershipPower = -1;
if (isset($_SESSION['id'])) {
  
  $currentUserID = $_SESSION['id'];
  $findUserSQL = "SELECT * FROM `beta_users` WHERE `id` = '$currentUserID'";
  $findUser = $conn->query($findUserSQL);
  
  if ($findUser->num_rows > 0) {
    $userRow = (object) $findUser->fetch_assoc();
  } else {
    unset($_SESSION['id']);
    //header('Location: /login/');
  }
  
  $power = $userRow->{'power'};
  $UID = $userRow->{'id'};
  $currentID = $userRow->{'id'};
  $curDate = date('Y-m-d H:i:s');
  $sqlRead = "UPDATE `beta_users` SET `last_online` = '$curDate' WHERE `id` = '$currentUserID'";
  $result = $conn->query($sqlRead);
  
  $membershipSQL = "SELECT * FROM `membership` WHERE `active`='yes' AND `user_id`='$currentUserID'";
  $membership = $conn->query($membershipSQL);
  while($membershipRow = $membership->fetch_assoc()) {
    $membershipID = $membershipRow['id'];
  $membershipValue = $membershipRow['membership'];
  $memSQL = "SELECT * FROM `membership_values` WHERE `value`='$membershipValue'";
  $mem = $conn->query($memSQL);
  $memRow = $mem->fetch_assoc();
  
  $membershipPower = max($membershipPower,$membershipValue);
  
  $currentDate = $curDate;
  $membershipEnd = date('Y-m-d H:i:s',strtotime($membershipRow['date'].' +'.$membershipRow['length'].' minutes'));
  if($currentDate >= $membershipEnd) {
    $stopSQL = "UPDATE `membership` SET `active`='no' WHERE `id`='$membershipID';";
    $stop = $conn->query($stopSQL);
  }
  }
  
  $membershipSQL = "SELECT * FROM `membership_values` WHERE `value`='$membershipPower'";
  $membership = $conn->query($membershipSQL);
  $membershipRow = $membership->fetch_assoc();
  
  

  $lastonline = strtotime($curDate)-strtotime($userRow->{'daily_bits'});
  if ($lastonline >= 86400) {
    $bits = ($userRow->{'bits'}+10);
    $sqlBits = "UPDATE `beta_users` SET `bits` = '$bits' WHERE `id` = '$currentUserID'";
    $result = $conn->query($sqlBits);

    $daily = $curDate;
    $sqlDaily = "UPDATE `beta_users` SET `daily_bits` = '$daily' WHERE `id` = '$currentUserID'";
    $result = $conn->query($sqlDaily);
    
    
    ////MEMBERSHIP CASH
  $membershipSQL = "SELECT * FROM `membership` WHERE `active`='yes' AND `user_id`='$currentUserID'";
  $membership = $conn->query($membershipSQL);
  while($membershipRow = $membership->fetch_assoc()) {
    $membershipValue = $membershipRow['membership'];
    $memSQL = "SELECT * FROM `membership_values` WHERE `value`='$membershipValue'";
    $mem = $conn->query($memSQL);
    $memRow = $mem->fetch_assoc();
    
    $userMemSQL = "SELECT * FROM `beta_users` WHERE `id`='$currentUserID'";
    $userMem = $conn->query($userMemSQL);
    $userMemRow = $userMem->fetch_assoc();
    $bucks = ($userMemRow['bucks']+$memRow['daily_bucks']);
    $bucksSQL = "UPDATE `beta_users` SET `bucks` = '$bucks' WHERE `id` = '$currentUserID'";
    $result = $conn->query($bucksSQL);
  }
  ////
  }
  $loggedIn = true;
} else {
  $loggedIn = false;
  
  $URI = $_SERVER['REQUEST_URI'];
  if ($URI != '/login/' && $URI != '/register/') {
    //header('Location: /login/');
  }
}



$shopUnapprovedAssetsSQL = "SELECT * FROM `shop_items` WHERE `approved`='no' ORDER BY `date` DESC LIMIT 0,10";
$shopUnapprovedAssets = $conn->query($shopUnapprovedAssetsSQL);
$unapprovedNum = $shopUnapprovedAssets->num_rows;



// Fetch unread messages count
if ($loggedIn) {
  $mID = $userRow->{'id'};
  $sqlUnread = "SELECT COUNT(*) AS unread_count FROM `userdms` WHERE `touser` = '$mID' AND `unread` = 1";
  $resultUnread = $conn->query($sqlUnread);
  $unreadRow = $resultUnread->fetch_assoc();
  $unreadCount = $unreadRow['unread_count'];
}
?>



<!DOCTYPE html>
  <head>
  <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
  <script>
    (adsbygoogle = window.adsbygoogle || []).push({
      google_ad_client: "ca-pub-8506355182613043",
      enable_page_level_ads: true
    });
  </script>
  <script
  src="https://code.jquery.com/jquery-3.1.1.min.js"
  integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8="
  crossorigin="anonymous"></script>
  <script src="/javascript/security.js?r=<?php echo rand(10000,1000000) ?>"></script>
  <?php
  if($loggedIn) {
    $theme = $userRow->{'theme'};
  } else {
    $theme = 0;
  }
  $themeArray = array(
  "0" => "",
  "1" => "",
  );
  //$themeName = strtr();
  
  if ($theme != 3) {
    ?>
    <script src="/javascript/css/night.js?r=<?php echo rand(10000,1000000) ?>"></script>
    <?php
  }
  ?>
    <link rel="icon" href="/assets/BH_favicon.png">
    <link rel="stylesheet" href="/style.css?r=<?php echo rand(10000,1000000) ?>" type="text/css">
  <?php
  if ($theme == 1) {
    ?>
  <link rel="stylesheet" href="/winter-theme/winter-style.css?r=<?php echo rand(10000,1000000) ?>" type="text/css">
    <?php
  } elseif ($theme == 2) {
    ?>
  <link rel="stylesheet" href="/assets/night.css?r=<?php echo rand(10000,1000000) ?>" type="text/css">
    <?php
  } elseif ($theme == 3) {
    
  } elseif ($theme == 4) {
    ?>
    <link rel="stylesheet" href="/assets/SummerTheme.css?r=<?php echo rand(10000,1000000) ?>" type="text/css">
    <?php
  } elseif ($theme == 5) {
    ?>
    <link rel="stylesheet" href="/assets/FallTheme.css?r=<?php echo rand(10000,1000000) ?>" type="text/css">
    <?php
  }
  ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script>
      //checks if youre banned. if youre banned, it will bring you to the page
function checkBannedStatus() {
    const excludedPaths = ['/banned/', '/login/']; //wont constantly refresh if youre already on the page.

    const currentPath = window.location.pathname;
    if (excludedPaths.includes(currentPath)) {
        return; 
    }

    fetch('/check_banned_status')
        .then(response => response.json())
        .then(data => {
            if (data.banned) {
                window.location.href = '/banned/';
            }
        })
        .catch(error => console.error('Error checking banned status:', error));
}

setInterval(checkBannedStatus, 1000);

    //checks if the users status is still valid. If its not, it will send you to the login page  
      let previousSessionValid = true; 

function checkSession() {
    const excludedPaths = ['/login/', '/register/', '/terms/', '/privacy/', '/about/', '/contact/']; //stops from automatically refreshing on these pages

    const currentPath = window.location.pathname;
    if (excludedPaths.includes(currentPath)) {
        return; // 
    }

    fetch('/checkSession')
        .then(response => response.json())
        .then(data => {
            if (!data.sessionValid) {
                window.location.href = '/login/';
            }
        })
        .catch(error => console.error('Error checking session:', error));
}

// Start checking every second
setInterval(checkSession, 1000);

      
    </script>
  </head>
  <body>
    <div id="header">
      <div id="banner">
  <?php if($loggedIn) {echo '<div id="welcome"><a class="nav" href="/">Welcome, '. $userRow->{'username'}.'</a></div>';} ?>
        <div id="info" <?php if(!$loggedIn) {echo 'style="visibility:hidden;"';} ?> >
          <span style="display:inline-block;float: left;margin-left: -5px;">
            <ul>

            </ul>
          </span>
         
            <ul>
              <li><a class="nav" href="/messages/"><i class="fa fa-envelope"></i>
                <?php
                if($loggedIn) {
                  $mID = $userRow->{'id'};
                  $sqlSearch = "SELECT * FROM `messages` WHERE  `recipient_id` = '$mID' AND `read` = 0";
                  $result = $conn->query($sqlSearch);
                  
                  $messages = 0;
                  while($searchRow=$result->fetch_assoc()) {$messages++;}
                  echo number_format($messages);
                }
                ?>
              </a></li>
              <li><a class="nav" href="/friends/"><i class="fa fa-users"></i>
              <?php
              if($loggedIn) {
                $requestsQuery = mysqli_query($conn,"SELECT * FROM `friends` WHERE `to_id`='$mID' AND `status`='pending'");
                $requests = mysqli_num_rows($requestsQuery);
                echo number_format($requests);
              }
              ?>
              </a></li>
            </ul>
          </span>
        </div>
      </div>
      <div id="navbar">
        <span>
          <span>
          <?php
          if($loggedIn) {
            echo '<a href="/user?id='.$userRow->{'id'}.'">You</a>';
          } else {
            echo '<a href="/login/">You</a>';
          }
          ?>
          </span>
          <span> | </span>
          <span>
          <?php
          if($loggedIn) {
            echo '<a href="/uploadavatars/">Avatar</a>';
          } else {
            echo '<a href="/login/">Avatar</a>';
          }
          ?>
          </span>
          <span> | </span>
          <span>
           <a class="nav" href="/forum/">Subtalks</a>
          </span>
          <span> | </span>
          <span>
            <a class="nav" href="/clans/">Clans</a>
          </span>
          <span> | </span>
          <span>
            <a class="nav" href="/search/">Users</a>
          </span>
          <span> | </span>
          <span>
             <a class="nav" href="/createboard/">Create Subtalk</a>
          </span>
        
     <?php if ($loggedIn): ?>
        <span> | </span>
        <span>
          <a class="nav" href="/mydms/">
            <?php echo ($unreadCount > 0) ? 'DMS Unread Message(s)!' : 'DMS'; ?>
          </a>
        </span>
        <?php endif; ?>
        
          <?php
          if($loggedIn) {
            if($power >= 1) {echo '<span> | </span><span><a class="nav" href="/admin/">Admin'; if ($unapprovedNum > 0) { echo " ($unapprovedNum) ";} echo '</a></span>';}
          }
          ?>
        
           <?php
          if($loggedIn) {
            if($power >= 1) {echo '<span> | </span><span><a class="nav" href="/editboard/">Delete Subtalk'; if ($unapprovedNum > 0) { echo " ($unapprovedNum) ";} echo '</a></span>';}
          }
          ?>
          
          
          <span style="float:right; margin-top: -3px;">
          <?php
          if($loggedIn) {
            echo '<a class="nav" href="/logout/">Logout</a>';
          } else {
            echo '<a class="nav" href="/login/">Login</a>';
          }
          ?>
          </span>
        </span>
      </div>
      
      
<?php
/*
$sql = "SELECT * FROM site_announcements ORDER BY timestamp DESC LIMIT 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $announcementText = $row["announcement_text"];
    // Display the announcement as an alert
    echo '<div class="announcement">
            <span class="announcement-text">' . $announcementText . '</span>
          </div>';
}
*/
?>


    </div>
<?php

  
  if($_SERVER['REMOTE_ADDR'] != '82.21.246.202') { //for working on the site
    //exit;
  }
  ////
  if($loggedIn) {
    $bannedSQL = "SELECT * FROM `moderation` WHERE `active`='yes' AND `user_id`='$currentID'";
    $banned = $conn->query($bannedSQL);
    if($banned->num_rows != 0) {//they are banned
      $URI = $_SERVER['REQUEST_URI'];
      if ($URI != '/banned/') {
      header('Location: /banned/');
    
        
   
    }
  }
  
 
  }
?>