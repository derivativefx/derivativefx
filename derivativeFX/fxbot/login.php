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

// ############### EDIT WIKIPEDIA - FUNCTION ###############
function wikiedit($project,$page,$newtext,$description,$minor,$username,$password)
{



logfile($page,"Schreibe Text am ".date("r",time())." in die Seite '$page'.\n");

$getrequest = "/w/api.php?action=login";
$postrequest = "lgname=$username&lgpassword=$password";
$useragent = "Luxobot/1.0 (toolserver; php) luxo@ts.wikimedia.org";
$accept = "text/xml,application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5";

$fp = fsockopen ($project, 80, $errno, $errstr, 30);

//Request Senden
fputs($fp, "POST $getrequest HTTP/1.1\n");
fputs($fp, "Host: $project\n");
fputs($fp, "User-Agent: $useragent\n");
fputs($fp, "Connection: close\n");
fputs($fp, "Content-Type: application/x-www-form-urlencoded\n");
fputs($fp, "Content-Length: ".strlen($postrequest)."\n");
fputs($fp, "\n");
fputs($fp, $postrequest);

//Response Header auslesen
do {
$line=fgets($fp,255);
$header.=$line;

//auf cookie prüfen
if(substr($line,0,11) == "Set-Cookie:")
{
$cookies[] = substr($line,11,strpos($line,";")-11);
}


} while (trim($line)!="");
fclose($fp);
logfile($page,"Angemeldet in $project!\n");
//echo $header."\n\n";

// Response Body auslesen
/*while (!feof($fp)) { 
$linew=fgets($fp,255);
$bodyw.=$linew;
}
echo $bodyw;*/
$header = "";


//Angemeldet, Cookies ausgelesen, editieren kann beginnen**************
$fpb = fsockopen ($project, 80, $errno, $errstr, 30);

//Bearbeiten-Seite aufrufen, um wpEditToken & cookie zu erhalten ***************
$getrequest = "/w/index.php?title=$page&action=edit";
fputs($fpb, "GET $getrequest HTTP/1.1\n");
fputs($fpb, "Host: $project\n");
fputs($fpb, "User-Agent: $useragent\n");
fputs($fpb, "Accept: $accept\n");
fputs($fpb, "Accept-Language: de\n");

foreach ($cookies as $key=>$value) {
	
	if($key == "0")
	{
  $cookie.= trim($value);
  }
  else
	{
  $cookie.= ";".trim($value);
  }
	
}

logfile($page,"Lade Seite; Cookies: $cookie\n");

fputs($fpb, "Cookie: ".$cookie."\n");

fputs($fpb, "Connection: close\n");
fputs($fpb, "\n");


//Response Header auslesen forallem cooke********************
do {
$linex=fgets($fpb,255);
$headerrx.=$linex;

//auf cookie prüfen
if(substr($linex,0,11) == "Set-Cookie:")
{
$cookies[] = substr($linex,11,strpos($linex,";")-11);
}

} while (trim($linex)!="");

//cookie-header erneut generieren
$cookie = "";
foreach ($cookies as $key=>$value) {
	
	if($key == "0")
	{
  $cookie.= trim($value);
  }
  else
	{
  $cookie.= ";".trim($value);
  }
	
}
logfile($page,"Neue Cookies: $cookie\n");

//echo $headerrx."\n\n";
// Response Body auslesen**********************
while (!feof($fpb)) { 
$line=fgets($fpb,255);
$bodyy.=$line;
//Die verschiedenen form-data's auslesen
if(strstr($line, "wpStarttime"))
{
$formdata['wpStarttime'] = $line;
}
if(strstr($line, "wpEdittime"))
{
$formdata['wpEdittime'] = $line;
}
if(strstr($line, "wpScrolltop"))
{
$formdata['wpScrolltop'] = $line;
}
if(strstr($line, "wpEditToken"))
{
$formdata['wpEditToken'] = $line;
}
if(strstr($line, "wpAutoSummary"))
{
$formdata['wpAutoSummary'] = $line;
}
if(strstr($line, "wpSave"))
{
$formdata['wpSave'] = $line;
}
if(strstr($line, "baseRevId"))
{
$formdata['baseRevId'] = $line;
}
}
logfile($page,"Seite geladen, Anmeldung prüfen.\n");

if(strstr($bodyy,'var wgUserName = "Bilderbot";'))
{
logfile($page,"Anmeldung erfolgreich!\n");

//ende auslesen, verbindung schliessen
fclose($fpb);

//aus formdatas nur values nehmen

foreach($formdata as $type => $formcontent)
{

$t1 = strstr($formcontent,'value="');
$t2 = strpos($t1,'"',7);
$t1 = substr($t1,7,$t2-7);

$formdata["$type"] = $t1;
}


// ########################### POST-CONTENT VORBEREITEN #####################

//content vorbereiten
$addtocont = "";
if($formdata['baseRevId'])
{
$addtocont = "&baseRevId=".urlencode($formdata['baseRevId']);
}

$content = "wpSection=".$addtocont."&wpStarttime=".urlencode($formdata['wpStarttime'])."&wpEdittime=".urlencode($formdata['wpEdittime'])."&wpScrolltop=".urlencode($formdata['wpScrolltop'])."&wpTextbox1=".urlencode($newtext)."&wpSummary=".urlencode($description)."&wpMinoredit=".urlencode($minor)."&wpWatchthis=1&wpSave=".$formdata['wpSave']."&wpEditToken=".urlencode($formdata['wpEditToken'])."&wpAutoSummary=".urlencode($formdata['wpAutoSummary']);



logfile($page,"Content (".strlen($content)." Zeichen) vorbereitet, verbinde zum Speichern!\n");

//######## POST-Content vorbereitet, verbinden & POST-header senden #########

//zum speichern verbinden
$fpc = fsockopen ($project, 80, $errno, $errstr, 30);
//Speichern per Post.. ***************

$referer = "http://$project/w/index.php?title=".$page."&action=edit";

fputs($fpc, "POST /w/index.php?title=".$page."&action=submit HTTP/1.1\n");
fputs($fpc, "Host: $project\n");
fputs($fpc, "User-Agent: $useragent\n");
fputs($fpc, "Accept: $accept\n");
fputs($fpc, "Accept-Language: de\n");
//fputs($fpc, "Accept-Encoding: gzip,deflate\n"); //gzip --> seite komprimiert!
fputs($fpc, "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7\n");
fputs($fpc, "Keep-Alive: 300\n");
fputs($fpc, "Connection: keep-alive\n");
fputs($fpc, "Referer: $referer\n");
fputs($fpc, "Cookie: ".$cookie."\n");
fputs($fpc, "Content-Type: application/x-www-form-urlencoded\n");
fputs($fpc, "Content-Length: ".strlen($content)."\n");
fputs($fpc, "\n");
fputs($fpc, $content);
logfile($page,"Header gesendet.\n");


$line=fgets($fpc,255);

if(strstr($line,"Moved Temporarily"))
{
logfile($page,"Bearbeitung Erfolgreich.\n");
}
else
{
logfile($page,"BEARBEITUNG FEHLGESCHLAGEN!.\nFehler-Header: $line\n");
}




/*
while (!feof($fpc)) { 
$linew=fgets($fpc,255);
$bodyw.=$linew;
}
logfile($page,"-------\n".$bodyw."----------\n"); */
fclose($fpc);

echo"ende.";
}
else
{
logfile($page,"ANMELDUNG FEHLGESCHLAGEN, KONNTE NICHT ANMELDEN!\n");

}

}

function logfile($artikelname,$text)
{

$artikelname = str_replace(array("."," ",":","&","/"), array("-", "_", "-","-","-"), $artikelname);

$dateiname = "/home/luxo/Bilderbot/logfile/schreiben/".$artikelname."-".date("dmy",time()).".txt";
$fp = fopen($dateiname, "a");
fputs($fp,$text);
fclose($fp);
echo $text;
}


?>
