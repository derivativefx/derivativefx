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
if($_SERVER["REQUEST_METHOD"] != "POST")
{
header('Location: http://'. $_SERVER['SERVER_NAME'] .'/~luxo/derivativeFX/deri1.php');
die();
}
//error_reporting(E_ALL);


//print_r($_POST);
$imagesdata = unserialize(base64_decode($_POST['data']));
//print_r($imagesdata);
//Autor(en) auslesen, nicht ganz einfach
//1.) Wenn |author angegeben, dieses Feld verwenden
//2.) Wenn bekannte Lizenzen mit Urheberinfos, diese Verwenden
//3.) Wenn Stichworte wie selfmade oder own work auftauchen, uploader verwenden

//array für next aufbauen
$originals = array();

foreach($imagesdata as $imagename => $imagedata)
{
$originals[] = $imagename;

    $author = "";
    //|Author= auslesen
    $start = stripos($imagedata["content"]["*"], "|Author");
    $end   = stripos($imagedata["content"]["*"], "|Permission");
    if($start AND $end)
    {
      $author = substr($imagedata["content"]["*"],$start,$end-$start );
      $author = trim(substr(strstr($author,"="),1));
      
    }
    else
    {
      //bekannte lizenzen filtern
      foreach($imagedata["licenses"] as $liz)
      {
        if(stristr($liz, "PD-USGov-NASA"))
        {
          $author = "created by [[en:NASA|NASA]]";
        }
        if(stristr($liz, "PD-USGov-NPS"))
        {
          $author = "work of a [[:en:National Park Service|National Park Service]] employee";
        }
        if(stristr($imagedata["content"]["*"], "{{Agência Brasil}}"))
        {
          $author = "produced by [[:en:Agência Brasil|Agência Brasil]], a public Brazilian news agency";
        }   
        if(stristr($imagedata["content"]["*"], "{{MdB}}"))
        {
          $author = "produced by the [[:en:Brazilian Navy|Brazillian navy]]";
        }   
        if(stristr($imagedata["content"]["*"], "{{CC-AR-Presidency}}"))
        {
          $author = "taken from the [http://www.presidencia.gov.ar/ Presidency of Argentina web site]";
        }
        if(stristr($liz, "PD-PDphoto.org"))
        {
          $author = "image from [http://pdphoto.org/ PD Photo.org]";
        }
      }
      if(!$author)
      {
      //nach schlagwörter suchen
        if(stristr($imagedata["content"]["*"], "myself") OR stristr($imagedata["content"]["*"], "own work") OR stristr($imagedata["content"]["*"], "selfmade") or stristr($imagedata["content"]["*"], "self made"))
        {
          $authorkey = count($imagedata["imageinfo"]) - 1;
          $author = "[[User:".$imagedata["imageinfo"][$authorkey]["user"]."|".$imagedata["imageinfo"][$authorkey]["user"]."]]";
        }
      }
    }
    
    if($author)
    {
    $Authors .= "*[[:$imagename|]]: ".trim($author)."\n";
    }
    
}

//Beginne Formular aufzubauen
$formular  = "{{Information\n|Description=".stripslashes($_POST['description'])."\n";
$formular .= "|Source=";
foreach($imagesdata as $imagename => $imagedata)
{
$formular .= "*[[:$imagename|]]\n";
$tmpimg = $imagename;
}
$formular .= "|Date=".date("Y-m-d H:i",time())." (UTC)\n";
$formular .= "|Author=".$Authors;
$formular .= "|Permission=see below\n";
$formular .= "|other_versions=\n}}\n\n";

//Vorlage RetouchedPicture
if($_POST["addtempret"] == "true")
{
  $formular .= "{{RetouchedPicture|".$_POST["changestemp"]."|editor=".$_POST['editor']."|orig=".substr($tmpimg,6)."}}\n\n";
}

//Template:BWS
if($_POST["addbwstemp"] == "true")
{
  $formular .= "{{Bilderwerkstatt|changes=".$_POST["changesbws"]."|editor=~~~|orig=".substr($tmpimg,6)."}}\n\n";
}

//Template {{Atelier graphique}}
if($_POST["addfrbws"] == "true")
{
  $formular .= "{{Atelier graphique}}\n\n";
}

//Template {{Atelier graphique carte}}
if($_POST["addfrkws"] == "true")
{
  $formular .= "{{Atelier graphique carte}}\n\n";
}
//********* Lizenz

$formular .= "{{".$_POST["license"]."}}\n\n";

//history
$formular .= "== Original upload log ==\nThis image is a derivative work of the following images:\n";

foreach($imagesdata as $imagename => $imagedata)
{
$formular .= "\n*[[:$imagename]] licensed with ";
foreach($imagedata["licenses"] as $key => $lizenz )
{
if($key != 0) { $formular .= ", ".$lizenz; } else {$formular .= $lizenz; }
}
$formular .= "\n";
foreach($imagedata["imageinfo"] as $vkey => $cntns)
{
  $formular .= "**".$imagedata["imageinfo"][$vkey]["timestamp"]." [[User:".$imagedata["imageinfo"][$vkey]["user"]."|".$imagedata["imageinfo"][$vkey]["user"]."]] ".$imagedata["imageinfo"][$vkey]["width"]."x".$imagedata["imageinfo"][$vkey]["height"]." (".$imagedata["imageinfo"][$vkey]["size"]." Bytes) ''<nowiki>".substr(strip_tags(str_replace("\n"," ",$imagedata["imageinfo"][$vkey]["comment"])),0,225)."</nowiki>''\n";
}

}
$formular .= "\n";

//Kategorien anhängen
foreach($_POST as $Cname => $Cvalue)
{
  if(substr($Cname,0,8) == "Category")
  {
    $formular .= "[[Category:$Cvalue]]\n";
  }

}


//Array zur übergabe vorbereiten

$token = rand(100000,999999);
$checksum = md5($token / 3);
$nextarray = array("originals" => $originals, "token" => $token, "time" => time());

$nextarray = base64_encode(serialize($nextarray));


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html lang="en">
<head>

  <meta content="text/html; charset=UTF-8" http-equiv="content-type">
  <title>derivativeFX</title>
  <meta content="Luxo" name="author">
  <link rel="stylesheet" type="text/css" href="style/style.css">
    <script type="text/javascript" src="js/prototype.js"></script>
        <script type="text/javascript" src="js/checkupload.js"></script>
    <script type="text/javascript">
    var checksum = "<?php echo $checksum; ?>";
    var data     = "<?php echo $nextarray; ?>";
    
    
    function enableupload()
    {
      var button = $("startupload");
      
      if(button.disabled == true)
      {
        button.disabled = false;
      }
      else
      {
        button.disabled = true;
      }   
    }
    

    </script>
</head>
<body style="direction: ltr;" class="bodynorm">
<img src="derivativeFX_small.png" />
<form id="comform" method='post' enctype='multipart/form-data' action="http://commons.wikimedia.org/wiki/Special:Upload" name="lastform"><br>

Select your derivative file:<br>

  <input name="wpUploadFile" type="file" size="50"><br>
  <input type='hidden' name='wpSourceType' value='file' id="atfile" />

  <br>
  Destination filename: <br>  
  <input type="text" name="wpDestFile" size="50" id="newfilename" onchange='checkimg(this.value)' onkeyup="checkimg(this.value);"><br>
<br>
Summary:<br>

  <textarea rows='25' cols='90' name="wpUploadDescription"><?php echo htmlspecialchars($formular); ?></textarea><br>
  <input type='hidden' name='wpLicense' value='' />
<input checked="checked" name="wpWatchthis" value="true" type="checkbox"> Watch this page
  <br>

  <input name="addorig" id="notibutton" value="true" type="checkbox"> Add a notice to the original file(s) about
this derivative work. (by Bot)<br>

  <br>

  <br>

  <input name="acceptterm" value="true" type="checkbox" onClick="enableupload();"><span class="acceptterm" >
You confirm that all details in&nbsp;the file description above are
  <span style="font-weight: bold;">correct and conformable
with the license(s) of the original-file(s)</span>. This tool and
his developer&nbsp;assume no accountability about the correctness
of the generated content. &nbsp;It has been created in the hope
that it will be useful, but <span style="font-weight: bold;">WITHOUT
ANY WARRANTY. You are accountable for
the correctness! </span>Image name, original files and time is logged.<br></span>

  <br>

  <input onclick="return upchecker()" disabled="disabled" id="startupload" name='wpUpload' value="Upload file" title="Start upload" type="submit" > &nbsp;<br>

  <br>
<input type='hidden' name='wpDestFileWarningAck' id='wpDestFileWarningAck' value=''/>
</form>
<hr style="height: 2px; width: 60%;">
<div style="text-align: center;">by <a href="/%7Eluxo/">Luxo</a>
| <a href="http://commons.wikimedia.org/wiki/User_talk:Luxo">contact</a>
| <a
 href="http://meta.wikimedia.org/wiki/User:Luxo/Licenses#derivativeFX">license</a><br>
<br>
<a href="http://wiki.ts.wikimedia.org/view/Main_Page"><img
 style="border: 0px solid ; width: 88px; height: 31px;"
 alt="powered by Wikimedia Toolserver"
 title="powered by Wikimedia Toolserver"
 src="http://tools.wikimedia.de/images/wikimedia-toolserver-button.png"></a>&nbsp;</div>

</body>
</html>














