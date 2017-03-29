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
ini_set( 'user_agent', ' derivativeFX on toolslabs / PHP' );

if ( $_SERVER["REQUEST_METHOD"] != "POST" ) {
	header( 'Location: https://' . $_SERVER['SERVER_NAME'] . '/derivativeFX/deri1.php' );
	die();
}
//error_reporting(E_ALL);
$language = $_GET['lang'];
include( "language.php" );

//print_r($_POST);
$imagesdata = unserialize( base64_decode( $_POST['data'] ) );
//print_r($imagesdata);
//Autor(en) auslesen, nicht ganz einfach
//1.) Wenn |author angegeben, dieses Feld verwenden
//2.) Wenn bekannte Lizenzen mit Urheberinfos, diese Verwenden
//3.) Wenn Stichworte wie selfmade oder own work auftauchen, uploader verwenden

//array für next aufbauen
$originals = array();
$noauthor = array(); //bilder, deren author nicht gefunden werden kann
foreach ( $imagesdata as $imagename => $imagedata ) {
	$originals[] = $imagename;

	$author = "";
	//|Author= auslesen
	$start = stripos( $imagedata["content"]["*"], "|Author" );
	$end1 = stripos( $imagedata["content"]["*"], "|Permission" );
	$end2 = stripos( $imagedata["content"]["*"], "|Date" );

	if ( ( $end2 - $start ) < ( $end1 - $start ) AND $end2 > $start ) {
		$end = $end2;
	} else {
		$end = $end1;
	}

	if ( $start AND $end ) {
		$author = substr( $imagedata["content"]["*"], $start, $end - $start );
		$author = stripslashes( trim( substr( strstr( $author, "=" ), 1 ) ) );
	} else {
		//bekannte lizenzen filtern
		foreach ( $imagedata["licenses"] as $liz ) {
			if ( stristr( $liz, "PD-USGov-NASA" ) ) {
				$author = "created by [[en:NASA|NASA]]";
			}
			if ( stristr( $liz, "PD-USGov-NPS" ) ) {
				$author = "work of a [[:en:National Park Service|National Park Service]] employee";
			}
			if ( stristr( $imagedata["content"]["*"], "{{Agência Brasil}}" ) ) {
				$author = "produced by [[:en:Agência Brasil|Agência Brasil]], a public Brazilian news agency";
			}
			if ( stristr( $imagedata["content"]["*"], "{{MdB}}" ) ) {
				$author = "produced by the [[:en:Brazilian Navy|Brazillian navy]]";
			}
			if ( stristr( $imagedata["content"]["*"], "{{CC-AR-Presidency}}" ) ) {
				$author = "taken from the [http://www.presidencia.gov.ar/ Presidency of Argentina web site]";
			}
			if ( stristr( $liz, "PD-PDphoto.org" ) ) {
				$author = "image from [http://pdphoto.org/ PD Photo.org]";
			}
		}
		if ( ! $author ) {
			//nach schlagwörter suchen
			if ( stristr( $imagedata["content"]["*"], "myself" ) OR stristr( $imagedata["content"]["*"], "own work" ) OR stristr( $imagedata["content"]["*"], "selfmade" ) or stristr( $imagedata["content"]["*"], "self made" ) or stristr( $imagedata["content"]["*"], "self-made" ) ) {
				$authorkey = count( $imagedata["imageinfo"] ) - 1;
				$author = "[[User:" . $imagedata["imageinfo"][$authorkey]["user"] . "|" . $imagedata["imageinfo"][$authorkey]["user"] . "]]";
			}
		}
	}

	if ( ! $author ) {
		$author = "'''PLEASE COMPLETE AUTHOR INFORMATION'''";
		$noauthor[] = $imagename;
	}

	if ( $author ) {
		$Authors .= "*[[:$imagename|]]: " . trim( $author ) . "\n";
	}

}
//Beginne Formular aufzubauen
$formular = "== {{int:filedesc}} ==\n{{Information\n|Description=" . trim( stripslashes( $_POST['description'] ) ) . "\n";
$formular .= "|Source={{Derived from";
foreach ( $imagesdata as $imagename => $imagedata ) {
	$formular .= "|" . substr( $imagename, 5 );
	$tmpimg = $imagename;
}
$formular .= "|display=50}}\n";
$formular .= "|Date=" . date( "Y-m-d H:i", time() ) . " (UTC)\n";
$formular .= "|Author=" . $Authors . "*derivative work: [[User:{{subst:REVISIONUSER}}|{{subst:REVISIONUSER}}]]\n";
$formular .= "|Permission=\n";
$formular .= "|other_versions=\n}}\n\n";

//Vorlage RetouchedPicture
if ( $_POST["addtempret"] == "true" ) {
	$formular .= "{{RetouchedPicture|" . $_POST["changestemp"] . "|editor=" . $_POST['editor'] . "|orig=" . substr( $tmpimg, 5 ) . "}}\n\n";
}

//Template:BWS
if ( $_POST["addbwstemp"] == "true" ) {
	$formular .= "{{Bilderwerkstatt|changes=" . $_POST["changesbws"] . "|editor=~~~|orig=" . substr( $tmpimg, 5 ) . "}}\n\n";
}

//Template {{Atelier graphique}}
if ( $_POST["addfrbws"] == "true" ) {
	$formular .= "{{Atelier graphique}}\n\n";
}

//Template {{Atelier graphique carte}}
if ( $_POST["addfrkws"] == "true" ) {
	$formular .= "{{Atelier graphique carte}}\n\n";
}
//********* Lizenz
$formular .= "== {{int:license-header}} ==\n";
$formular .= "{{" . $_POST["license"] . "}}\n\n";

//history
$formular .= "== {{Original upload log}} ==\nThis image is a derivative work of the following images:\n";

foreach ( $imagesdata as $imagename => $imagedata ) {
	$formular .= "\n*[[:$imagename]] licensed with ";
	foreach ( $imagedata["licenses"] as $key => $lizenz ) {
		if ( $key != 0 ) {
			$formular .= ", " . $lizenz;
		} else {
			$formular .= $lizenz;
		}
	}
	$formular .= "\n";
	foreach ( $imagedata["imageinfo"] as $vkey => $cntns ) {
		$formular .= "**" . $imagedata["imageinfo"][$vkey]["timestamp"] . " [[User:" . $imagedata["imageinfo"][$vkey]["user"] . "|" . $imagedata["imageinfo"][$vkey]["user"] . "]] " . $imagedata["imageinfo"][$vkey]["width"] . "x" . $imagedata["imageinfo"][$vkey]["height"] . " (" . $imagedata["imageinfo"][$vkey]["size"] . " Bytes) ''<nowiki>" . substr( strip_tags( str_replace( "\n", " ", $imagedata["imageinfo"][$vkey]["comment"] ) ), 0, 225 ) . "</nowiki>''\n";
	}

}
$formular .= "\n";
$formular .= "{{Uploaded with derivativeFX|version=2}}\n\n";
//Kategorien anhängen
foreach ( $_POST as $Cname => $Cvalue ) {
	if ( substr( $Cname, 0, 8 ) == "Category" ) {
		$formular .= "[[Category:$Cvalue]]\n";
	}

}


//Array zur übergabe vorbereiten

$token = rand( 100000, 999999 );
$checksum = md5( $token / 3 );
$nextarray = array( "originals" => $originals, "token" => $token, "time" => time() );

$nextarray = base64_encode( serialize( $nextarray ) );

//new filename propositions

foreach ( $imagesdata as $imagename => $imagedata ) {
	$onlyname = substr( $imagename, 0, strrpos( $imagename, "." ) );
	$extension = substr( $imagename, strrpos( $imagename, "." ) + 1 );
	$newnames[] = substr( $onlyname, 5 ) . "-2." . $extension;
	$newnames[] = substr( $onlyname, 5 ) . "-" . date( "Y-d-m", time() ) . "." . $extension;
//$newnames[] =  substr($onlyname,5)."_new.".$extension;

	$newnames[] = substr( $onlyname, 5 ) . "_cropped." . $extension;
	$newnames[] = substr( $onlyname, 5 ) . "_flopped." . $extension;
	$newnames[] = substr( $onlyname, 5 ) . "_flipped." . $extension;
	$newnames[] = substr( $onlyname, 5 ) . "_mirrored." . $extension;
}

//Warnung für nicht gefundene Authoren
$authorwarn = "";
if ( count( $noauthor ) > 0 ) {
	$authorwarn = "<div><img src='warn.png' /> <b>" . $lng['x']['plscom'] . "</b><br />";
}
foreach ( $noauthor as $tempimg ) {
	$authorwarn .= '- <a target="_blank" href="//commons.wikimedia.org/w/index.php?title=' . urlencode( $tempimg ) . '">' . $tempimg . '</a><br />';
}

if ( count( $noauthor ) > 0 ) {
	$authorwarn .= "</div><br /><br />";
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html lang="en">
<head>
	<meta content="text/html; charset=UTF-8" http-equiv="content-type">
	<title>derivativeFX</title>
	<meta content="Luxo" name="author">
	<link rel="stylesheet" type="text/css" href="style/style.css">
	<script type="text/javascript" src="js/prototype.js"></script>
	<script type="text/javascript" src="js/checkupload.js.php?lang=<?php echo $language; ?>"></script>
	<script type="text/javascript">

		var checksum = "<?php echo $checksum; ?>";
		var data = "<?php echo $nextarray; ?>";


		function enableupload() {
			var button = $( "startupload" );
			var cb = $( "accbut" );

			if ( cb.checked == true ) {
				button.disabled = false;
			}
			else {
				button.disabled = true;
			}
		}

		function propositionsc() {
			var props = $( "propositions" );
			var probsymb = $( "probsymb" );

			if ( props.style.display == "none" ) {
				props.style.display = "block";
				probsymb.firstChild.data = "▲";
			}
			else {
				props.style.display = "none";
				probsymb.firstChild.data = "▼";
			}
		}
	</script>
	<link href="//tools-static.wmflabs.org/cdnjs/ajax/libs/twitter-bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet"> 
</head>
<body style="direction: ltr;" class="bodynorm container">
<img src="derivativeFX_small.png"/>

<form id="comform" method='post' enctype='multipart/form-data'
	  action="//commons.wikimedia.org/wiki/Special:Upload?withjs=MediaWiki:Derivativefx-uploadform.js&specialform=derivativefxinterface" name="lastform"><br>
<!---
	<?php echo $lng['x']['selfil']; ?>:<br>

	<input name="wpUploadFile" type="file" size="50"><br>
	<input type='hidden' name='wpSourceType' value='file' id="atfile"/>
  old, no longer works because token handling changed on commons.
	<br>
	<?php echo $lng['x']['destin']; ?>: <br>
  <span style="background-color:white;font-size:x-small"><?php echo $lng['x']['propos']; ?> <a
		  href="javascript:propositionsc();" id="probsymb">▼</a><br>
  <span style="display:none" id="propositions"><ul>
		  <?php
		  foreach ( $newnames as $titlesx ) {
			  echo "<li><a href='javascript:$(\"newfilename\").value=\"" . htmlspecialchars( $titlesx ) . "\";checkimg($(\"newfilename\").value);'>" . htmlspecialchars( $titlesx ) . "</a></li>\n";
		  }
		  ?></ul></span>
  <br></span>
	<input type="text" name="wpDestFile" size="50" id="newfilename" onchange="checkimg(this.value);"
		   onkeyup="lasttatch();checkimg(this.value);"><br><br>

	<div id="existwarn"
		 style="display:none;border-width:1px;border-color:red;border-style:solid;padding:5px;background-color:#FFE4E1;">
		<img src="warn.png"> <?php echo $lng['x']['desexi']; ?><br><img id="falseimg"
																		src="http://commons.wikimedia.org/w/thumb.php?w=120&f="/><br><b>Image:</b><b
			id="imgtitle">...</b></div>
	<span id="dontexist"
		  style="display:none;border-width:1px;border-color:green;border-style:solid;padding:5px;background-color:#E0FFE0;"><img
			src="ok.png"> <?php echo $lng['x']['desoke']; ?><br></span>
-->
	<br>
	<?php echo $lng['x']['summar']; ?>:<br>

	<textarea rows='25' cols='100' name="wpUploadDescription"
			  id="desctext"><?php echo htmlspecialchars( $formular ); ?></textarea><br>
	<?php echo $authorwarn; ?>
	<br>
	<input type='hidden' name='wpLicense' value=''/>
	<label for="wpWatchthis"> <input checked="checked" name="wpWatchthis" id="wpWatchthis" value="true" type="checkbox"> <?php echo $lng['x']['watcht']; ?></label>
	<input name="wpIgnoreWarning" value="1" id="wpIgnoreWarning" type="hidden">
	<br>
	<br>
	<label for='accbut'> <input name="acceptterm" value="true" id="accbut" type="checkbox" onClick="enableupload();"><span class="acceptterm"> <?php echo $lng['x']['accept']; ?></label><br></span>

	<br>

	<input onclick="return upchecker()" disabled="disabled" id="startupload" name='wpUpload'
		   value="<?php echo $lng['x']['start']; ?>" title="Start upload" type="submit" class="btn btn-success"> &nbsp;<br>

	<br>
	<input type='hidden' name='wpDestFileWarningAck' id='wpDestFileWarningAck' value=''/>
</form>
<hr style="height: 2px; width: 60%;">
<div style="text-align: center;">The tool was originally written by <a href="//commons.wikimedia.org/wiki/User:Luxo">Luxo</a>
	| <a href="https://commons.wikimedia.org/wiki/Commons:DerivativeFX">about</a>
	| <a href="//github.com/derivativefx/derivativefx">source &amp; license</a></div>
</body>
</html>














