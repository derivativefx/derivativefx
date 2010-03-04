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
    

   
   
   //Funktion um Quelltext zu laden 
function wikitextload($page)
{
  $project = "commons.wikimedia.org";
  $page = str_replace(" ","_",$page);
  $url = "http://".$project."/w/index.php?title=".urlencode($page)."&action=raw";
  $wikitext = file_get_contents($url);
  return($wikitext);
}
//#############################################################
function firstbig($username)
{

$firstletter = substr($username, 0,1);
$rest = substr($username,1);

$firstletter = strtoupper( $firstletter );

$reexit = $firstletter.$rest;

return ($reexit);


}

//#############################################################

function addlog($text)
{
  echo $text."\n";
}

//#############################################################

function checkderivative($imagename)
{
  static $deristatus = array();
  
  if(!$deristatus[$imagename])
  {
    //Existenz prüfen
    $api = unserialize(file_get_contents("http://commons.wikimedia.org/w/api.php?action=query&format=php&titles=".urlencode($imagename)."&prop=imageinfo"));
    
    if($api["query"]["pages"]["-1"])
    {
      //nicht existent
      $return = false;
    }
    else
    {
      $return = true;
    }
    
  }
  else
  {
    $return = $deristatus[$imagename];
  }
  
  $deristatus[$imagename] = $return;
  
  return $return;
  
}

//########################################################

function addnote($derivatives,$origtitle,$rawdesc=false)
{
  if($rawdesc === false)
  {
    $rawdesc = wikitextload($origtitle);
  }
  
  if(!$rawdesc){ die("beschreibung leer"); }
  
  //text zwischen <nowiki> und </nowiki> ersetzen
  $rawdesc = remove_nowiki($rawdesc);
  
  if(stristr($rawdesc, "{{DerivativeVersions"))
  {
    $start = stristr($rawdesc, "{{DerivativeVersions");
    $end = strpos($start, "}}");
    $template = substr($start,2,$end-2);
    //echo $template;
    $content = explode("|", $template);
    
    foreach($content as $eximagename)
    {
      if($eximagename != "DerivativeVersions")
      {
        $eximagename = str_replace("_"," ",$eximagename);
        $derivatives[$eximagename] = $eximagename; 
      }
    }
    
    //neues Template erstellen
    $newtemp = "DerivativeVersions";
    foreach($derivatives as $image)
    {
      $newtemp .= "|".$image;
    }
    
    $newraw = str_ireplace($template,$newtemp,$rawdesc);
     
  }
  else if(stristr($rawdesc, "{{Information"))  //Mit Vorlage:Information
  {
  
    //neues Template erstellen
    $newtemp = "{{DerivativeVersions";
    $x = 0;
    foreach($derivatives as $image)
    {
      if(!stristr($rawdesc,$image) AND !stristr($rawdesc,str_replace(" ","_",$image)))
      {
        $newtemp .= "|".$image;
        $x++;
      }
    }
    
    $newtemp .= "}}\n";
    
    if($x > 0)
    {
      $otherversion = array(
      "|other_versions=",
      "|other_versions =",
      "| other_versions =",
      "| other_versions=",
      "|other_versions	=");
      
      $count = "";
      $ersetzt = false;
      foreach($otherversion as $andereversion)
      {
        if(stristr($rawdesc,$andereversion))
        {
          $rawdesc = str_ireplace($andereversion,"|other_versions=".$newtemp,$rawdesc,$count);
          $ersetzt = true;
        }
      }
      
      if($ersetzt == false)
      {
        $rawdesc = str_ireplace("{{Information","{{Information\n|other_versions=".$newtemp,$rawdesc);
      }
    }
    $newraw = $rawdesc;
  
  }
  else
  {
    //ohne Vorlage:Information
    
    
      $newtemp = "{{DerivativeVersions";
      foreach($derivatives as $image)
      {
        $newtemp .= "|".$image;
      }
      
      $newtemp .= "}}";
    
    $catpos = strpos($rawdesc, "[[Category:");
    if($catpos)
    {
      $pre = substr($rawdesc,0,$catpos);
      $after = substr($rawdesc,$catpos);
    }
    else //falls keine kats vorhanden sind
    {
      $pre = $rawdesc;
      $after = "";
    }
    $newraw = $pre."\n== derivative works ==\n".$newtemp."\n".$after;
      
  }
  
  //nowiki wieder einfügen
  $newraw = nowiki_replacer($newraw,1);

  return $newraw;
}

function remove_nowiki($msg) 
{
  $rgx_search  = "/<nowiki>(.*)<\/nowiki>/Uie";
  $msg = str_replace("\n","¶",$msg);
  do
    $msg = preg_replace($rgx_search,'nowiki_replacer("\\1")',$msg,-1,$ct);
  while($ct != 0);
  $msg = str_replace("¶","\n",$msg);
  return $msg;
}

function nowiki_replacer($msg,$x = 0)
{
  static $save = array();
  
  if($x == 0) {
    $replacer = generatereplacer($msg);
    $save[] = $msg;
    return $replacer;
  }
  if($x == 1) {
    foreach($save as $nowikiline) {
      $replacer = generatereplacer($nowikiline);
      $msg = str_replace($replacer,"<nowiki>".$nowikiline."</nowiki>",$msg);
    }
    $msg = str_replace("¶","\n",$msg);
    return $msg;
  }  
}

function generatereplacer($str)
{
  $lenght = strlen($str);
  $y = 0;
  while($y < $lenght) {
    $replacer .= "•";
    $y++;
  }
  return $replacer;
}

    
?>
