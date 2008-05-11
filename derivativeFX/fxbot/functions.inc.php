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


$url = "http://".$project."/w/index.php?title=".$page."&action=raw";

$wikitext = @file_get_contents($url);

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
function addnote($derivatives,$origtitle)
{
  
  $rawdesc = wikitextload($origtitle);
  
  
  
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
    
    foreach($derivatives as $image)
    {
      $newtemp .= "|".$image;
    }
    
    $newtemp .= "}}";
    
    
    
    $otherversion = array(
    "|other_versions=",
    "|other_versions =",
    "| other_versions =",
    "| other_versions=");
    
    $count = "";
    $ersetzt = false;
    foreach($otherversion as $andereversion)
    {
      if(strstr($rawdesc,$andereversion))
      {
        $rawdesc = str_ireplace($andereversion,"|other_versions=".$newtemp,$rawdesc,$count);
        $ersetzt = true;
      }
    }
    
    if($ersetzt == false)
    {
      $rawdesc = str_ireplace("{{Information","{{Information\n|other_versions=".$newtemp,$rawdesc);
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
    $rawdesc = $pre."\n".$newtemp."\n".$after;
      
  }

  return $rawdesc;
}


    
?>
