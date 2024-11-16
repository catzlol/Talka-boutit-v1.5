<?php

// Requires the GD Library
header("Content-type: image/png");
$im = imagecreatetruecolor(1024, 1024)
    or die("Cannot Initialize new GD image stream");

for ($y = 0; $y < 1024; $y++) {
    for ($x = 0; $x < 1024; $x++) {
        imagesetpixel($im, $x, $y, imagecolorallocate($im, 0, 0, 0));
    }
}		
imagepng($im);
imagedestroy($im);