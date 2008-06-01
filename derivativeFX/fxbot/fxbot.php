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
include("logindata.php");//Include PW & UN
include("functions.inc.php");
include("login.php");

addlog("Bot gestartet! (".time().")");
include("/home/luxo/public_html/contributions/logindata.php");//pw & bn einbinden
$dblink = mysql_connect($databankname, $userloginname, $databasepw) or die(mysql_error());//Allgemein (TS-Database)

mysql_select_db("u_luxo", $dblink);

       $resu1 = mysql_query( "SELECT * FROM derivativefx WHERE status='open'", $dblink) or die(mysql_error());
addlog("Datenbankabfrage erfolgreich.");

$images = array();
while ($a_row2 = mysql_fetch_row($resu1)) 
{	
	$file       = $a_row2["0"]; 
  $derivative = $a_row2["1"]; 
  $status     = $a_row2["2"];
  $time       = $a_row2["3"]; 
  $donetime   = $a_row2["4"]; 
  
  //$file = str_replace("_"," ",$file);
  //$derivative = str_replace("_"," ",$derivative);
  
  $images[$file]["derivativesTC"][$derivative] = $derivative;
  $images[$file]["time"] = $time;
}


//Inexistente Bilder ausmisten
$stopbot = true;


foreach($images as $name => $array)
{

  foreach($array["derivativesTC"] as $derivativfile)
  {
    $deristatus = checkderivative("Image:".$derivativfile);
    if($deristatus == true)
    {
      $images[$name]["derivatives"][$derivativfile] = $derivativfile;
      addlog("Image:$derivativfile existiert, ok.");
      $stopbot = false;
    }
    else
    {
      $images[$name]["error"][$derivativfile] = $derivativfile;
      addlog("Image:$derivativfile existiert nicht!");
      $stopbot = false;
    }
  }
}

if($stopbot == true)
{
  addlog("Keine Bilder vorhanden, Beenden.");
  die();
}

addlog("Bilder erfolgreich auf existenz überprüft.");
//neue Beschr.seiten generieren

foreach($images as $name => $array)
{
  if($array["derivatives"])
  {
    $newqtext = addnote($array["derivatives"],$name);
    $images[$name]["newdesc"] = $newqtext;
    addlog("Generiere neue Bildbeschreibung für $name.");
  }
}

//Nun noch datei abspeichern
foreach($images as $name => $array)
{
  if($array["newdesc"])
  {
    addlog("Speichere neue Bildbeschreibung von $name.");
    wikiedit("commons.wikimedia.org",$name,$array["newdesc"],"Bot: notice of a derivative work added","true",$username,$password);
    sleep(15);
  }
  $images[$name]["donetime"] = time();
}

addlog("Update Datenbank");
foreach($images as $name => $array)
{
  if($array["derivatives"])
  {
    foreach($array["derivatives"] as $derivativfile)
    {
      $resu1 = mysql_query( "UPDATE `u_luxo`.`derivativefx` SET `status`='done', `donetime`='".mysql_real_escape_string($array["donetime"])."' WHERE CONVERT(`derivativefx`.`status` USING utf8) = 'open' AND CONVERT(`derivativefx`.`file` USING utf8) ='".mysql_real_escape_string($name)."' AND CONVERT(`derivativefx`.`derivative` USING utf8)='".mysql_real_escape_string($derivativfile)."'", $dblink) or die(mysql_error());
      echo"UPDATE `u_luxo`.`derivativefx` SET `status`='done', `donetime`='".mysql_real_escape_string($array["donetime"])."' WHERE CONVERT(`derivativefx`.`status` USING utf8) = 'open' AND CONVERT(`derivativefx`.`file` USING utf8) ='".mysql_real_escape_string($name)."' AND CONVERT(`derivativefx`.`derivative` USING utf8)='".mysql_real_escape_string($derivativfile)."'\n";
    }
  }
  if($array["error"])
  {
    foreach($array["error"] as $derivativfile)
    {
      $resu1 = mysql_query( "UPDATE `u_luxo`.`derivativefx` SET `status`='noexist', `donetime`='".mysql_real_escape_string($array["donetime"])."' WHERE CONVERT(`derivativefx`.`status` USING utf8) = 'open' AND CONVERT(`derivativefx`.`file` USING utf8) ='".mysql_real_escape_string($name)."' AND CONVERT(`derivativefx`.`derivative` USING utf8)='".mysql_real_escape_string($derivativfile)."'", $dblink) or die(mysql_error());
      echo"UPDATE `u_luxo`.`derivativefx` SET `status`='noexist', `donetime`='".mysql_real_escape_string($array["donetime"])."' WHERE CONVERT(`derivativefx`.`status` USING utf8) = 'open' AND CONVERT(`derivativefx`.`file` USING utf8) ='".mysql_real_escape_string($name)."' AND CONVERT(`derivativefx`.`derivative` USING utf8)='".mysql_real_escape_string($derivativfile)."'\n";

    }
  }
}

addlog("Botdurchgang erfolgreich, Ende.");
?>
