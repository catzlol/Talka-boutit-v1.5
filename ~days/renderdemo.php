<?php
header("Content-Type: image/png");

// Retrieve avatar data
include '../SiT_3/config.php';
$avatar = $conn->query("SELECT * FROM avatar WHERE user_id = 2 LIMIT 1")->fetch_assoc();

// Create an image
$im = imagecreatetruecolor(512, 512);

list($r, $g, $b) = sscanf($avatar["head_color"], "%02x%02x%02x");
$color = imagecolorallocate($im, $r, $g, $b);

// Head.obj, LeftArm.obj, LeftLeg.obj, RightArm.obj, RightLeg.obj, TShirt.obj, Torso.obj
imagesetpixel($im, 256, 256, $color);

// Send off the image
imagepng($im);
imagedestroy($im);

?>