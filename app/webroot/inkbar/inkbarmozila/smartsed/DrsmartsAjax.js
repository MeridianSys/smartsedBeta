/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

function sendDataToServer(){
    var stickyArray = new Array();
    var stickyTitleArray =  new Array();
    
    for(var i=0;i < objArrayStickyNotes.length;i++){
        var tempTextArea;
        if(document.getElementById(objArrayStickyNotes[i])!=null){
            tempTextArea = document.getElementById(objArrayStickyNotes[i]);
            stickyArray[i] = tempTextArea.value;
            tempTextArea.innerHTML = "";
            tempTextArea.appendChild(document.createTextNode(stickyArray[i]));
        }
    }

    for(i=0;i < objTitleStickyNotes.length;i++){
        var tempStickyTitle;
        if(document.getElementById(objTitleStickyNotes[i])!=null){
            tempStickyTitle = document.getElementById(objTitleStickyNotes[i]);
            tempStickyTitle.setAttribute("value", document.getElementById(objTitleStickyNotes[i]).value);
        }
    }
    
    
    var title = document.getElementsByTagName("title").length==0?"title":document.getElementsByTagName("title")[0].textContent;
    var url = document.location.href;
    
    
    var tag = '';
    var isPrivate = '';
    var categoryID = '';
    var drsmartDOM = '';
    
    var data;
    if(flagmailandsave){
        drsmartDOM = getDOM();

        generateToolbar();
        data = [
                {name: 'uid', value: u} ,
                {name: 'title', value: title},
                {name: 'url', value: url},
                {name: 'text', value: drsmartDOM.toString()} ,
                {name: 'stickyNotes[]', value : stickyArray},
                {name: 'tag', value : tag},
                {name: 'isPrivate', value : isPrivate},
                {name: 'categoryID', value : categoryID},
                {name: 'isSaveShare', value : flagmailandsave},
                {name: 'about', value : about},
                {name: 'recipients', value : recipients},
                {name: 'message', value : message}
                ];
    }else{
        if(document.getElementById("txtTag")!=null){
            tag = document.getElementById("txtTag").value;
        }else{
            tag = stickyTag;
        }
        isPrivate = document.getElementById("chbPrivate").checked?1:0;
        categoryID = document.getElementById("categoryId").value
        if(categoryID=='Select Category')
            categoryID = "";
        drsmartDOM = getDOM();

        generateToolbar();
        data = [
                {name: 'uid', value: u} ,
                {name: 'title', value: title},
                {name: 'url', value: url},
                {name: 'text', value: drsmartDOM.toString()} ,
                {name: 'stickyNotes[]', value : stickyArray},
                {name: 'tag', value : tag},
                {name: 'isPrivate', value : isPrivate},
                {name: 'categoryID', value : categoryID},
                {name: 'isSaveShare', value : flagmailandsave}
               ];
    }
    
    makePostRequest(data, "savingDivID");
    
}

function makePostRequest(data, savingDiv){
    $.ajax({
        type: 'POST',
        url: baseURL + '/smartsed/db/DBConnectionHandlerClass.php',
        data:  data,
        dataType: "json",

        beforeSend:function(){
                var savingDiv = document.createElement("div");
//                var imgSaving = document.createElement("img");
//                imgSaving.src = baseURL +'/drsmarts/icons/loader.gif';
//                imgSaving.setAttribute("alt", "Saving...");
//                imgSaving.setAttribute("id", "savingImgID");

                savingDiv.setAttribute("id", "savingDivID");
                //savingDiv.appendChild(imgSaving);
                savingDiv.style.zIndex = "9999999999";
                savingDiv.style.position = "fixed";
                savingDiv.style.left = "0px";
                savingDiv.style.top = "0px";
                //savingDiv.style.border = "2px #414141 double";
                savingDiv.style.backgroundColor = "orange";
                savingDiv.style.align = "center";
                savingDiv.innerHTML = '<center><p>Saving your Annotaion...</p></center><div align="center"><img  src="'+baseURL+'/smartsed/icons/ajax-loader.gif"/></div>';

                document.body.insertBefore(savingDiv, document.body.firstChild);
                

                putinCenter(document.getElementById("savingDivID"));
        },

        success:function(data){
            document.getElementById("savingDivID").innerHTML = "<span height='50px' width='100px'><center>Annotaion has been Saved...</center></span>";
            var t=setTimeout("removeSavingDiv()",3000);
        },
        error:function(){
            document.getElementById("savingDivID").innerHTML = "<span height='50px' width='100px'><center>Error in Saving. Please Try Again...</center></span>";
            var t=setTimeout("removeSavingDiv()",3000);
        }
    });
}

function removeSavingDiv(){
    document.body.removeChild(document.getElementById("savingDivID"));
}

function getDOM(){
    var scriptEle = document.getElementsByTagName("script");
    for(var i=0;i<scriptEle.length;i++){
        if(scriptEle[i].id=='loaderSpan' || scriptEle[i].id=='drsmartsMozilla'){
            document.body.removeChild(document.getElementById(scriptEle[i].id));
        }
    }
    document.body.removeChild(document.getElementById("toolbarContainer"));
    
    if(document.getElementById("categoryPopUp") && document.getElementById("maskDivId")){
        document.body.removeChild(document.getElementById("categoryPopUp"));
        document.body.removeChild(document.getElementById("maskDivId"));
    }

    changeSrc(document.getElementsByTagName("img"));
    changeHrefLink("href");
    changeHrefLink("a");
    changeHrefLink("link");
    changeSrc(document.getElementsByTagName("script"));
    return getCode();
}

function getCode(object, print, stripTags, nl2){

    return '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd"> <html xmlns="http://www.w3.org/1999/xhtml">' + $('HTML').html() + '</html>';
}

function changeHrefLink(tag){
    var linkArray = new Array();
    var linkHref;
    linkArray = document.getElementsByTagName(tag);
    var hostUrl = document.location.host;
    for(var i=0;i<linkArray.length;i++){
        linkHref = linkArray[i].href;
        if(linkHref.substr(0, 2) != '//'){
            linkArray[i].href = linkHref;
        }
    }
}

function changeSrc(tagArray){
    var source;
    var hostUrl = document.location.host;
    for(var i=0;i<tagArray.length;i++){
        source = tagArray[i].src;
        if(source.substr(0, 2)!='//'){
            tagArray[i].src = source;
        }
    }
}

function putinCenter(msgbox) {
  var x = (window.innerWidth / 2) - (msgbox.offsetWidth / 2);
  var y = (window.innerHeight / 2) - (msgbox.offsetHeight / 2);
  msgbox.style.top = y + "px";
  msgbox.style.left = x + "px";
}