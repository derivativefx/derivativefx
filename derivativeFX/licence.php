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
$image = $_GET['image'];
$image = str_replace(" ", "_",$image);
if($image)
{

$url = "http://commons.wikimedia.org/w/api.php?action=query&prop=templates&format=php&tllimit=500&titles=".urlencode($image);

$raw = file_get_contents($url);

$unserialized = unserialize($raw);
//Array key (pageid) herausfinden
$pageid = array_keys($unserialized['query']['pages']);

if($unserialized['query']['pages'][$pageid['0']]['templates'])
{
foreach($unserialized['query']['pages'][$pageid['0']]['templates'] as $tmpl)
{

  $template = $tmpl['title']; //easyer
  $arraytemplates[] = $template;
//Einige Templates von Vornherein ausschliessen, um Ladezeit zu verkürzen
$whitelist = array(
"Template:CC-Layout",
"Template:Cc-by-sa-2.5,2.0,1.0/lang",
"Template:Tlp",
"Template:Als",
"Template:Ast",
"Template:Rtl-lang",
"Template:Description",
"Template:Description missing",
"Template:Description missing/lang",
"Template:Edit",
"Template:GFDL/lang",
"Template:GFDL-self", //>>Included {{GFDL}}
"Template:GFDL-user", //>>Included {{GFDL}}
"Template:GNU-Layout",
"Template:Information",
"Template:Lang",
"Template:Self",
"Template:Self2",
"Template:PD/lang",
"Template:PD-Layout",
"Template:PD-self/lang",
"Template:Public domain",
"Template:Template link",
"Template:Template link with parameter",
"Template:Picture of the day",
"Template:Information Picswiss",
"Template:Cc-by-sa",
"Template:Flickrreview",
"Template:Featured picture",
"Template:Personality rights",
"Template:Personality rights/lang",
"Template:Zh-hans",
"Template:Zh-hant",
"Template:Zh-min-nan",
"Template:Flickrreview");


if(!in_array($template,$whitelist))
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

  
  }
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
  echo $output;
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
}';

}


}
 




?>
