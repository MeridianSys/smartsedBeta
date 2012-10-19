/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

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
var objAddSticky;
var layerWidthSticky = 200;
var layerHeightSticky = 150;
var flagStickyColorPicker = true;
var flagStickyColorPickerColorSelected;
var stickyNotesOrigionalColor;

function openStickyColorPicker(objSticky) {
    if(flagStickyColorPicker){
        objAddSticky = objSticky;
        stickyNotesOrigionalColor = objSticky.style.backgroundColor;
        createStickyColorLayer(objSticky.id,findPosX(objSticky)+objSticky.offsetWidth+20,findPosY(objSticky));
        flagStickyColorPicker = false;
        flagStickyColorPickerColorSelected = false;
    }
}


function createStickyColorLayer(id,left,top){
    var width = layerWidthSticky;
    var height = layerHeightSticky;
    var zindex = 10000000000000;
    var bgcolor = "#d4d0c8";
    var txtcolor = "#000000";
    var msg = getPickerContentStickyColor();
    var layer = document.createElement ('div');
    layer.setAttribute ('id', "stickyColorPickerDiv");
    layer.className = "picker_layer";
    layer.style.position= "fixed";
    layer.style.left= left - 50 +"px";
    layer.style.top = top + objAddSticky.offsetTop + 20 + "px";
    layer.style.width= width+ "px";
    layer.style.height= height+ "px";
    layer.style.textAlign= "left";
    layer.innerHTML = msg;
    layer.style.zIndex = "99999999";
    dragDrop.initElement(layer);
    document.body.insertBefore(layer, document.body.firstChild);
}

function showClrSticky(color){
    objAddSticky.style.backgroundColor = color;
}

function setClrSticky(color){
    objAddSticky.style.backgroundColor = color;
    flagStickyColorPickerColorSelected = true;
    cancelStickyColor();
    openStickyNotes(window.getSelection(), objAddSticky);
}

function cancelStickyColor(){
    if(!flagStickyColorPickerColorSelected){
        objAddSticky.style.backgroundColor = stickyNotesOrigionalColor;
    }
    flagStickyColorPicker = true;
    document.body.removeChild(document.getElementById("stickyColorPickerDiv"));
}

function getPickerContentStickyColor(){
    var content = '<table width="200" border="5" cellpadding="0" cellspacing="1"><tr><td>';
    content += '<table width="100%" border="0" cellpadding="0" cellspacing="1" class="color_table"><tr><td>Choose Color</td><td width="60px" align="right"><input type="submit" value="" onclick="cancelStickyColor()" class="default_color_btn" /></td></tr></table>';
    content += '</td></tr><tr><td>';
    content += colorTableStickyColor()+'</td></tr></table>';
    return content;
}
function colorTableStickyColor(){
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
                    table += '<tr><td bgcolor="'+clr+'" class="cell_color" onmouseover="showClrSticky('+"'"+clr+"'"+')" onclick="setClrSticky('+"'"+clr+"'"+')"></td></tr>';
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
                            table += '<td bgcolor="'+clrhex+'" class="cell_color" onmouseover="showClrSticky('+"'"+clrhex+"'"+')" onclick="setClrSticky('+"'"+clrhex+"'"+')"></td>';
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

function rgb2hex(red, green, blue){
    var decColor = red + 256 * green + 65536 * blue;
    var clr = decColor.toString(16);
    for(var i =clr.length;i<6;i++){
            clr = "0"+clr;
    }
    return "#"+clr;
}



