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


$b = $_GET['b'];


if(!$b OR !preg_match("=^[0-9]+$=i",$b)){
$b = 30;
}

$v = $b - 30;

?>
<html>
<head>
<title>derivativeFX - Logfile</title>
</head>
<body style="direction: ltr;">
<img style="border: 0px solid ;" alt="" src="/~luxo/derivativeFX/derivativeFX_small.png">
<h2>Logfile</h2>
Legend:<br>
<span style="background-color:green">Green:</span> everything done<br />
<span style="background-color:orange">Orange:</span> the bot will make this in the next time<br />
<span style="background-color:red">Red:</span> error (e.g. derivative work file not found)<br/>
<center>Show <?php echo $v." to ".$b; ?></center><br />
<table style="text-align: left; width: 100%;" border="1"
 cellpadding="3" cellspacing="0">
  <tbody>
    <tr>
      <td style="width: 20%;">Original file</td>
      <td style="width: 20%;">Derivative file</td>
      <td style="width: 20%;">Status</td>
      <td style="width: 20%;">Time</td>
      <td style="width: 20%;">Done by bot at:</td>
    </tr>
    
    <?php
    //Mit MySQL verbinden
    include("/home/luxo/public_html/contributions/logindata.php");//pw & bn einbinden
    $dblink = @mysql_connect($databankname, $userloginname, $databasepw);//Allgemein (TS-Database)
    mysql_select_db("u_luxo", $dblink);
    
    //Abfragen
       $resu1 = mysql_query( "SELECT * FROM derivativefx ORDER BY time DESC LIMIT ".mysql_real_escape_string($v).", ".mysql_real_escape_string($b), $dblink) or die("error");

    while ($a_row2 = mysql_fetch_row($resu1)) {	
	
	$file       = $a_row2["0"]; 
  $derivative = $a_row2["1"]; 
  $status     = $a_row2["2"];
  $time       = $a_row2["3"]; 
  $donetime   = $a_row2["4"]; 
  
  if($status == "done" or $status == "nobot")
  {
  $color = "green";
  }
  else if($status == "open")
  {
  $color = "orange";
  }
  else if($status == "noexist")
  {
  $color = "red";
  }
  else
  {
  $color = "red;color:yellow";
  }
  
  echo"<tr style='background-color: $color'>";
  echo"<td><a href=\"http://commons.wikimedia.org/wiki/".htmlspecialchars($file)."\">".htmlspecialchars($file)."</a></td>";
  echo"<td><a href=\"http://commons.wikimedia.org/wiki/Image:".htmlspecialchars($derivative)."\">Image:".htmlspecialchars($derivative)."</a></td>";
  echo"<td>".htmlspecialchars($status)."</td>";
  echo"<td>".htmlspecialchars(date("H:i, d. F Y",$time))."</td>";
  if(preg_match("=^[0-9]+$=i",$donetime))
  {
    echo"<td>".htmlspecialchars(date("H:i, d. F Y",$donetime))."</td>";
  }
  else
  {
    if($status == "nobot")
    {
      echo"<td>-</td>";
    }
    else
    {
      echo"<td>(not done)</td>";
    } 
  }
  echo"</tr>\n";
      }
    
    ?>

  </tbody>
</table><br />
    <center><a href="?b=<?php echo $b - 30;?>" title="back">←</a> | <a href="?b=30" title="Home">↑</a> | <a href="?b=<?php echo $b + 30;?>" title="Next">→</a><br /><br />by Luxo</center>
<br>
</body>
</html>

