<?php
 error_reporting(1);
  

  
  
 
file_put_contents("/home/bricflrb/public_html/SiT_3/connections.txt",$_SERVER['REMOTE_ADDR']."\n", FILE_APPEND | LOCK_EX);
  

  $conn = mysqli_connect( "host" , "dbusername", "dbpassword" , "dbname");
  
  if(!$conn) {
    //include("site/maint.php");
    die("Database Error");
  }
  
  
  


  if(session_status() == PHP_SESSION_NONE) {
    session_name("BRICK-SESSION");
    session_start();
  }
?>
<?php

  