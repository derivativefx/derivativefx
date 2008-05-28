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
include("functions.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html style="direction: ltr;" lang="en">
  <head>
    <meta content="text/html; charset=UTF-8" http-equiv="content-type">
    <title>derivativeFX</title>
    <meta content="Luxo" name="author">
    <meta content="easy upload derivative works!" name="description">
    <script type="text/javascript">
    var skipcheck = <?php if($_GET['skipcheck'] == 'true' or $_COOKIE['skipcheck'] == 'true') { echo'true'; } else { echo'false'; } ?>;
    </script>
    <script type="text/javascript" src="js/prototype.js"></script>
    <script type="text/javascript" src="js/commands.js"></script>
    <link rel="stylesheet" type="text/css" href="style/style.css">
        
  </head>
  <body class="bodynorm" id="bodyid">
    <br />
    <img src="derivativeFX_small.png" />
    <br />
        <div id="JavascriptWarn">
      <br /><h1>Sorry,</h1> You must have a browser which support the JavaScript-standards of the <a href="http://www.w3.org/">World Wide Web Consortium</a> (W3C) to use this tool. <br />
      
      Please download a current browser like
      <a href="http://www.firefox.com">Firefox</a>, <a href="http://www.opera.com">Opera</a> or <a href="http://www.apple.com/de/safari/">Safari</a>.
      <br />
      <br />
      You can use the <a href="http://commons.wikimedia.org/wiki/Special:Upload">standard upload form</a>, don't forgot to add a notice about the authors of the original files and respect their licenses. 
      <br />
    </div>
    <script type="text/javascript">
    $('JavascriptWarn').hide();
    </script>
    <div id="firstform" style="display:none">
    <br /> Welcome to derivativeFX!
    <br /> <?php echo helpcontent('whatisthat',"What's derivativeFX?"); ?><br />
    
    <br /> Please confirm that you are logged in to Wikimedia:Commons. 
    <br />
    <iframe src="http://commons.wikimedia.org/wiki/Special:Mypage" OnLoad="$('onlyfornext').enable('loggedinnext')" name="checkuser" width="700" height="200" align="left"
        scrolling="no" marginheight="0" marginwidth="0" frameborder="1">
  </iframe>
      <br style="clear:both">If you see your userpage in this frame, you are logged in. If not, please <a href="http://commons.wikimedia.org/w/index.php?title=Special:Userlogin">log in</a>.<br />
      <input id="checkskip" name="checkskip" value="true" type="checkbox"> Skip this check next time<br />
      <br />
      <form id="onlyfornext">
    <input id="loggedinnext" disabled="disabled" value="Ok - I'm logged in." name="loggedin" type="button" onClick="skipcookie();$('firstform').hide();$('secondform').show();<?php if($_GET['image']) { echo"getname('".htmlspecialchars($_GET['image'], ENT_QUOTES)."');"; } ?>"></form></div>
    <script type="text/javascript">
    if(skipcheck == true)
    {
      window.onload = function (){
        //$('firstform').hide(); (schon versteckt)
        $('secondform').show();
        <?php if($_GET['image']) { echo"getname('".htmlspecialchars($_GET['image'], ENT_QUOTES)."');"; } ?>
      }
    }
    

      if(skipcheck != true)
      {
        $('firstform').show();
      }

    </script>
    
<div id="secondform" style="display:none">
<form enctype="multipart/form-data" method="post" action="deri2.php" name="imageselect" id="sendform">
<span id="loading" style="display:none"><table
 style="width: 10%; text-align: left; margin-left: auto; margin-right: auto;"
 border="1" cellpadding="2" cellspacing="0">
  <tbody>
    <tr>
      <td style="text-align: center; vertical-align: middle;"><img
 style="width: 32px; height: 32px;" alt=""
 src="loader.gif"><br />
Loading...<br />
Please wait<br />
      </td>
    </tr>
  </tbody>
</table>
</span>
    <!--http://commons.wikimedia.org/w/index.php?action=ajax&rs=UploadForm%3A%3AajaxGetLicensePreview&rsargs[]=GFDL-self-->
    <p align="right" id="licprev" style="display:none">preview of <span id="licinprev" class="license">the GFDL</span>:
    <iframe src="http://commons.wikimedia.org/w/index.php?action=ajax&rs=UploadForm%3A%3AajaxGetLicensePreview&rsargs[]=GFDL" id="licenceframe" width="50%" height="310" align="right"
        scrolling="auto" marginheight="0" marginwidth="0" frameborder="0">
</iframe></p>
     Original Work:
      <br />
      <input id="firstfield" size="50" name="original_1" value="Image:" onkeyup="loadlic('1',this.value)" autocomplete="off">
      <input name="origliz_1" id="origlizid_1" value="" type="hidden">
      <br />
      License of this file: <span class="license" id="lic1">please add name</span>
      <br /><img src="loader.gif" style="display:none" id="img1" />
      <div id="placeformore">

      </div>
      <br />
        <br />
      <input id="mtobut" name="morethanone" value="add more original files" type="button" OnClick="more()">
      <br />
      
      <br />
      <input value="OK - Next" disabled="disabled" type="submit" id="sendtonext"><!-- disabled="disabled" -->
    </form></div>

    <hr style="height: 2px; width: 60%;">
<div style="text-align: center;">by <a href="/%7Eluxo/">Luxo</a>
| <a href="http://commons.wikimedia.org/wiki/User_talk:Luxo">contact</a>
| <a
 href="http://meta.wikimedia.org/wiki/User:Luxo/Licenses#derivativeFX">license</a><br />
<br />
<a href="http://wiki.ts.wikimedia.org/view/Main_Page"><img
 style="border: 0px solid ; width: 88px; height: 31px;"
 alt="powered by Wikimedia Toolserver"
 title="powered by Wikimedia Toolserver"
 src="http://tools.wikimedia.de/images/wikimedia-toolserver-button.png"></a>&nbsp;</div>

  </body>
</html>
