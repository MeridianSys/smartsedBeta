
/**
  * class_Drsmarts.js
  *
  * This class is the handler of the user interface for the application
  *
  * @author: Nikesh kumar
  * @version: 12/07/2011
  */

 /**
   * Generates the toolbar.
   */

  var guid;
  var highlighClicedFirstTime = true;
  var privateFlag = false;
  var openedFlag = false;
  var sc;
  
  try{
      rangy.init();
      rangy.createCssClassApplier("guid", true);
      generateToolbar();
  } catch(e){
      addScript();
  }
  
  
  function generateToolbar()
  {
        
        loadPickerJS();
        if(document.getElementById("toolbar")!=null){
            return;
        }
        
        //process(xmlHttp, userid, 'showUserStickies', '', url, '', '', '', '', '');
        sc = document.createElement("span");
        sc.id = "toolbarContainer";
        sc.style.position = "fixed";
        sc.style.left = "10px";
        sc.style.top = "20px";
        sc.style.zIndex = "9999999";
        document.body.insertBefore(sc, document.body.firstChild);
          	 
	  var toolbarFrame = document.createElement("div");
	  toolbarFrame.setAttribute("id", "toolbar");
          toolbarFrame.setAttribute("align", "center");
	  toolbarFrame.setAttribute("class","toolbar");
          toolbarFrame.style.backgroundColor = "buttonface";
	  toolbarFrame.style.fontFamily = "MS San Serif";
	  toolbarFrame.style.fontSize = "14px";
	  toolbarFrame.style.paddingTop = "0px";
	  toolbarFrame.style.paddingBottom = "0px";
	  toolbarFrame.style.paddingLeft = "0px";
	  toolbarFrame.style.border = "2px #414141 double";
          
	  var b1 = document.createElement("span");
          //b1.appendChild(document.createTextNode("Add sticky"));
          b1.innerHTML = "<FONT>Add sticky</FONT>";
          b1.style.paddingRight = "10px";
	  b1.style.paddingLeft = "10px";
          b1.style.backgroundColor = "yellow";
	  b1.onclick = function(){handle_functions("sticky", b1);return false;};

          var imgPrivateSpan = document.createElement("span");
          var imgPrivate = document.createElement("img");
          imgPrivate.src = baseURL + "/smartsed/icons/private.png";
          imgPrivateSpan.onclick = function(){imgPrivateClicked(b1);return false;};
          imgPrivateSpan.appendChild(imgPrivate);

          var imgStickyColorSpan = document.createElement("span");
          imgStickyColorSpan.style.paddingRight = "10px";
	  imgStickyColorSpan.style.paddingLeft = "10px";
          var imgStickyColor = document.createElement("img");
          imgStickyColor.src = baseURL + "/smartsed/icons/color.png";
          imgStickyColorSpan.onclick = function(){imgStickyColorMouseOver(b1);return false;};
          imgStickyColorSpan.appendChild(imgStickyColor);

          //heighlight tool
	  
          var b2 = document.createElement("span");
          var imgHighlight = document.createElement("img");
          imgHighlight.src = baseURL + "/smartsed/icons/highlightIcon1.png";
          imgHighlight.style.backgroundColor = "yellow";
          imgHighlight.style.height = "15px";
	  b2.appendChild(imgHighlight);
          
	  b2.setAttribute("style", "width: 50px;" + "padding-right: 10px;" + "padding-left:10px;" + "padding-bottom:0px");
          b2.setAttribute("id", "highlight");
          b2.onclick = function(){handle_functions("highlight",imgHighlight);return false;};

          var imgColor = document.createElement("img");
          imgColor.src = baseURL + "/smartsed/icons/color.png";
          var imgColorSpan = document.createElement("span");
          imgColorSpan.style.paddingRight = "10px";
	  imgColorSpan.style.paddingLeft = "10px";
          imgColorSpan.appendChild(imgColor);
          imgColorSpan.onclick = function(){handleColorChosser(imgHighlight);return false;};
	  
	  var b4 = document.createElement("span");
	  b4.appendChild(document.createTextNode("Show all annotations"));
	  b4.setAttribute("style", "width: 50px;" + "padding-right: 0px;" + "padding-left:5px;");
	  b4.style.fontSize = "14px";
	  b4.style.fontFamily = "MS San Serif";
	  b4.style.fontWeight = "normal";
	  b4.style.fontStyle = "normal";
	  b4.onclick = function(){handle_functions("ShowAllAnnotations", b4);return false;};

	  var b5 = document.createElement("span");
	  b5.appendChild(document.createTextNode("My account"));
	  b5.setAttribute("style", "width: 50px;" + "padding-right: 5px;" + "padding-left:5px;");
	  b5.style.fontSize = "14px";
	  b5.style.fontFamily = "MS San Serif";
	  b5.style.fontWeight = "normal";
	  b5.style.fontStyle = "normal";
	  b5.onclick = function(){handle_functions("my_pages", b5);return false;};
	
	  var b7 = document.createElement("span");
          b7.style.paddingLeft = "10px";
          b7.style.paddingTop = "2px";
          var imgLogo = document.createElement("img");
          imgLogo.src = baseURL + "/smartsed/icons/smartedlogo.png";
          b7.appendChild(imgLogo);
          b7.innerHTML += "<font size='1'>&nbsp;<b>V:1.0.0<\/B><\/font>";

          var saveSpan = document.createElement("span");
          var imgSave = document.createElement("img");
          imgSave.src = baseURL + "/smartsed/icons/save.png";
          saveSpan.style.paddingRight = "5px";
	  saveSpan.style.paddingLeft = "5px";
          saveSpan.appendChild(imgSave);
          saveSpan.onclick = function(){save();return false;};

          var b8 = document.createElement("span");
          var imgClose = document.createElement("img");
          imgClose.src = baseURL + "/smartsed/icons/Close.png";
          b8.style.paddingRight = "5px";
	  b8.style.paddingLeft = "5px";
          b8.appendChild(imgClose);
          b8.onclick = function(){handle_functions("close", b8);return false;};

          var saveMail = document.createElement("span");
          var imgSaveMail = document.createElement("img");
          imgSaveMail.src = baseURL + "/smartsed/icons/email_01.png";
          saveMail.style.paddingRight = "5px";
	  saveMail.style.paddingLeft = "5px";
          saveMail.appendChild(imgSaveMail);
          saveMail.onclick = function(){handle_functions("savemail", saveMail);return false;};

          var dotSpan1 = document.createElement("span");
          dotSpan1.setAttribute("id", "dotSpan1");
          dotSpan1.className = "dottedSpan";
          dotSpan1.innerHTML = "&nbsp;"

          var dotSpan2 = document.createElement("span");
          dotSpan2.setAttribute("id", "dotSpan2");
          dotSpan2.className = "dottedSpan";

          var dotSpan3 = document.createElement("span");
          dotSpan3.setAttribute("id", "dotSpan3");
          dotSpan3.className = "dottedSpan";

          var dotSpan4 = document.createElement("span");
          dotSpan4.setAttribute("id", "dotSpan4");
          dotSpan4.className = "dottedSpan";

          var dotSpan5 = document.createElement("span");
          dotSpan5.setAttribute("id", "dotSpan5");
          dotSpan5.className = "dottedSpan";

          var dotSpan6 = document.createElement("span");
          dotSpan6.setAttribute("id", "dotSpan6");
          dotSpan6.className = "dottedSpan";

          var dotSpan7 = document.createElement("span");
          dotSpan7.setAttribute("id", "dotSpan7");
          dotSpan7.className = "dottedSpan";

          var dotSpan8 = document.createElement("span");
          dotSpan8.setAttribute("id", "dotSpan7");
          dotSpan8.className = "dottedSpan";

          toolbarFrame.appendChild(b7);
          toolbarFrame.appendChild(dotSpan1);
	  toolbarFrame.appendChild(b1);
          toolbarFrame.appendChild(imgStickyColorSpan);
          toolbarFrame.appendChild(dotSpan2);
	  toolbarFrame.appendChild(b2);
          toolbarFrame.appendChild(imgColorSpan);
          toolbarFrame.appendChild(dotSpan3);
          toolbarFrame.appendChild(getBoldSpan());
          toolbarFrame.appendChild(getItalicsSpan());
          toolbarFrame.appendChild(getUnderlineSpan());
          toolbarFrame.appendChild(dotSpan4);
	  toolbarFrame.appendChild(b4);
          toolbarFrame.appendChild(dotSpan5);
          toolbarFrame.appendChild(saveSpan);
          toolbarFrame.appendChild(dotSpan7);
          toolbarFrame.appendChild(saveMail);
          toolbarFrame.appendChild(dotSpan8);
          toolbarFrame.appendChild(b8);
          
	  //attach this tool bar to the web page
	  sc.appendChild(toolbarFrame);
          dragDrop.initElement(sc);
          
          var spanList = document.getElementById("toolbar").childNodes;
        for (var i=1; i<spanList.length; i++){
            
            if(spanList[i].id!="dotSpan1" && spanList[i].id!="dotSpan2" && spanList[i].id!="dotSpan3" && spanList[i].id!="dotSpan4" && spanList[i].id!="dotSpan5" && spanList[i].id!="dotSpan6"){
                spanList[i].style.border = "0px buttonface solid";
                spanList[i].style.paddingTop = "0px";
                spanList[i].style.textAlign = "center";
                spanList[i].style.cursor = "default";
                spanList[i].onselectstart = function(){return false;}
                spanList[i].onmouseover = function(){
                                            this.style.border = "2px buttonface outset";
                                         }
                spanList[i].onmouseout = function(){
                                            this.style.border = "0px buttonface solid";
                                        }
                spanList[i].onmousedown = function(){
                                            this.style.border = "2px buttonhighlight inset";
                                        }
                spanList[i].onmouseup = function(){
                                        this.style.border = "2px buttonhighlight outset";
                                    }
            }
       }
      if(document.getElementById("loaderSpanElement")!=null)
        document.body.removeChild(document.getElementById("loaderSpanElement"));

      return true;
}

function getBoldSpan(){
    var boldSpan = document.createElement("span");
    boldSpan.id = "boldSpan";
    boldSpan.innerHTML = '<img src="' + baseURL + '/smartsed/icons/bold.png"/>';
    boldSpan.style.paddingLeft = "10px";
    boldSpan.style.paddingRight = "10px";
    boldSpan.onclick = function(){changeSelectedTextStyle("{font-weight : bold;}");return false;};
    return boldSpan;
}

function changeSelectedTextStyle(textStyle){
    var sheet = document.createElement("style");
    sheet.type = 'text/css';
    var tempTextStyleGuid = guidGenerator();
    sheet.innerHTML = "."+ tempTextStyleGuid + textStyle;
    document.getElementsByTagName('head')[0].appendChild(sheet);

    rangy.init();
    var highlightApplier = rangy.createCssClassApplier(tempTextStyleGuid, true);
    highlightApplier.applyToSelection();
    window.getSelection().removeAllRanges();
}

function getItalicsSpan(){
    var italicSpan = document.createElement("span");
    italicSpan.id = "italicSpan";
    italicSpan.style.paddingLeft = "10px";
    italicSpan.style.paddingRight = "10px";
    italicSpan.innerHTML = '<img src="' + baseURL + '/smartsed/icons/italics.png"/>';
    italicSpan.onclick = function(){changeSelectedTextStyle("{font-style : italic;}");return false;};
    return italicSpan;
}

function getUnderlineSpan(){
    var underlineSpan = document.createElement("span");
    underlineSpan.id = "underlineSpan";
    underlineSpan.style.paddingLeft = "10px";
    underlineSpan.style.paddingRight = "10px";
   // underlineSpan.style.backgroundColor = "orange";
    underlineSpan.innerHTML = '<img src="' + baseURL + '/smartsed/icons/underline.png"/>';
    underlineSpan.onclick = function(){changeSelectedTextStyle("{text-decoration:underline}");return false;};
    return underlineSpan;
}

function save(){
    OpenModelPopup();
    //showTagCategoryPopUp();
    //sendDataToServer();
}

function imgStickyColorMouseOver(objSticky){
    openStickyColorPicker(objSticky);
}

function imgPrivateClicked(obj){
    openAccessSpecifire(obj)
}

//load picker.js at run time
function loadPickerJS(){
    var pickerJS =  document.createElement("script");
    pickerJS.src = baseURL + "/smartsed/picker.js";
    pickerJS.type="text/javascript";
    document.getElementsByTagName('head')[0].appendChild(pickerJS);
}

function handleColorChosser(highlightButton){
    var guidTemp = guidGenerator();
    openPicker(highlightButton, guidTemp);
}


function guidGenerator() {
    var S4 = function() {
       return (((1+Math.random())*0x10000)|0).toString(16).substring(1);
    };
    return ("c"+S4()+S4()+S4());
}

/**
  * Creates images.
  */
function create_img( url )
{ 
  var img = document.createElement( "img" );
  img.src = url;
  img.style.border = "0px";
  img.style.padding = "0px";

  return img;
}



/**
  * The MAIN function of the whole application. This function is the handler
  * for the toolbar and sends out function calls to complete tasks.
  */
var d = new Date()
var index = d.toUTCString();
//var newSpanId = userid + "spanId" + index + url; //the spanId of the <span>

function handle_functions(f, callingObj){
    
    var selection;
    var range;
    var targ;
    
    //if some text characters are selected
    try{
        if(window.getSelection()){
            selection = window.getSelection(); //a Selection object

            if(selection.toString().length !=0 ){
                targ = selection.anchorNode;
                if(targ.nodeType != 1){
                    targ =  selection.anchorNode.parentNode;
                }
                range = selection.getRangeAt(0);//obtain the W3C Range object
                selectedString = range.toString();


            }
        }
        
        if(f === "sticky"){
            if(selection.toString().length !=0 ){
//                path = findPath(targ);
//                //alert(path);
//                var xpath = recreateNode(path);
//                // alert(xpath);
//                var newSpan = attachSpan(xpath, newSpanId);
//                // alert(url);
//                var newSticky = new EditableSticky(xmlHttp, userid, url, stickyIdTag, newSpanId, minimized_icon_tag, usern, selectedString, '', true, path);
//                newSticky.drawEditableSticky();
                openStickyNotes(selection, callingObj);
                
            }else{
                alert('ERROR: Please select some text before calling for a function.');
                return false;
            }
        }

        if(f === "highlight"){
            if(selection.toString().length !=0 ){
                if(highlighClicedFirstTime){
                    guid = guidGenerator();
                    var sheet = document.createElement("style");
                    sheet.type = 'text/css';
                    //sheet.setAttribute("id", "myStyle"+guid);
                    sheet.innerHTML = "."+ guid +" { background-color :"+ callingObj.style.backgroundColor +";}";
                    document.getElementsByTagName('head')[0].appendChild(sheet);
                    highlighClicedFirstTime = false;
                }
                rangy.init();
                //var highlightApplier = rangy.createCssClassApplier(guid + "\" onMouseOver=\"onMouseOverSpan();",true, "div");
                var highlightApplier = rangy.createCssClassApplier(guid, true);
                highlightApplier.applyToSelection();
                makeHighlightRemovable(selection.focusNode, guid);
                selection.removeAllRanges();
            }else{
                alert('ERROR: Please select some text before calling for a function.');
                return false;
               
            }
        }

        if(f === "ShowAllAnnotations"){
            //process(xmlHttp, userid, 'showAllStickies', '', url, '', '', '', '', '');
            window.location.href = "http://www.smartsed.com/smartsed/inkbars/mylibrary/"+u;
        }

        if(f === "close"){
            document.body.removeChild(document.getElementById( "toolbarContainer" ));
        }

        if(f=="savemail"){
            if(!document.getElementById("mailBox"))
                openMailBox();
        }
    }catch(e){
        alert('The operation could not be completed successfully.');
        document.body.removeChild(document.getElementById( "toolbarContainer" ));
        createLoaderSpanElement();
        
    }

    return false;
}

function onMouseOverSpan (el){
    alert("mouse moved");
}
/**
  * Searches and highlights for the given string.
  */
function searchAndHighlight(xmlHttp, userid, url, action)
{
	process(xmlHttp, userid, 'hideStickies', '', url, '', '', '', '', ''); 
	var searchString = '';	//the search term
	if(action == "word"){
		searchString = prompt('Please enter the word to be searched below:', 'Enter here...');
		if(searchString == ''){
			alert('Please enter some text!');
		}else{
			highlight(xmlHttp, userid, url,"word", searchString);
		}
	}
	if(action == "phrase"){
		searchString = prompt('Please enter the phrase to be searched below:', 'Enter here...');
		if(searchString == ''){
			alert('Please enter some text!');
		}else{
			highlight(xmlHttp, userid, url, "phrase", searchString);
		}
	}
}

function highlight(xmlHttp, userid, url, action, searchTerm)
{
  var sarray = new Array();

	if(action == "phrase"){
    	sarray = [searchTerm];
 	 } else {
    	sarray = searchTerm.split(" ");
  	}

  var bt = document.body.innerHTML; //obtain the HTML of the page
 
  for (var i = 0; i < sarray.length; i++) {
    bt = doHighlight(bt, sarray[i]);
  }
  document.body.innerHTML = bt; //replace the HTML with the highlighted terms 
  return true;
}

/**
  * This method searches the HTML body for the given search strings and
  * encapsulate the search strings within a pair of <span> tags.
  */
function doHighlight(bodyText, searchTerm) 
{
  var newText = "";
  var i = -1;
  var lcSearchTerm = searchTerm.toLowerCase();
  var lcBodyText = bodyText.toLowerCase();
    
  while (bodyText.length > 0) {
    i = lcBodyText.indexOf(lcSearchTerm, i+1); //search the HTML body string character by character
    if (i < 0) {			//if none is found
      newText += bodyText;
      bodyText = "";
    } else {
      if (bodyText.lastIndexOf(">", i) >= bodyText.lastIndexOf("<", i)) {	
        // skip anything inside a <script> block
        if (lcBodyText.lastIndexOf("/script>", i) >= lcBodyText.lastIndexOf("<script", i)) {
         	 	newText += bodyText.substring(0, i) + "<span style='background-color:#F4F965;'>" + bodyText.substr(i, searchTerm.length) + "</span>";
         	 	bodyText = bodyText.substr(i + searchTerm.length);
         	 	lcBodyText = bodyText.toLowerCase();
         	 	i = -1;
        }
      }
    }
  }

  return newText;
}


/**
 * Find the path from the node handling the selection to the
 * <body> element.
 */
function findPath(rel)
{
	var path = '';
	var node = rel;
	var root = document.getElementsByTagName("body")[0];
	while ( node != null && root != node )
	{
		var count = 1;
		for ( var prev = node.previousSibling; prev;  prev = prev.previousSibling ) //go up sibling tree
		{
			if ( prev.nodeType == 1 ){
				count += 1;
			}
		}
		path = '/' + String( count ) + path;	//add to the left
		node = node.parentNode; //up the tree
	}
	return path;
}


