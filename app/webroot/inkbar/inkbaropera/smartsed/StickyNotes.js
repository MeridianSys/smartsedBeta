/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.   document.body.removeChild(document.getElementById(\''+ guidTemp +'\'))
 */
var objArrayStickyNotes;
var objTitleStickyNotes;
var mapRemoveNotes;
var stickyNotesZIndex = 50000;
var stickyTag;
var mapMinimize;
var categoryHTML;

if(mapMinimize == null){
    mapMinimize = new HashMap();
}

if(objArrayStickyNotes == null){
    objArrayStickyNotes = new Array();
}

if(objTitleStickyNotes == null){
     objTitleStickyNotes = new Array()
}

if(mapRemoveNotes == null){
    mapRemoveNotes = new HashMap();
}

function openStickyNotes(selection, callingObj){
    if(selection.toString().length > 0){
        makePostRequestforCategoryStickyNotes(selection, callingObj);
        
    }
}

function createStickyNotes(selection, callingObj){
    
    var divIDStickyNotes = "stickyNotes" + guidGenerator();
    var stickyNotes = document.createElement("span");
    var guidTemp = guidGenerator();
    var guidTextArea = guidGenerator();
    var guidTitleTextField = guidGenerator();
    try{
        stickyNotes.setAttribute("id", guidTemp);
        stickyNotes.className = "stickyNoteCSSClass";
        stickyNotes.style.zIndex = stickyNotesZIndex++;
        stickyNotes.style.border = "2px #414141 double";
        var tempLeft = findPosX(selection.anchorNode.parentNode) - 215 > 0 ? findPosX(selection.anchorNode.parentNode) - 215 : 0;
        stickyNotes.style.left = tempLeft + "px";
        stickyNotes.style.top = findPosY(selection.anchorNode.parentNode) + "px";
        stickyNotes.style.position = "absolute";
        stickyNotes.style.backgroundColor = callingObj.style.backgroundColor;
        //stickyNotes.style.boxShadow = "5px 5px 5px #888888";

        
        //stickyNoteInnerHTML += '<tr><td>';
        var stickyNoteInnerHTML = '<div class ="stickyTitle" onmousedown="dragDropElements.startDragMouse(event)" id="'+ divIDStickyNotes +'" style=" box-shadow : inset 0px 0px 70px 1px ' +callingObj.style.backgroundColor+'">';
        stickyNoteInnerHTML += '<table width="100%"><tr><td align="left"><font color="#FFFFFF"><b>Sticky Notes</b></font></td>';
        stickyNoteInnerHTML += '<td align="right"><img src="'+ baseURL +'/smartsed/icons/minimize.png" onClick="minimizeStickyNotes(this)"><img src="'+ baseURL+'/smartsed/icons/Close.png" onClick="onCloseStickyNotes(this)"/>';
        stickyNoteInnerHTML += '</td></tr></table></div>';
        //stickyNoteInnerHTML += '</td></tr>';
        stickyNoteInnerHTML += '<table>';
        stickyNoteInnerHTML += '<tr><td><input style="width:98%" id="'+ guidTitleTextField +'"type="text" placeholder="Title"/></td></tr>';
   
        stickyNoteInnerHTML += '<tr><td>';
        stickyNoteInnerHTML += '<textarea class="stickyNoteTextAreaCss" id="'+guidTextArea+'" rows="8"  cols="20" style="background-color:'+callingObj.style.backgroundColor+'" ></textarea>';
        stickyNoteInnerHTML += '</tr></td>';
        stickyNoteInnerHTML += '<tr>';
        stickyNoteInnerHTML += '<td><font size="2">Category :</font>&nbsp'+ categoryHTML +'</td>';
        stickyNoteInnerHTML += '</tr>';
        stickyNoteInnerHTML += '<tr style="height:25px;">';
        stickyNoteInnerHTML += '<td style="height:28px;" align="left"><input type="checkbox" id="chbPrivate" value="private"/><font size="2">Private</font><div title="'+guidTitleTextField+'" align="center" style="float:right;" onmouseover="onMouseOverSaveSticky(this)" onmouseout="onMouseOutSaveSticky(this)" onmousedown="onMouseDownSaveSticky(this)" onmouseup="onMouseUpSaveSticky(this)" onclick="onClickStickySave(this)" ><img src="'+ baseURL +'/smartsed/icons/save.png" /></div></td>';
        stickyNoteInnerHTML += '</tr>';
        stickyNoteInnerHTML += '</table>';

        stickyNotes.innerHTML = stickyNoteInnerHTML;
        document.body.insertBefore(stickyNotes, document.body.firstChild);
        //dragDropElements.initElement(document.getElementById(divIDStickyNotes));

        highlightBackground(selection, callingObj, guidTemp, stickyNotes);

        objArrayStickyNotes.push(guidTextArea);
        objTitleStickyNotes.push(guidTitleTextField);

        mapMinimize.put(guidTemp, selection.focusNode.parentNode);
        //selection.focusNode.parentNode.innerHTML += '<span><img src="' + baseURL +'/drsmarts-toolbar/drsmartsOpera/icons/note.png"/><span>'
        selection.removeAllRanges();
    }catch(e){
        alert('The operation could not be completed successfully.');
        if(document.getElementById( guidTemp )!=null)
            document.body.removeChild(document.getElementById( guidTemp ));
        document.body.removeChild(document.getElementById( "toolbarContainer" ));
        createLoaderSpanElement();
        
    }

}

function onMouseOverSaveSticky(objThis){
    objThis.style.border = "2px buttonface outset";
}

function onMouseOutSaveSticky(objThis){
    objThis.style.border = "0px buttonface solid";
}

function onMouseDownSaveSticky(objThis){
    objThis.style.border = "2px buttonhighlight inset";
}

function onMouseUpSaveSticky(objThis){
    objThis.style.border = "2px buttonhighlight outset";
}

function onClickStickySave(objThis){
    stickyTag = document.getElementById(objThis.title).value;
    if(stickyTag != "")
        sendDataToServer();
    else
        alert("Please Enter Tags Before Saving")

}

function closeStickyNotes(){
    var evt = window.event;
    document.body.removeChild(document.getElementById(e.currentTarget.id));
}

function highlightBackground(selection, callingObj, guidTemp, stickyNotes){
    
    var sheet = document.createElement("style");
    sheet.type = 'text/css';
    //sheet.setAttribute("id", "myStyle"+guid);
    var guidSelectionStickyBackground = guidGenerator();
    stickyNotes.title = guidSelectionStickyBackground;
    sheet.innerHTML = "."+ guidSelectionStickyBackground +" { background-color :"+ callingObj.style.backgroundColor +";}";
    document.getElementsByTagName('head')[0].appendChild(sheet);

    rangy.init();
    var highlightApplier = rangy.createCssClassApplier(guidSelectionStickyBackground, true);
    highlightApplier.applyToSelection();
    mapRemoveNotes.put(guidTemp, guidSelectionStickyBackground);
    
}

function onCloseStickyNotes(objThis){
    var parent = objThis;
    while(parent.localName.toString().toLowerCase() !='span'){
        parent = parent.parentNode;
    }
    var cssClassName = parent.title;//mapRemoveNotes.get(parent.id);
//    rangy.deserializeSelection(cssApplierInfo.serializer);
//    cssApplierInfo.cssApplier.undoToSelection();
//    window.getSelection().removeAllRanges();

    var eleSpan = new Array();
    eleSpan = document.getElementsByTagName('span');
    for(var i=0; i<eleSpan.length;i++){
        if(eleSpan[i].className.toString().search(cssClassName)>=0){
            eleSpan[i].className = eleSpan[i].className.toString().replace(cssClassName, "");
        }
    }

    document.body.removeChild(parent);
}

function minimizeStickyNotes(objThis){
    var parent = objThis;
    while(parent.localName.toString().toLowerCase()!='span'){
        parent = parent.parentNode;
    }

    var focusNodeStickyNodes;// = mapMinimize.get(parent.id);
    var spanElements = document.getElementsByTagName('span');
    for(var i=0;i< spanElements.length;i++){
        if(spanElements[i].className == parent.title)
            focusNodeStickyNodes = spanElements[i];
    }
    parent.style.visibility = "hidden";

//    var minImgSpan = document.createElement("span");
//    minImgSpan.id = "img" + parent.id;
//    minImgSpan.setAttribute("onclick", "showStickyNotes(this)");
//    minImgSpan.innerHTML = '<img src="' + baseURL +'/smartsed/icons/min.gif"/>';
//    document.body.insertBefore(minImgSpan, focusNodeStickyNodes.nextSibling);

    focusNodeStickyNodes.innerHTML += '<span id="img'+ parent.id +'" onClick = "showStickyNotes(this)"><img src="' + baseURL +'/smartsed/icons/min.gif"/><span>'
    
}

function showStickyNotes(objThis){
    document.getElementById(objThis.id.toString().substr(3, objThis.id.length)).style.visibility = "visible";
    var objThisParent = objThis.parentNode;
    objThisParent.removeChild(objThis);
}

function makePostRequestforCategoryStickyNotes(selection, callingObj){
    $.ajax({
      dataType: 'jsonp',
      jsonp: 'jsonp_callback',
      url: baseURL + '/users/CategoryLoader.php',
      async: false,
      success: function (data) {
            categoryHTML = data;
            createStickyNotes(selection, callingObj);
      },
      error:function(xhr, textStatus, errorThrown){
           alert(textStatus);
      }
    });
}