<?php
require ("/storage/ssd3/157/16817157/public_html/core/header.php");
require ("/storage/ssd3/157/16817157/public_html/core/nav.php");
if (!$isloggedin) {
    header('Location: /Login/');
}
header("Location: /users?q=$namefixed");

$namefixed = mysqli_real_escape_string($connect,$_POST['name']);