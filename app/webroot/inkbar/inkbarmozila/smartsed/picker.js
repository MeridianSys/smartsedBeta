/*
 *	Gchats color picker by Majid Khosravi
 *	Copyright (c) 2006 - 2008 Gchat Design Studio
 *	URL: http://www.gchats.com
 *	Last Updated: August 29 2009
 *  Gchats color picker is freely distributable under the terms of GPL license.
 *  Please visit: http://www.gchats.com for updates
 *  @Version 1.2
 *--------------------------------------------------------------------------*/
// JavaScript Document
var layerWidth = 200;
var layerHeight = 150;
var currentId = "";
var orgColor ="";
var onPick = undefined;
var onCancel = undefined;
var Obj;
var selectedColor;
var guidPicker;
var flag = true;
var indexCount = 0;
var objLayer;
var flag2 = false;
var oldColor;
var arrayAnnotation = new Array();
var currentSpanID;
var flag3 = true;
var mapHighlight;

if(mapHighlight == null)
   mapHighlight = new HashMap();

function openPicker(Obj, guidPickerTemp) {
    if(flag){
        this.Obj = Obj;
        this.guidPicker = guidPickerTemp;
        oldColor = Obj.style.backgroundColor;
	createLayer(Obj.id,findPosX(Obj)+Obj.offsetWidth+20,findPosY(Obj));        
        flag = false;
        flag2 = false;
    }
}

function createLayer(id,left,top){
	var width = layerWidth;
	var height = layerHeight;
	var zindex = 10000000000000;
	var bgcolor = "#d4d0c8";
	var txtcolor = "#000000";
	var msg = getPickerContent();
	if (document.layers) {
		if (document.layers[id]) {
		   return;
		}
		var layer=document.layers[id]=new Layer(width);
		layer.className = "picker_layer"; 
		layer.name = id;
		layer.left=left;
		layer.top=top;
		layer.clip.height=height;
		layer.visibility = 'show';
		layer.zIndex=zindex;
		layer.bgColor=bgcolor;
		layer.innerHTML = msg;
	}else if (document.all) {
		if (document.all[id]) {
			return
		}
  		var layer= '\n<DIV class="picker_layer" id='+id+' style="position:absolute'
		+'; left:'+left+"px"
		+'; top:'+top+"px"
		+'; width:'+width
		+'; height:'+height		
		+'; visibility:visible'
		+'; z-index:'+zindex
		+';text-align:left">'
		+ msg
		+'</DIV>';
		document.body.insertAdjacentHTML("BeforeEnd",layer);
	}else if(document.getElementById){
		var layer = document.createElement ('span');
		layer.setAttribute ('id', "colorPickerDiv");
		layer.className = "picker_layer";
		layer.style.position= "fixed";
		layer.style.left= left +"px";
		layer.style.top = top + Obj.offsetTop + 20 + "px";
		layer.style.width= width+ "px";
		layer.style.height= height+ "px";
		layer.style.textAlign= "left";
		layer.innerHTML = msg;
                layer.style.zIndex = "99999999";
                //layer.onmouseout = cancel;
                objLayer = layer;
                dragDrop.initElement(layer);
                document.body.insertBefore(layer, document.body.firstChild);
	}
}
function showClr(color){
	Obj.style.backgroundColor=color;
}

function setClr(color){
    Obj.style.backgroundColor=color;
    if(window.getSelection().toString().length > 0){
        try{
        
            var sheet = document.createElement("style");
            sheet.type = 'text/css';
            //sheet.setAttribute("id", "myStyle"+guidPicker);
            sheet.innerHTML = "."+ guidPicker +" { background-color :"+ Obj.style.backgroundColor +";}";
            document.getElementsByTagName('head')[0].appendChild(sheet);
            rangy.init();
            var highlightApplier = rangy.createCssClassApplier(guidPicker, true);
            highlightApplier.applyToSelection();
            //applyMouseoverToSelection(Obj);
            makeHighlightRemovable(window.getSelection().focusNode, guidPicker);
            guid = guidPicker;
            
            window.getSelection().removeAllRanges();
            
        }catch(e){
            alert('The operation could not be completed successfully.');
            document.body.removeChild(document.getElementById( "toolbarContainer" ));
            createLoaderSpanElement();
        }
    }
    flag2 = true;
    cancel();
}

function cancel(){
        if(!flag2){
            Obj.style.backgroundColor = oldColor;
        }
        flag = true;
	document.body.removeChild(document.getElementById( "colorPickerDiv" ));
}

function getPickerContent(){
	var content = 	'<table width="200" border="5" cellpadding="0" cellspacing="1"><tr><td>';
	content += '<table width="100%" border="0" cellpadding="0" cellspacing="1" class="color_table"><tr><td>Choose Color</td><td width="60px" align="right"><input type="submit" value="" onclick="cancel()" class="default_color_btn" /></td></tr></table>';
	content += '</td></tr><tr><td>';
	content += colorTable()+'</td></tr></table>';
	return content;	
}
function colorTable(){
	var clrfix = Array("#000000","#333333","#666666","#999999","#cccccc","#ffffff","#ff0000","#00ff00","#0000ff","#ffff00","#00ffff","#ff00ff");
	var table ='<table border="0"  cellpadding="0" cellspacing="0" bgcolor="#000000"><tr>';
	table += '';
	for(var j=0;j<3;j++){
		table += '<td width="11"><table bgcolor="#000000"  border="0"  cellpadding="0" cellspacing="1"  class="color_table">';
		for(var i=0;i<12;i++){
			var clr ='#000000';
			if(j==1){
				clr = clrfix[i];	
			}
			table += '<tr><td bgcolor="'+clr+'" class="cell_color" onmouseover="showClr('+"'"+clr+"'"+')" onclick="setClr('+"'"+clr+"'"+')"></td></tr>';
		}
		table += '</table></td>';		
	}
	table +='<td><table border="0" cellpadding="0" cellspacing="0">';	
	for (var c = 0; c<6; c++) {
		if(c==0 || c==3){
			table +="<tr>";	
		}
		table += "<td>"	
		
		table = table+'<table border="0" cellpadding="0" cellspacing="1" class="color_table"> ';
		for (var j = 0; j<6; j++) {
			table +="<tr>";
			for (var i = 0; i<6; i++) {
				var clrhex = rgb2hex(j*255/5,i*255/5,c*255/5);
				table += '<td bgcolor="'+clrhex+'" class="cell_color" onmouseover="showClr('+"'"+clrhex+"'"+')" onclick="setClr('+"'"+clrhex+"'"+')"></td>';
			}
			table +="</tr>";
		}
		table +="</table>";
		table += "</td>"	
		if(c==2 || c==5){
			table +="</tr>";	
		}	
	}
	table +='</table></td></tr></table>';	
	return table;
}

function findPosX(obj){
	var curleft = 0;
	if(obj.offsetParent)
        while(1){
			curleft += obj.offsetLeft;
			if(!obj.offsetParent)
			break;
			obj = obj.offsetParent;
		}
	else if(obj.x)
	curleft += obj.x;
	return curleft;
}
function findPosY(obj){
	var curtop = 0;
	if(obj.offsetParent){
		while(1){
			curtop += obj.offsetTop;
			if(!obj.offsetParent){
				break;
			}
			obj = obj.offsetParent;
		}
    }else if(obj.y){
		curtop += obj.y;
	}
	return curtop;
}

function rgb2hex(red, green, blue)
{
    var decColor = red + 256 * green + 65536 * blue;
    var clr = decColor.toString(16);
	for(var i =clr.length;i<6;i++){
		clr = "0"+clr;	
	}
	return "#"+clr;
}

function makeHighlightRemovable(offsetNode, guidPicker){
    //var tempInnerHTML = anchorNode.parentNode.innerHTML;
    offsetNode.parentNode.innerHTML += '<span><img onclick="removeHighlight(this)" title="'+ guidPicker +'" src="' + baseURL + '/smartsed/icons/Close.png"/></span>';
}

function removeHighlight(objThis){
    
    var cssClassName = objThis.title;
    var eleSpan = new Array();
    eleSpan = document.getElementsByTagName('span');
    for(var i=0; i<eleSpan.length;i++){
        if(eleSpan[i].className.toString().search(cssClassName)>=0){
            eleSpan[i].className = eleSpan[i].className.toString().replace(cssClassName, "");
        }
    }

    objThis.parentNode.removeChild(objThis);
}