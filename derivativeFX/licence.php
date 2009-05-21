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

//Tool um die lizenz eines Bildes herauszufinden.
//Benutze Kategorien, in dem das Bild ist (nicht in dem die vorlagen sind)
//Vorlagen zurückverfolgen bis zur [[Category:Copyright statuses]]

$output = ""; 
$image = trim($_GET['image']);
$image = str_replace(" ", "_",$image);

if($image)
{

$url = "http://commons.wikimedia.org/w/api.php?action=query&prop=templates&format=php&tllimit=500&titles=".urlencode($image);

$raw = file_get_contents($url);

if($_GET['format'] == "whitelist")
{
  echo"Query URL: ".htmlspecialchars($url)." ($image)<br>\n";
} 

$unserialized = unserialize($raw);
//Array key (pageid) herausfinden
$pageid = array_keys($unserialized['query']['pages']);

if($unserialized['query']['pages'][$pageid['0']]['templates'])
{

//whitelist erstellen (Templates, die sicher keine Lizenzen sind)
$whitelist = array();
$addtowhitelist = array();

//whitelist aus datei laden
$rawwhitelist = file("whitelist.txt");
foreach($rawwhitelist as $whitelistentry)
{
  if(trim($whitelistentry) != "")
  {
    $whitelist[] = trim($whitelistentry);
  } 
}


//blacklist erstellen (Templates, die sicher Lizenzen sind)
$suretemplatelist = array();
$addtoblacklist = array();

//whitelist aus datei laden
$rawblacklist = file("blacklist.txt");
foreach($rawblacklist as $blacklistentry)
{
  if(trim($blacklistentry) != "")
  {
    $suretemplatelist[] = trim($blacklistentry);
  } 
}


if($_GET['format'] == "whitelist")
{
  echo"Query URL: ".htmlspecialchars($url)."<br>\n";
  echo"The following templates are on the whitelist:<br>\n";
  htmltemplatelist($whitelist);
  
    echo"The following templates are on the license template list:<br>\n";
  htmltemplatelist($suretemplatelist);
} 

foreach($unserialized['query']['pages'][$pageid['0']]['templates'] as $tmpl)
{

  $template = $tmpl['title']; //easyer
  $arraytemplates[] = $template;

if(!in_array($template,$whitelist) AND substr($template,-3) != "/en")
{

if(strlen($template) > 11 AND $template != "Template:PD" AND substr($template,0,14) != "Template:Potd/")
{

if($_GET['echo'] == true)
{
$output .=  $template."<br>";
}
  //Jede Vorlage prüfen: ist es eine Lizenz?

  $islicense[$template] = false; //default
  //Kategorien prüfen, dann noch Subkategorien auf "License tags" prüfen.
  
  //Kats der Vorlage laden.
  $url = "http://commons.wikimedia.org/w/api.php?action=query&prop=categories&format=php&cllimit=500&titles=".urlencode($template);
  $raw = file_get_contents($url);
  $catunserialized = unserialize($raw);
  $catid = array_keys($catunserialized['query']['pages']);
  //Vorlagen durchgehen
  if($catunserialized['query']['pages'][$catid['0']]['categories'])
  {
    foreach($catunserialized['query']['pages'][$catid['0']]['categories'] as $katofTemp)
    {
      if($katofTemp['title'] == "Category:License tags")
      {
        $islicense[$template] = true;
      }
    }
  }
  
  //Noch nicht als Lizenz identifiziert. Nun Kategorien der Kategorie durchsuchen.
  if($islicense[$template] == false && $catunserialized['query']['pages'][$catid['0']]['categories'])
  {
  foreach($catunserialized['query']['pages'][$catid['0']]['categories'] as $katofTemp)
  {
    $url = "http://commons.wikimedia.org/w/api.php?action=query&prop=categories&format=php&cllimit=500&titles=".urlencode($katofTemp['title']);
    $raw = file_get_contents($url);
    $catunserialized2 = unserialize($raw);
    $catid = array_keys($catunserialized2['query']['pages']);
    
    if($catunserialized2['query']['pages'][$catid['0']]['categories'])
    {
      foreach($catunserialized2['query']['pages'][$catid['0']]['categories'] as $KatOfKat)
      {
      if($KatOfKat['title'] == "Category:License tags")
      {
       $islicense[$template] = true;
      }
      }
    }
  }
  }
  
//**
  //Noch nicht als Lizenz identifiziert. Nun Kategorien der Unterkategorie durchsuchen.
  if($islicense[$template] == false && $catunserialized2['query']['pages'][$catid['0']]['categories'])
  {
  if($_GET['format'] == "whitelist"){ echo "Prüfe $template 3. mal!"; }
    foreach($catunserialized2['query']['pages'][$catid['0']]['categories'] as $katofTemp)
    {
      $url = "http://commons.wikimedia.org/w/api.php?action=query&prop=categories&format=php&cllimit=500&titles=".urlencode($katofTemp['title']);
      $raw = file_get_contents($url);
      $catunserialized3 = unserialize($raw);
      $catid = array_keys($catunserialized3['query']['pages']);
      
      if($catunserialized3['query']['pages'][$catid['0']]['categories'])
      {
        foreach($catunserialized3['query']['pages'][$catid['0']]['categories'] as $KatOfKat)
        {
          if($KatOfKat['title'] == "Category:License tags")
          {
           $islicense[$template] = true;
          }
        }
      }
    }
  }
//**
  
  }
  }
  
  if($islicense[$template] == false AND !in_array($template,$whitelist) AND !in_array($template,$blacklist))
  {
    if(!stristr($template, "Template:Potd/"))
    {
      $addtowhitelist[] = $template;
    }
  }
  else if($islicense[$template] == true AND !in_array($template,$suretemplatelist) AND !in_array($template,$whitelist))
  {
    $addtosuretemplatelist[] = $template;
  }
 
 
}
}
else
{
//Bild existiert nicht
$output .= "NOTEXIST";
}

if($islicense)
{

$blacklist = array(
"Template:Nonderivative",
"Template:Speedy delete text",
"Template:Speedydelete",
"Template:Delete",
"Template:Copyvio",
"Template:Nld",
"Template:Own work",
"Template:No license");
$save = true;
foreach($blacklist as $blacklisted){
if(in_array($blacklisted,$arraytemplates))
{
$save = false;
  }
}

  if($save == true){
  foreach($islicense as $lizenz => $isit)
  {
  if($isit == true){ 
  
  $output .=  substr($lizenz,9)."|"; }
  }
}
else
{ 
$output .= "DELETE"; }

}


if($_GET['format'] != "JSON")
{
  echo "\n\n\n<br><br><b>Output:</b><pre>".$output."</pre><br><br>\n\n";
} 
else
{
  //JSON format
  
  //thumurl auslesen
  //http://commons.wikimedia.org/w/api.php?action=query&titles=".urlencode($image)."&prop=imageinfo&iiprop=url&iiurlwidth=120&format=txtfm
  $url = "http://commons.wikimedia.org/w/api.php?action=query&titles=".urlencode($image)."&prop=imageinfo&iiprop=url&iiurlwidth=120&format=php";
  $query = unserialize(file_get_contents($url));
  
  foreach($query['query']['pages'] as $detquery)
  {
    $thumburl = $detquery['imageinfo']['0']['thumburl'];
  }
  

  header('Content-type: application/json');


if(trim($output) == "")
{
  $output = "NOLIC";
}

echo'{
  "licenses": "'.htmlspecialchars($output).'",
  "tumburl": "'.htmlspecialchars($thumburl).'", 
  "url": "'.$url.'",
}';

}


//Whitelist updaten
foreach($addtowhitelist as $temptowhite)
{
  $addit .= $temptowhite."\n";
}
if(trim($addit) != "")
{
  //schreiben
  file_put_contents("whitelist.txt", $addit, FILE_APPEND | LOCK_EX);
}


//**
//$addtosuretemplatelist updaten
foreach($addtosuretemplatelist as $temptosure)
{
  $addit .= $temptosure."\n";
}
if(trim($addit) != "")
{
  //schreiben
  file_put_contents("blacklist.txt", $addit, FILE_APPEND | LOCK_EX);
}

if($_GET['format'] == "whitelist")
{
  echo"put the following templates to the whitelist:<br>\n";
  htmltemplatelist($addtowhitelist);
  
    echo"put the following templates to the license template list:<br>\n";
  htmltemplatelist($addtosuretemplatelist);
} 


}
 
function htmltemplatelist($templates)
{
  echo"<ol>\n";
  foreach($templates as $template)
  {
    echo"<li><a href=\"http://commons.wikimedia.org/w/index.php?title=".urlencode($template)."\">".htmlspecialchars($template)."</a></li>\n";
  }
  if(count($templates) == 0)
  {
     echo"<li><i>nothing...</i></li>\n"; 
  }
  echo"</ol><br><br>\n\n";
}



?>
