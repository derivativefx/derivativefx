<?php
//********************* DATENBANK
include("/home/luxo/public_html/contributions/logindata.php");//pw & bn einbinden
$dblink = @mysql_connect($databankname, $userloginname, $databasepw);//Allgemein (TS-Database)

mysql_select_db("u_luxo", $dblink);//Zurückstellen

$ct = mysql_query("SELECT COUNT(*) FROM derivativefx",$dblink);
$ct_Array= @mysql_fetch_row($ct);
$anz = $ct_Array["0"]; 

//*********************IMAGE
header("Content-type: image/png");


$img = imagecreatefrompng("derivativeFX_small.png");
imageAlphaBlending($img, true);
imageSaveAlpha($img, true);

$font = "/home/luxo/public_html/derivativeFX/font.ttf";

$red = imagecolorallocate($img,225,0,0);
imageTTFtext($img,15,14,10,85,$red,$font,"$anz images uploaded!");

imagepng($img);


?>
