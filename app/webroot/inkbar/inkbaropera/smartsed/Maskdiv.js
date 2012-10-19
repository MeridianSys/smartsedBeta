  var categoryPopUp ;
  var  maskedDiv;

 function OpenModelPopup()
    { 
  //Create mask
        maskedDiv = document.createElement("div");
        maskedDiv.className = "MaskedDiv";
        maskedDiv.setAttribute("id", "maskDivId")
        maskedDiv.style.display='';
        maskedDiv.style.visibility='visible';
        maskedDiv.style.top='0px';
        maskedDiv.style.left='0px';
        maskedDiv.style.position = "fixed";
        maskedDiv.style.width= document.documentElement.clientWidth + 'px';
        maskedDiv.style.height=document.documentElement.clientHeight+ 'px';
        document.body.insertBefore(maskedDiv, document.body.firstChild);
        showTagCategoryPopUp();
    }

    //to close popup
    function CloseModelPopup()
    {
       // document.getElementById ('maskDivId').style.display='none';
      //  document.getElementById ('categoryPopUp').style.display='none';
        document.body.removeChild(document.getElementById('maskDivId'));
        document.body.removeChild(document.getElementById('categoryPopUp'));

    }

//Crete popup
	function showTagCategoryPopUp(){
		categoryPopUp = document.createElement("div");
        categoryPopUp.setAttribute("id", "categoryPopUp");
        categoryPopUp.style.zIndex = "99999999";
        categoryPopUp.style.border = "2px #414141 double";
        categoryPopUp.style.backgroundColor = "buttonface";
        categoryPopUp.style.top = Math.round ((document.documentElement.clientHeight/2)+ document.documentElement.scrollTop)-100 + 'px';
        categoryPopUp.style.left = "400px";
        categoryPopUp.style.position = "fixed";
        categoryPopUp.className = "ModalPopup stickyNoteCSSClass";
        makePostRequestforCategory();
        //CategoryInnerHtml();
}

function CategoryInnerHtml(data){
        
        var catTableInnerHtml = '<div class ="stickyTitle" id="" style="-moz-box-shadow : inset 0px 0px 70px 1px buttonface;box-shadow : inset 0px 0px 70px 1px buttonface;">';
        catTableInnerHtml +='<table width="100%"><tr><td align="left" style="width: 80%; color :White; font-weight:bold";valign="top"><b>Sticky Notes</b></td>';
        catTableInnerHtml += '<td style="width: 20%; color :White; font-weight:bold;" align="right" valign="top"><img src="http://www.smartsed.com/smartsed/app/webroot/inkbar/inkbaropera/icons/Close.png" onClick="CloseModelPopup()"/></td></tr></table></div>';
        //catTableInnerHtml += '</td></tr>';
        catTableInnerHtml +='<table width="280" height="140"><tr><td colspan=2>';
        catTableInnerHtml += '<tr><td>Tag</td><td><input id="txtTag" type="text" placeholder="Tag"/></td></tr>';
        catTableInnerHtml += '<tr><td>Category</td><td>'+ data +'</td></tr>';
        catTableInnerHtml += '<tr><td align="left"><input type="checkbox" id="chbPrivate" value="private">Private</td><td align="right" ><input type="BUTTON" VALUE="save" onclick="sendDataToServer()"/></td></tr></table>';
        categoryPopUp.innerHTML = catTableInnerHtml;
        document.body.insertBefore(categoryPopUp, document.body.firstChild);
        putinCenter(document.getElementById("categoryPopUp"));
}

function addValueToCombo(value){
    var oOption = document.createElement("OPTION");
    oOption.text=value;
    document.getElementById(oOption);
}

