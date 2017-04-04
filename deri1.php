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
include( "functions.php" );
i18n();
include( "language.php" );
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
        <link href="//tools-static.wmflabs.org/cdnjs/ajax/libs/twitter-bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet">
        <script type="text/javascript" src="js/prototype.js"></script>
        <script type="text/javascript" src="js/commands.js.php?lang=<?php echo $language; ?>"></script>
        <link rel="stylesheet" type="text/css" href="style/style.css">

</head>
<body class="bodynorm" id="bodyid">
<div class="container">
<br/>
<img src="derivativeFX_small.png"/>
<br/>
<?php
//SPRACHEN
$cotn = 0;
foreach ( $lng as $shortcut => $lgarry ) {
        if ( $shortcut != "x" ) {
                if ( $cotn != 0 ) {
                        echo "| ";
                }
                echo "<a href='?lang=$shortcut'>" . $lgarry['name'] . "</a> ";
                $cotn = 1;
        }
}

?><br><br>

<div id="JavascriptWarn">
        <br/>

        <h1>Sorry,</h1> You must have a browser which support the JavaScript-standards of the <a
                href="https://www.w3.org/">World Wide Web Consortium</a> (W3C) to use this tool. <br/>

        Please <b>turn on your Javascript</b> or, if your browser don't support this, download a current
        browser like
        <a href="https://www.firefox.com">Firefox</a>, <a href="https://www.opera.com">Opera</a> or <a
                href="https://www.apple.com/de/safari/">Safari</a>.
        <br/>
        <br/>
        You can use the <a href="https://commons.wikimedia.org/wiki/Special:Upload">standard upload
                form</a>, don't forgot to add a notice about the authors of the original files and respect
        their licenses.
        <br/>
</div>
<script type="text/javascript">
        $( 'JavascriptWarn' ).hide();
</script>
<div id="firstform" style="display:none">
        <br/> <?php echo $lng['x']['welc']; ?><br/>
        <br/> <?php echo helpcontent( 'whatisthat', $lng['x']['what'] ); ?><br/>

        <br/> <?php echo $lng['x']['conf']; ?>
        <br/>
        <iframe src="https://commons.wikimedia.org/wiki/Special:Mypage"
                        OnLoad="$('onlyfornext').enable('loggedinnext')" name="checkuser" width="700"
                        height="200" align="left"
                        scrolling="no" marginheight="0" marginwidth="0" frameborder="1">
        </iframe>
        <img src="transparent.gif" style="position:absolute;left:9px;"/>
        <br style="clear:both"><?php echo $lng['x']['look']; ?> <a href="https://commons.wikimedia.org/w/index.php?title=Special:Userlogin"><?php echo $lng['x']['loin']; ?></a>.<br/>
        <input id="checkskip" name="checkskip" value="true"
                   type="checkbox"> <?php echo $lng['x']['skip']; ?><br/>
        <br/>

        <form id="onlyfornext">
                <input id="loggedinnext" disabled="disabled" value="<?php echo $lng['x']['okin']; ?>"
                           name="loggedin" type="button" class="btn btn-success"
                           onClick="skipcookie();$('firstform').hide();$('secondform').show();<?php if ( $_GET['image'] ) {
                                   echo "getname('" . htmlspecialchars( $_GET['image'], ENT_QUOTES ) . "');";
                           } ?>"></form>
</div>
<script type="text/javascript">
        if ( skipcheck == true ) {
                window.onload = function() {
                        //$('firstform').hide(); (schon versteckt)
                        $( 'secondform' ).show();
                        <?php if($_GET['image']) { echo"getname('".htmlspecialchars($_GET['image'], ENT_QUOTES)."');"; } ?>
                }
        }


        if ( skipcheck != true ) {
                $( 'firstform' ).show();
        }

</script>

<div id="secondform" style="display:none">
        <form enctype="multipart/form-data" method="post"
                  action="deri2.php?lang=<?php echo $language; ?>" name="imageselect" id="sendform">
<span id="loading" style="display:none"><table
                style="width: 10%; text-align: left; margin-left: auto; margin-right: auto;"
                border="1" cellpadding="2" cellspacing="0">
                <tbody>
                <tr>
                        <td style="text-align: center; vertical-align: middle;"><img
                                        style="width: 32px; height: 32px;" alt=""
                                        src="loader.gif"><br/>
                                <?php echo $lng['x']['load']; ?>...<br/>
                                <?php echo $lng['x']['wait']; ?><br/>
                                <span style="font-size:x-small;" id="loadtext">GET...</span><br/>
                        </td>
                </tr>
                </tbody>
        </table>
</span>
                <!--https://commons.wikimedia.org/w/index.php?action=ajax&rs=UploadForm%3A%3AajaxGetLicensePreview&rsargs[]=GFDL-self-->
                <!--<p align="right" id="licprev" style="display:none"><?php echo $lng['x']['prov']; ?> <span id="licinprev" class="license">the GFDL</span>:
    <iframe src="https://commons.wikimedia.org/w/index.php?action=ajax&rs=UploadForm%3A%3AajaxGetLicensePreview&rsargs[]=GFDL" id="licenceframe" width="50%" height="310" align="right"
        scrolling="auto" marginheight="0" marginwidth="0" frameborder="0">
</iframe></p>-->
                <?php echo $lng['x']['orwo']; ?>:
                <br/>
                <input id="firstfield" size="50" name="original_1" value="File:" onkeyup="loadlic('1',this.value)" onchange="loadlic('1',this.value)" autocomplete="off">
                <input name="origliz_1" id="origlizid_1" value="" type="hidden">
                <br/>
                <?php echo $lng['x']['lotf']; ?>: <span class="license" id="lic1"><?php echo $lng['x']['pan']; ?></span>
                <br/><img src="loader.gif" style="display:none" id="img1"/>

                <div id="placeformore">

                </div>
                <br/>
                <input id="mtobut" name="morethanone" value="<?php echo $lng['x']['admo']; ?>" type="button"  class="btn btn-primary"
                           OnClick="more()">
                <br/>

                <br/>
                <input value="<?php echo $lng['x']['next']; ?>" disabled="disabled" type="submit"  class="btn btn-success"
                           id="sendtonext"><!-- disabled="disabled" -->
        </form>
</div>

<hr style="height: 2px; width: 60%;">
<?php fxfooter(); ?>
</body>
</html>
