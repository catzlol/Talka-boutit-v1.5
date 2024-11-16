


<?php 
include('SiT_3/config.php');
include('SiT_3/header.php');

if ($loggedIn) {
  $currentUserID = $_SESSION['id'];
  $findUserSQL = "SELECT * FROM `beta_users` WHERE `id` = '$currentUserID'";
  $findUser = $conn->query($findUserSQL);
  
  if ($findUser->num_rows > 0) {
    $userRow = (object) $findUser->fetch_assoc();
  } else {
    unset($_SESSION['id']);
    header('Location: /login/');
    die();
  }
  
} else {
  header('Location: /login/');
  die();
}

//update desc
if (isset($_POST['desc'])) {
  $newDesc = mysqli_real_escape_string($conn,$_POST['desc']);
  //$newDesc = strip_tags($_POST['desc']);
  $userID = $userRow->{'id'};
  $updateDescSQL = "UPDATE `beta_users` SET `description` = '$newDesc' WHERE `id` = '$userID'";
  $updateDesc = $conn->query($updateDescSQL);
  header("Location: index");
  }
  //update status
if (isset($_POST['status'])) {
  //make something where you can't spam statuses
  $newStatus = mysqli_real_escape_string($conn,$_POST['status']);
  mysqli_query($conn,"INSERT INTO `statuses` VALUES (NULL,'$currentUserID','$newStatus','$curDate')");
  header("Location: index");
  }
  
//$statusReq = mysqli_query($conn,"SELECT * FROM `statuses` WHERE `owner_id`='$currentUserID' ORDER BY `id` DESC");
//$statusReqData = mysqli_fetch_assoc($statusReq);
//$currStatus = $statusReqData['body'];

$statusSQL = "SELECT * FROM `statuses` WHERE `owner_id`='$currentUserID' ORDER BY `id` DESC";
$findStatus = $conn->query($statusSQL);

if ($findStatus->num_rows > 0) {
  $statsRow = (object) $findStatus->fetch_assoc();
}
//games
$findGamesSQL = "SELECT * FROM `games`";
$findGames = $conn->query($findGamesSQL);
$gameRow = $findGames->fetch_assoc()

//featured games
//$featuredGameSQL = "SELECT * FROM `misc` WHERE `featured_game_id`";
//$findFeaturedGame = $conn->query($featuredGameSQL);
//$featuredGameRow = (object) $findFeaturedGame->fetch_assoc();
//$featuredGameUsername = $featuredGameRow->{'featured_game_id'} == $gameRow->{'id'};
  
?>

<!DOCTYPE html>
<head>
    <title>Dashboard - Talka-boutit</title>
</head>
<body>
    <div id="body">
        <div id="column" style="float:left; width:580px;">
            <div id="box" style="width:100%;">
                <div style="margin:20px;float:left;position:relative;width: 165px;">
                    <img style="width:180px" src="/images/avatars/<?php echo $userRow->{'id'}; ?>.png?c=<?php echo $userRow->{'avatar_id'}; ?>">
                </div>
                <form style="margin: 20px 20px 10px 20px; float:left; text-align:left;" action="" method="POST">
                    <p class="title" style="font-weight:bold;">Status:</p>
                    <input name="status" style="width:320px;" type="text"<?php if (empty($currStatus)) {echo 'placeholder="Right now I&#39;m..."></input>';}else {echo 'value="' . $currStatus . '"</input>';} ?><br>
                    <input type="submit" style="margin-top:4px;">
                </form>
                <form style="margin: 10px 20px 20px 20px; float:left; text-align:left;" action="" method="POST">
                    <p class="title" style="font-weight:bold;">Blurb:</p>
                    <textarea name="desc" style="width:320px; height:120px;"<?php if (empty($userRow->{'description'})) {echo 'placeholder="Hi, my name is "' . $userRow->{'username'} . '"></textarea>';} else {echo '>' . $userRow->{'description'} . '</textarea>';} ?><br>
                    <input type="submit">
                </form>
                
            
                       
                   
            </div>
        </div>
    </div>
</body>
</html>

            <?php
$id = $userRow->{'id'};
$friendsQuery = "SELECT `from_id`,`to_id` FROM `friends` WHERE (`from_id`='$id' OR `to_id`='$id') AND `status`='accepted'";
$friends = $conn->query($friendsQuery);

$friendsArray = array();
while($friendRow = $friends->fetch_assoc()) {
  $friendsArray[] = $friendRow['from_id'];
  $friendsArray[] = $friendRow['to_id'];
}

$friendList = join("','",$friendsArray);

$statusQuery = "SELECT * FROM `statuses` WHERE `owner_id` IN('$friendList') AND `owner_id`!='$id' ORDER BY `id` DESC";
$status = $conn->query($statusQuery);

for($c = 0; $c < 10; $c++) {
  $statusRow = $status->fetch_assoc();
  
  if(empty($statusRow)) {break;}
  
  $findPosterRowSQL = "SELECT * FROM `beta_users` WHERE `id` = '" . $statusRow['owner_id'] . "'";
  $findPosterRow = $conn->query($findPosterRowSQL);
  $posterRow = $findPosterRow->fetch_assoc();
  echo '<tr style="border-bottom: 1px solid #aaaaaa;">
    <td style="padding: 8px 0px 0px 8px;background: #f8f9ff;" valign="top">'.str_replace(">","&gt;",str_replace("<","<",$statusRow['body'])).'</td><td style="background: #f8f9ff;font-size: 12.5px;font-style: italic;text-align: right;padding-top: 46px;float: right;padding-right: 5px;">'.$statusRow['date'].'</div>
    <td style="width: 20%; text-align: center;">
    <div style="border-left: 1px solid #b7b7b7;padding: 3px;">
    <a href="/user?id='.$statusRow['owner_id'].'" style="color:#000;text-decoration:none;">
      <img style="width:40px;" src="/images/avatars/'.$statusRow['owner_id'].'.png?c='.$posterRow['avatar_id'].'"><br>' .$posterRow['username']. '</a>
    </div></td>
    </tr>';
}

/*while($statusRow = $status->fetch_assoc()) {
  $findPosterRowSQL = "SELECT * FROM `beta_users` WHERE `id` = '" . $statusRow['owner_id'] . "'";
  $findPosterRow = $conn->query($findPosterRowSQL);
  $posterRow = $findPosterRow->fetch_assoc();
  echo '<div id="subsect" style="overflow: auto;">
    <div style="display:inline-block;float:left;">
    <a href="http://www.brick-hill.com/user.php?id='.$statusRow['owner_id'].'" style="color:#000;text-decoration:none;">
      <img style="width:40px;" src="http://storage.brick-hill.com/images/avatars/'.$statusRow['owner_id'].'.png?c='.$posterRow['avatar_id'].'"><br>' .$posterRow['username']. '</a>
    </div>
    <div>'.str_replace(">","&gt;",str_replace("<","<",$statusRow['body'])).'</div>
    </div>';
  }*/
            
            
            
            ?>
            </tbody>
            </table>
          </div>
    
        </div>
      </div>
    </div>
    <?php include('SiT_3/footer.php'); ?>
  </body>
</html>