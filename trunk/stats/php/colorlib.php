<?php

function getMidColors($rgb1,$rgb2,$nb){
$rgb1 = explode(",",$rgb1);
$rgb2 = explode(",",$rgb2);
for($i=0;$i<3;$i++){
for($j=1;$j<=$nb;$j++){
if($rgb1[$i]<$rgb2[$i]){
$rgb_mid[$j].= round(((max($rgb1[$i],$rgb2[$i])-min($rgb1[$i],$rgb2[$i]))/($nb+1))*$j  + min($rgb1[$i],$rgb2[$i]));
}
else{
$rgb_mid[$j].= round(max($rgb1[$i],$rgb2[$i])-((max($rgb1[$i],$rgb2[$i])-min($rgb1[$i],$rgb2[$i]))/($nb+1))*$j);
}
if($i!=2){$rgb_mid[$j].=",";}
}
}
return $rgb_mid;
}

function adMidColors($src_colors,$nb_inter){
for($i=0;$i<count($src_colors);$i++){
$dest_colors[]=$src_colors[$i];
if($i<count($src_colors)-1){
if(is_array($nb_inter)){
$nb = $nb_inter[$i];
}
else{$nb = $nb_inter;}
$new_colors = getMidColors($src_colors[$i],$src_colors[$i+1],$nb);
foreach($new_colors as $new_color)
$dest_colors[]= $new_color;
}
}
return $dest_colors;
}

function getColor($var,$src_colors){

$colors_keys = array_keys($src_colors);
$colors_RGB = array_values($src_colors);

for($i=0;$i<sizeof($src_colors);$i++){

if($var>=$colors_keys[$i]&&$var<$colors_keys[$i+1]){

$rgb1 = explode(",",$colors_RGB[$i]);
$rgb2 = explode(",",$colors_RGB[$i+1]);

for($j=0;$j<3;$j++){

$c = (max($rgb1[$j],$rgb2[$j]) - min($rgb1[$j],$rgb2[$j])) / (max($colors_keys[$i],$colors_keys[$i+1]) - min($colors_keys[$i],$colors_keys[$i+1]));
if($rgb1[$j]<$rgb2[$j]){
$dest_color .= round(max($rgb1[$j],$rgb2[$j]) - ((max($colors_keys[$i],$colors_keys[$i+1]) - $var)*$c));
}
else{
$dest_color .= round(min($rgb1[$j],$rgb2[$j]) + ((max($colors_keys[$i],$colors_keys[$i+1]) - $var)*$c));
}
if($j!=2){$dest_color.=",";}
}

}

}
if($var<=$colors_keys[0]){$dest_color = $colors_RGB[0];}
if($var>=$colors_keys[sizeof($src_colors)-1]){$dest_color = $colors_RGB[sizeof($src_colors)-1];}

return $dest_color;
}

function convertColor($color){
#convert hexadecimal to RGB
if(!is_array($color) && preg_match("/^[#]([0-9a-fA-F]{6})$/",$color)){

$hex_R = substr($color,1,2);
$hex_G = substr($color,3,2);
$hex_B = substr($color,5,2);
$RGB = hexdec($hex_R).",".hexdec($hex_G).",".hexdec($hex_B);

return $RGB;
}

#convert RGB to hexadecimal
else{
if(!is_array($color)){$color = explode(",",$color);}

foreach($color as $value){
$hex_value = dechex($value); 
if(strlen($hex_value)<2){$hex_value="0".$hex_value;}
$hex_RGB.=$hex_value;
}

return "#".$hex_RGB;
}

}

?>
