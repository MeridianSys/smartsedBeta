/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
var accessFlag = true;

function openAccessSpecifire(obj){
    if(accessFlag){
        accessFlag = false;
        createAccessSpecifireLayer("accessSpecifire", findPosX(obj)+obj.offsetWidth+20, findPosY(obj), obj);
    }
}

function createAccessSpecifireLayer(id,left,top, obj){

    var accessSpecifire = document.createElement("span");
    accessSpecifire.setAttribute("id", id);
    accessSpecifire.style.left = left - 80 + "px";
    accessSpecifire.style.top = top + 20 + "px";
    accessSpecifire.style.zIndex = "99999999";
    accessSpecifire.style.position= "fixed";
    accessSpecifire.style.width= 90+ "px";
    accessSpecifire.style.height= 70+ "px";
    accessSpecifire.className = "picker_layer";
    //accessSpecifire.onmouseout = closeAccessSpecifire;

    dragDrop.initElement(accessSpecifire);

    var tableASL1 = '<table width="100%" border="2" cellpadding="0" cellspacing="1"><tr><td>';
    tableASL1 = tableASL1 + '<table><tr><td align="left" width="90%">Mode</td><td width="30%" align="right"><img src="http://www.smartsed.com/smartsed/app/webroot/inkbar/inkbaropera/icons/Close.png" onClick="closeAccessSpecifire()"></td></tr></table></td></tr><tr><td>';
    var tableASL2 = "</td></tr></table>";
    var accessSpecifireLayer = tableASL1 + '<input type="radio" name="g1" onClick="specifyAccess(false)" value="Private">Private<br><input type="radio" name="g1" CHECKED onClick="specifyAccess(true)" value="Public">Public';
    accessSpecifireLayer = accessSpecifireLayer + tableASL2;
    accessSpecifire.innerHTML = accessSpecifireLayer;

    document.body.insertBefore(accessSpecifire, document.body.firstChild);

}

function onSpecifyAccessMouseOut(){
    document.body.insertBefore(document.createTextNode(" Mouse Out <BR>"), document.body.firstChild);
}

function specifyAccess(value){
    privateFlag = value;
}

function closeAccessSpecifire(){
    accessFlag = true;
    document.body.removeChild(document.getElementById("accessSpecifire"));
}