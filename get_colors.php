<?php
$image = imagecreatefromjpeg('c:\wamp64\www\Kawanua_Admin_Laravel\Youtube pribadi.jpg');
$width = imagesx($image);
$height = imagesy($image);
$colors = [];
for($x = 0; $x < $width; $x += 5) {
    for($y = 0; $y < $height; $y += 5) {
        $rgb = imagecolorat($image, $x, $y);
        $r = ($rgb >> 16) & 0xFF;
        $g = ($rgb >> 8) & 0xFF;
        $b = $rgb & 0xFF;
        $hex = sprintf("#%02x%02x%02x", $r, $g, $b);
        if (!isset($colors[$hex])) $colors[$hex] = 0;
        $colors[$hex]++;
    }
}
arsort($colors);
$top_colors = array_slice($colors, 0, 20, true);
print_r($top_colors);
