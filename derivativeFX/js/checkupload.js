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

function upchecker()
{
  //window.document.lastform.xxx.value
  var ret = true;
  
  Begruendung = new Array();
  var n = 0;
  if(!window.document.lastform.wpUploadFile.value)
  {
    ret = false;
    Begruendung[n] = "Please attach a file.";
    n++;
    setBox("used","atfile");
  }
  
  var wpDestFile = window.document.lastform.wpDestFile.value;
  
  if(!wpDestFile.match(/(.*)\.(png|gif|jpg|jpeg|xcf|pdf|mid|sxw|sxi|sxc|sxd|ogg|svg|djvu)/gi))
  {
    ret = false;
    Begruendung[n] = "No correct file extension found in destination file. ("+wpDestFile+")";
    n++;
    setBox("used");
  }
  
  var wpUploadFile = window.document.lastform.wpUploadFile.value;
  
  var aa = wpDestFile.split(".");
  var bb = wpUploadFile.split(".");

  var el = aa.length;
  var el2 = bb.length;
  
  var ael = el - 1;
  var bel = el2 - 1;

  if(aa[ael] != bb[bel])
  {
    if(ret == true)
    {
      ret = false;
      setBox("used");
      var chk = window.confirm("Your selected file has not the same file extension ("+bb[bel]+") like the destination name ("+aa[ael]+")! Change dest. name to '"+aa[0]+"."+bb[bel]+"'?");
      if(chk == true)
      {
         window.document.lastform.wpDestFile.value = aa[0]+"."+bb[bel];
         setBox("ok");
      }
    }
  }
  
  var checkerc = wpDestFile.substr(0,6);
  if(checkerc.toLowerCase() == "image:")
  {
    if(ret == true)
    {
      var inam = wpDestFile.substr(6);
      var chk = window.confirm("Your destination filename is 'Image:Image_"+inam+"'. Do you mean 'Image:"+inam+"'?");
      if(chk == true)
      {
      window.document.lastform.wpDestFile.value = inam;
      wpDestFile = inam;
      }
    }
  }
  
  
  if(ret == true)
  {
    var addtourl = '&adanote=false';
    if(window.document.lastform.addorig.checked == true)
    {
      addtourl = '&adanote=true';
    }
    var newfile = window.document.lastform.wpDestFile.value;
    MeinFenster = window.open('addnote.php?data='+data+'&token='+checksum+addtourl+'&file='+newfile, "Uploading", "scrollbars=yes,width=350,height=400,left=100,top=200");
    MeinFenster.focus();
      
  }
  else
  {
    //Fehlermeldungen ausgeben
    var alertf = "";
    for (var i = 0; i < Begruendung.length; i++) {
      alertf = alertf+Begruendung[i]+"\n";
    }
    if(Begruendung.length > 0)
    {
      window.alert(alertf);
    }
  }
  return ret;
}



var imgcache = "";
function checkimg(image)
{
  if(image.match(/(.*)\.(png|gif|jpg|jpeg|xcf|pdf|mid|sxw|sxi|sxc|sxd|ogg|svg|djvu)/gi))
  {
    if(imgcache != image)
    {
      var checkerc = image.substr(0,6);
      if(checkerc.toLowerCase() == "image:")
      {
        var inam = image.substr(6);
        image = inam;
        $("newfilename").value = inam;
      }
      setBox("loading");
      $("existwarn").hide();
      $("dontexist").hide();
      var myAjax = new Ajax.Request(
      "checkexist.php?image="+image,
      { method: 'get', onComplete: abbr }
      );
      imgcache = image;
    }
  }
}

  function abbr(originalRequest)
  {
    if(originalRequest.responseText == "FALSE")
    {
      window.alert("Image name already exist. Please choose a different name.");
      $("existwarn").show();
      $("dontexist").hide();
      setBox("used");
    }
    else
    {
      setBox("ok");
      $("existwarn").hide();
      $("dontexist").show();
    }
  }
  
  function setBox(param,id){
  
  if(!id)
  {
  id = "newfilename";
  }
  
var box = $(id);	

switch(param){
	case "loading":
		box.setAttribute("style","border-color:#1D125C;");
		break;
	case "ok":
		box.setAttribute("style","border-color:green;");
		break;
	case "used":
		box.setAttribute("style","border-color:red;");
		break;
}
}
