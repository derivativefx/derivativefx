<?php
/*
Copyright Luxo 2008

This file is part of derivativeFX.

    derivativeFX is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    derivativeFX is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with derivativeFX.  If not, see <http://www.gnu.org/licenses/>.
    
    */
$language = $_GET['lang'];
include("language.php");

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

$spruch = sprintf($lng['x']['imgu'],$anz);

imageTTFtext($img,15,14,10,85,$red,$font,$spruch);

imagepng($img);


?>
