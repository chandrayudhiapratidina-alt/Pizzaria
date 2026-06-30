<?php
// Script to convert black backgrounds to transparent alpha channels
$dir = __DIR__ . '/public/assets/images/about/';
$files = ['item_tomato.png', 'item_basil.png', 'item_cheese.png', 'item_mushroom.png', 'hero_pizza.png'];

foreach ($files as $file) {
    $path = $dir . $file;
    if (!file_exists($path)) {
        echo "Missing: $file\n";
        continue;
    }
    
    $img = imagecreatefromstring(file_get_contents($path));
    if (!$img) {
        echo "Failed to load $file\n";
        continue;
    }
    $w = imagesx($img);
    $h = imagesy($img);
    
    $out = imagecreatetruecolor($w, $h);
    imagealphablending($out, false);
    imagesavealpha($out, true);
    
    for ($y = 0; $y < $h; $y++) {
        for ($x = 0; $x < $w; $x++) {
            $rgb = imagecolorat($img, $x, $y);
            $r = ($rgb >> 16) & 0xFF;
            $g = ($rgb >> 8) & 0xFF;
            $b = $rgb & 0xFF;
            
            // Calculate brightness
            $max = max($r, $g, $b);
            
            if ($max < 25) {
                // Pure black or near-black -> Fully transparent
                $alpha = 127; 
                $r = 0; $g = 0; $b = 0;
            } else if ($max < 80) {
                // Smooth transition for edges
                // Map max from 25-80 to alpha 127-0
                $alpha = 127 - (int)((($max - 25) / 55) * 127);
                // Boost color to compensate for transparency
                $factor = 255 / $max;
                $r = min(255, (int)($r * $factor));
                $g = min(255, (int)($g * $factor));
                $b = min(255, (int)($b * $factor));
            } else {
                // Solid pixel
                $alpha = 0;
            }
            
            $color = imagecolorallocatealpha($out, $r, $g, $b, $alpha);
            imagesetpixel($out, $x, $y, $color);
        }
    }
    
    $outPath = $dir . 'clear_' . $file;
    imagepng($out, $outPath);
    imagedestroy($img);
    imagedestroy($out);
    echo "Processed $file to clear_$file\n";
}
echo "Done.";
?>
