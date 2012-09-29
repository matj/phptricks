<?php
/**
 * Funkcia na vlozenie a vycentrovanie textu do bloku
 * 
 * 
 * @param resource $image Resourse z obrazku
 * @param int $size Velkost pisma
 * @param int $angle
 * @param int $left
 * @param int $top
 * @param int $color Farba ziskana z imagecolorallocate
 * @param string $font cesta k fontu ttf
 * @param string $text
 * @param int $max_width maximalna sirka bloku
 * @param string $align left|justify zarovnanie textu
 * @param int $minspacing
 * @param int $linespacing
 * @return boolean
 */
function imageTextToBlock(&$image, $size, $angle, $left, $top, $color, $font, $text, $max_width, $align = "left", $minspacing = 3, $linespacing = 1) {
    $wordwidth = array();
    $linewidth = array();
    $linewordcount = array();
    $largest_line_height = 0;
    $lineno = 0;
    $words = explode(" ", $text);
    $wln = 0;
    $linewidth[$lineno] = 0;
    $linewordcount[$lineno] = 0;
    foreach ($words as $word) {
        $dimensions = imagettfbbox($size, $angle, $font, $word);
        $line_width = $dimensions[2] - $dimensions[0];
        $line_height = $dimensions[1] - $dimensions[7];
        if ($line_height > $largest_line_height)
            $largest_line_height = $line_height;
        if (($linewidth[$lineno] + $line_width + $minspacing) > $max_width) {
            $lineno++;
            $linewidth[$lineno] = 0;
            $linewordcount[$lineno] = 0;
            $wln = 0;
        }
        $linewidth[$lineno]+=$line_width + $minspacing;
        $wordwidth[$lineno][$wln] = $line_width;
        $wordtext[$lineno][$wln] = $word;
        $linewordcount[$lineno]++;
        $wln++;
    }
    for ($ln = 0; $ln <= $lineno; $ln++) {
        $slack = $max_width - $linewidth[$ln];
        if (isset($_GET["justify"])==true && ($linewordcount[$ln] > 1) && ($ln != $lineno))
            $spacing = ($slack / ($linewordcount[$ln] - 1));
        else
            $spacing = $minspacing;
        
        $x = 0;
        for ($w = 0; $w < $linewordcount[$ln]; $w++) {
            imagettftext($image, $size, $angle, $left + intval($x), $top + $largest_line_height + ($largest_line_height * $ln * $linespacing), $color, $font, $wordtext[$ln][$w]);
            $x+=$wordwidth[$ln][$w] + $spacing + $minspacing;
        }
    }
    return true;
}




/***
 * skuska
 */
$image = imagecreatetruecolor(400, 400);
$black = imagecolorallocate($image, 0, 0, 0);
$white = imagecolorallocate($image, 255, 255, 255);

// Set the background to be white
imagefilledrectangle($image, 0, 0, 400, 400, $white);

// Path to our font file
$font = '../'.(isset($_GET["font"]) ? $_GET["font"] : 'arial.ttf');

$text= "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent varius vestibulum magna. Sed ac velit metus. Nullam sodales fermentum urna id feugiat. Aenean dignissim egestas eros non tristique. Duis lectus lacus, iaculis quis ultrices vel, feugiat a arcu. Nunc risus est, blandit rutrum imperdiet sed, congue non nisi. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Vestibulum diam massa, suscipit in gravida sed, fringilla a mi. Mauris augue purus, consectetur eu lacinia mattis, varius a nisi. Curabitur mattis dui quis est fermentum aliquam. Suspendisse lobortis molestie elit, tempor volutpat nisi lobortis sit amet. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Nam aliquam feugiat odio in gravida. Mauris sed gravida ante. Fusce a turpis odio, a scelerisque tortor. Curabitur in enim urna.";

if(isset($_GET["text"]))$text=$_GET["text"];
$size = isset($_GET["size"]) ? (int)$_GET["size"] : 10;


imageTextToBlock($image, $size, 0, 5, 5, $black, $font, $text, 380,"left",4);


header("Content-type:image/jpeg");
imagepng($image);
