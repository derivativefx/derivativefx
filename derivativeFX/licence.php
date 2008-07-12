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

$image = $_GET['image'];
$image = str_replace(" ", "_",$image);
if($image)
{

$url = "http://commons.wikimedia.org/w/query.php?what=templates&format=php&titles=".urlencode($image);

$raw = file_get_contents($url);

$unserialized = unserialize($raw);
//Array key (pageid) herausfinden
$pageid = array_keys($unserialized['pages']);

if($unserialized['pages'][$pageid['0']]['templates'])
{
foreach($unserialized['pages'][$pageid['0']]['templates'] as $tmpl)
{

  $template = $tmpl['*']; //easyer
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
"Template:Zh-min-nan");


if(!in_array($template,$whitelist))
{
if(strlen($template) > 11 AND $template != "Template:PD" AND substr($template,0,14) != "Template:Potd/")
{

if($_GET['echo'] == true)
{
echo $template."<br>";
}
  //Jede Vorlage prüfen: ist es eine Lizenz?

  $islicense[$template] = false; //default
  //Kategorien prüfen, dann noch Subkategorien auf "License tags" prüfen.
  
  //Kats der Vorlage laden.
  $url = "http://commons.wikimedia.org/w/query.php?what=categories&format=php&titles=".urlencode($template);
  $raw = file_get_contents($url);
  $catunserialized = unserialize($raw);
  $catid = array_keys($catunserialized['pages']);
  //Vorlagen durchgehen
  if($catunserialized['pages'][$catid['0']]['categories'])
  {
    foreach($catunserialized['pages'][$catid['0']]['categories'] as $katofTemp)
    {
      if($katofTemp['*'] == "Category:License tags")
      {
        $islicense[$template] = true;
      }
    }
  }
  
  //Noch nicht als Lizenz identifiziert. Nun Kategorien der Kategorie durchsuchen.
  if($islicense[$template] == false && $catunserialized['pages'][$catid['0']]['categories'])
  {
  foreach($catunserialized['pages'][$catid['0']]['categories'] as $katofTemp)
  {
    $url = "http://commons.wikimedia.org/w/query.php?what=categories&format=php&titles=".urlencode($katofTemp['*']);
    $raw = file_get_contents($url);
    $catunserialized2 = unserialize($raw);
    $catid = array_keys($catunserialized2['pages']);
    
    if($catunserialized2['pages'][$catid['0']]['categories'])
    {
      foreach($catunserialized2['pages'][$catid['0']]['categories'] as $KatOfKat)
      {
      if($KatOfKat['*'] == "Category:License tags")
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
echo"NOTEXIST";
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
  
  echo substr($lizenz,9)."|"; }
  }
}
else
{ 
echo"DELETE"; }

}


}
 




?>
