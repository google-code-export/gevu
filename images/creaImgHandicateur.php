<?php
try {
	if(isset($_GET["crea"]) && !$_GET["crea"]){
		extract($_GET);
		//header('Content-type: image/jpeg');
		header('Content-type: image/png');
		creaHandiImg($a, $c, $m, $v, false);				
	}
	
} catch (Exception $e) {
	echo "Message: " . $e->getMessage() . "\n";	
}

function creaHandiImg($a, $c, $m, $v, $crea){

	$file = "handi_";
	$pathImg = $file.substr($a, 0, -4).substr($c, 0, -4).substr($m, 0, -4).substr($v, 0, -4).".png";

	//$destination = imagecreate(148,38);
	$destination = imagecreatetruecolor(148,38);
	imagecolorallocate($destination, 255, 255, 255);

	$destination_x = 0;
	$destination_y = 0;

	// On charge d'abord les images
	$sourceA = imagecreatefromjpeg($a);
	//imagecopymerge($destination, $sourceA, $destination_x, $destination_y, 0, 0, 37, 38, 100);
	imagecopy($destination, $sourceA, $destination_x, $destination_y, 0, 0, 37, 38);
	$destination_x += 37;

	$sourceC = imagecreatefromjpeg($c);
	//imagecopymerge($destination, $sourceC, $destination_x, $destination_y, 0, 0, 37, 38, 100);
	imagecopy($destination, $sourceC, $destination_x, $destination_y, 0, 0, 37, 38);
	$destination_x += 37;

	$sourceM = imagecreatefromjpeg($m);
	//imagecopymerge($destination, $sourceM, $destination_x, $destination_y, 0, 0, 37, 38, 100);
	imagecopy($destination, $sourceM, $destination_x, $destination_y, 0, 0, 37, 38);
	$destination_x += 37;

	$sourceV = imagecreatefromjpeg($v);
	//imagecopymerge($destination, $sourceV, $destination_x, $destination_y, 0, 0, 37, 38, 100);
	imagecopy($destination, $sourceV, $destination_x, $destination_y, 0, 0, 37, 38);

	if($crea){
		imagepng($destination, $pathImg);
		echo "image crée :".$pathImg."<img src='".$pathImg."'/>";
	}else
		imagepng($destination);

	imagedestroy($sourceA);
	imagedestroy($sourceC);
	imagedestroy($sourceM);
	imagedestroy($sourceV);
	imagedestroy($destination);
}
?>
