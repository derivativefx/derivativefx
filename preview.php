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

ini_set( 'user_agent', ' derivativeFX by Luxo on the Toolserver / PHP' );

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8">
	<meta name="author" content="Luxo">
	<title>Preview of Upload</title>
	<style type="text/css" media="screen,projection">
		/*
		<![CDATA[*/
		@import "/~luxo/skins/toolserver/main.css?67";

		/*]]>
			*/
	</style>

</head>
<body class="mediawiki ns--1 ltr page-Spezial_Beiträge" style="direction: ltr;">
<div id="globalWrapper" style="position:absolute;top:5px;bottom:5px;right:5px;left:5px;">
	<?php

	//EXAMPLE TEXT
	$prev = "{{Information
|Description=Example image, used for tracking test edits
|Source=*[[:File:Example.jpg|]]
|Date=" . date( "r" ) . " (UTC)
|Author=*[[:File:Example.jpg|]]: [[User:Bdk|<font color=#116611>:Bdk:</font>]]
*derivative work: ~~~
|Permission=see below
|other_versions=
}}

{{RetouchedPicture|some details|editor=Luxo|orig=Example.jpg}}

{{PD-ineligible}}

== Original upload log ==
This image is a derivative work of the following images:

*[[:File:Example.jpg]] licensed with PD-ineligible
**2009-06-06T21:03:31Z [[User:Bawolff|Bawolff]] 172x178 (9022 Bytes) ''<nowiki>Reverted to version as of 20:15, 6 June 2009</nowiki>''
**2009-06-06T20:15:28Z [[User:Túrelio|Túrelio]] 172x178 (9022 Bytes) ''<nowiki>Reverted to version as of 15:51, 7 March 2006</nowiki>''
**2006-03-07T15:51:32Z [[User:Dbenbenn|Dbenbenn]] 172x178 (9022 Bytes) ''<nowiki>Reverted to earlier revision</nowiki>''
**2006-03-07T09:32:19Z [[User:I.R. Annie IP.|I.R. Annie IP.]] 172x178 (12702 Bytes) ''<nowiki></nowiki>''
**2005-07-25T21:22:50Z [[User:Bdk|Bdk]] 172x178 (9022 Bytes) ''<nowiki>selfmade image by ~~~  --> it is used for tracking test edits  {{PD}}</nowiki>''

''Uploaded with [[:tools:~luxo/derivativeFX/|derivativeFX]]''

[[Category:Example images]]";

	if ( $_GET['text'] ) //get default text
	{
		$prev = $_GET['text'];
		$prev = str_replace( "~~~", "''<your username>''", $prev );
		//replace {{subst:REVISIONUSER}}
		$prev = str_replace( "[[User:{{subst:REVISIONUSER}}|{{subst:REVISIONUSER}}]]", "''<your username>''", $prev );
	}

	$url = "http://commons.wikimedia.org/w/api.php?action=parse&format=php&pst&text=" . urlencode( $prev );

	$data = array();
	$data = unserialize( file_get_contents( $url ) );

	if ( $_GET['dontrender'] == "true" ) {
		echo "<pre>" . $prev . "</pre>";
	} else {
		echo $data['parse']['text']['*']; //return data
	}


	?>

</div>
<!-- don't allow to click the links -->
<img src="transparent.gif"
	 style="position:fixed;width:100%;height:100%;left:0px;top:0px;bottom:0px;"/>
</body>
</html>
