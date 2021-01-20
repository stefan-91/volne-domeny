
<?php
error_reporting(E_ALL);
ini_set("display_errors","On");

// Create a blank image and add some text
$im = imagecreatetruecolor(120, 20);
$text_color = imagecolorallocate($im, 233, 14, 91);
imagestring($im, 1, 5, 5,  'A Simple Text String', $text_color);

// Save the image as 'simpletext.jpg'
imagejpeg($im, 'simpletext.jpg');

// Free up memory
imagedestroy($im);
?> 