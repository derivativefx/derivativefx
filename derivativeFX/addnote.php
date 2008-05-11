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
session_start();
//empfangen
$data = $_GET['data'];
$token = $_GET['token'];
$file = $_GET['file'];
$adanote = $_GET['adanote'];

$dataarray = unserialize(base64_decode($data));

//Session managen

if($_SESSION[md5($file)] == md5($data))
{
$die = true;
}
else
{
$_SESSION[md5($file)] = md5($data);
}



?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html lang="en">
<head>

  <meta content="text/html; charset=UTF-8" http-equiv="content-type">
  <title>derivativeFX</title>
  <meta content="Luxo" name="author">
  <link rel="stylesheet" type="text/css" href="style/style.css">
</head>
<body style="direction: ltr;" class="bodynorm">
<img src="derivativeFX_small.png" />
<?php

if($die == true)
{
?>
<img src="dontreload.png" />
Please don't send the content again, it's already sended. Thanks!<br />
<?php
}
else if(md5($dataarray['token'] / 3) == $token AND $dataarray['time'] + 3600 > time() AND $file )
{
echo"<h1>Thank you</h1>Your file <b>$file</b> is going to Wikimedia:Commons.<br /><br />";
if($adanote == "true") {
echo"<b>User:Bilderbot will add in the next time a notice about this new derivative file to the original file(s).</b>\n"; }
//print_r($dataarray);


/*Das ganze in die Tabelle eintragen
*Name: derivativefx

*file
*derivative
*status
*time
*donetime
*/

include("/home/luxo/public_html/contributions/logindata.php");//pw & bn einbinden
$dblink = @mysql_connect($databankname, $userloginname, $databasepw);//Allgemein (TS-Database)

mysql_select_db("u_luxo", $dblink);//Zurückstellen

if(!$dblink)
{
die( "DATABASE ERROR.");
}


foreach($dataarray['originals'] as $origfile)
{

if($adanote == "true")
{
$status = "open";
}
else
{
$status = "nobot";
}

$time = time();
$donetime = "-";

mysql_query( "INSERT INTO derivativefx SET file='".mysql_real_escape_string($origfile)."', derivative='".mysql_real_escape_string($file)."', status='".mysql_real_escape_string($status)."', time='".mysql_real_escape_string($time)."', donetime='".mysql_real_escape_string($donetime)."'", $dblink) or die("Error");

}



}
else
{
echo"Whooops, error!<br />
<br />";
}



?>
<hr />
<center>by Luxo</center>
</body>
</html>
