<?php
  require("adminOnly.php");
  include("../../SiT_3/config.php");

  if ($power < 1) {
    header("Location: ../");
    die();
  }

?>

<div id="box" style="margin-bottom:10px">
  <div id="subsect">
    <h3>Admin Panel</h3>
  </div>
  <i>Abuse of this feature will result in indefinite suspension of your administrative privileges.<br>Logs are kept.</i>

  <h4>Membership</h4>
  <form action="" method="POST" style="margin:10px;">
    User ID: <input type="text" name="user"><br>
    Membership: <select name="value">
      <option value="1">Ace</option>
      <option value="2">Mint</option>
      <option value="3">Royal</option>
    </select><br>
    Length (Minutes): <input type="number" name="length"><br>
    <input type="submit" name="membership" value="Set Membership">
  </form><br>

  <h4>Password</h4>
  <form action="" method="POST" style="margin:10px;">
    User ID: <input type="text" name="user"><br>
    <input type="submit" name="password" value="Reset Password">
  </form><br>

  <h4>Manage Messages</h4>
  <p>Click the button below to manage messages in the system:</p>
  <a href="/managemessages" class="blue-button" style="display: inline-block; margin: 10px 0;">Go to Message Management</a>
</div>
