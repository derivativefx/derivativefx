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

/*  FUNCTIONS
  INCLUDED IN EVERY PAGE
*/

function api($url) {
	sleep(1);
        $con = curl_init();
        $to = 4;
        curl_setopt($con, CURLOPT_URL, $url);
        curl_setopt($con, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($con, CURLOPT_CONNECTTIMEOUT, $to);
        curl_setopt($con, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($con,CURLOPT_USERAGENT,'derivative tool; derivativeFX on tools wmflabs;');
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
	$url = "http://toolserver.org/~daniel/WikiSense/CommonSense.php?u=en&i=" . urlencode( $image ) . "&r=on&kw=" . urlencode( $kw ) . "&p=_20&go-clean=Kategorien+finden&cl=&w=en&v=0";
//	$return = file_get_contents( $url ) or print( "<span style='color:red'>CommonSense not accessible!</span><br/>" );
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


?>
