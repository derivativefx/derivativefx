<?php
/*
Copyright Luxo 2008
          derivativeFX Maintainer - 2016

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

/*
FUNCTIONS INCLUDED IN EVERY PAGE
*/

error_reporting(0);

function i18n() {
	global $language, $lang;
	$fla = $_GET['lang'];
	if(preg_match("/^[a-z]{1,4}(-[a-z]{1,4}|)+$/", $fla)) {
	     $language = htmlspecialchars($fla);
	     $lang = htmlspecialchars($fla);
	} else {
	     $language = "en";
	     $lang = "en";
	}
}
	
function api($url) {
	$ac = ( "/data/project/derivative/apicalls.txt" );
	$hii = file( $ac );
	$hii[0] ++;
	$fp = fopen( $ac , "w" );
	fputs( $fp , "$hii[0]" );
	fclose( $fp );
	
	usleep(200000);
	
        $con = curl_init();
        $to = 2;
        curl_setopt($con, CURLOPT_URL, $url);
        curl_setopt($con, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($con, CURLOPT_CONNECTTIMEOUT, $to);
        curl_setopt($con, CURLOPT_USERAGENT,'derivativeFX 2.0; labs2api; derivativeFX on Tool Labs;');
        $data = curl_exec($con);
        curl_close($con);
        return $data;
}

function helpcontent( $theme, $text = "?" ) {
	global $language;


	$url = "help/helpdesk.php?theme=" . $theme . "&lang=" . $language;

	$html = "<sup><a href='$url' title='help' onclick=\"helpwindow(this.href); return false\">$text</a></sup>";
	return $html;

}


function catscan( $image, $kw = "" ) {
	$url = "https://tools.wmflabs.org/wikisense/CommonSense.php?u=en&i=" . urlencode( $image ) . "&r=on&kw=" . urlencode( $kw ) . "&p=_20&go-clean=Kategorien+finden&cl=&w=en&v=0";
//	$return = api( $url ) or print( "<span style='color:red'>CommonSense not accessible!</span><br/>" );
	$return = ""; // deprecated, no replacement aviable
	$start = strpos( $return, "#CATEGORIES" ) or print( "<span style='color:red'>CommonSense not accessible!</span><br/>" );
	$end = strpos( $return, "#GALLERIES" );

	if ( $start AND $end ) {
		$return = substr( $return, $start, $end - $start );
		$firstabsatz = strpos( $return, "\n" );

		$return = trim( substr( $return, $firstabsatz ) );
		$category = array();
		$category = explode( "\n", $return );

		if ( $category == false ) {
			$category = array();
		}

		//print_r($category);

		$categorys = array();

		foreach ( $category as $cat ) {
			$cat = str_replace( "_", " ", $cat );
			$categorys[$cat] = $cat;
		}
		return $categorys;

	} else {
		return false;
	}
}

function fxfooter() {
	echo"<div style=\"text-align: center; font-size: x-small;\">Tool originally written by <a href=\"//commons.wikimedia.org/wiki/User:Luxo\">Luxo</a> | <a href=\"https://commons.wikimedia.org/wiki/Commons:DerivativeFX\">about</a> | <a href=\"//github.com/derivativefx/derivativefx\">source &amp; license</a><br></div>";
}

$stash = 'stash.php';

if (file_exists($stash)) {
    include $stash;
}

?>
