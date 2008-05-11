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
$theme = urlencode($_GET['theme']);

$file = @file_get_contents($theme.".txt");

if($file)
{
$output = $file;

$output = "<h2>".str_replace("\$\$\$", "</h2>", $output);
}
else
{
$output = "<h2>Sorry,</h2>\nCan't find help to this point.<br>";
}
/*<h2>title</h2>

Text<br>*/
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html style="direction: ltr;" lang="en">
<head>

  <meta content="text/html; charset=ISO-8859-1" http-equiv="content-type">
  <title>derivativeFX - Helpdesk</title>


  <meta content="Luxo" name="author">

  <link rel="stylesheet" href="http://tools.wikimedia.de/%7Eluxo/derivativeFX/style/style.css" type="text/css">

  <meta content="Helpdesk" name="description">
</head>
<body style="direction: ltr;" class="bodynorm">

<img style="width: 279px; height: 134px;" alt="derivativeFX" src="http://tools.wikimedia.de/%7Eluxo/derivativeFX/derivativeFX_small.png"><br>

<div style="margin-left: 80px;"><img style="width: 150px; height: 34px;" alt="helpdesk" src="help_small.png"></div><br>
<?php echo nl2br($output); ?>

<br>
<br>
<input name="close" value="Close" type="button" OnClick="self.close();">
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
