/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
var about;
var recipients;
var message;
var flagmailandsave = false;


function openMailBox(){
    var mailBox = document.createElement("span");
    mailBox.id = "mailBox";
    mailBox.className = "mailBox";

    var box = "<DIV class='mailBoxTitle'><SPAN style='padding-right:270px; padding-left:5px;' onmousedown='dragDropElements.startDragMouse(event)'><FONT size='4' color='#FFFFFF'><B>Share</B></FONT></SPAN><SPAN style='padding-top:2px;'><IMG src=" + baseURL +"/smartsed/icons/Close.png onclick='onCloseMailBox()' /></SPAN></DIV>";
    box += "<TABLE cellspacing='2'>";
    box += "<TR><TD width='100px'><DIV></DIV></TD><TD width='200px'><DIV id='errorPageAbout' class='error' align='left' style='visibility:hidden;'>Page Name can`t be empty </DIV></TD></TR>";
    box += "<TR><TD width='100px'><DIV align='right'>Page Name:</DIV></TD><TD width='200px'><DIV align='left'><input type='text' id='titleMailBox'/></DIV></TD></TR>";
    box += "<TR><TD width='100px'><DIV></DIV></TD><TD width='200px'><DIV id='errorRecipient' class='error' align='left' style='visibility:hidden;'>Enter Valid Email ID</DIV></TD></TR>";
    box += "<TR><TD width='100px'><DIV align='right'>Recipient:</DIV></TD><TD width='200px'><DIV align='left'><input style='width:200px;' type='text' id='recipientMailBox'/></DIV></TD></TR>";
    box += "<TR><TD width='100px'><DIV align='right'></DIV></TD><TD width='200px'><DIV align='left'><FONT size='0'><b>Enter recipient's email address. Separate multiple recipients with commas.</b></FONT></DIV></TD></TR>";
    box += "<TR><TD width='100px'><DIV></DIV></TD><TD width='200px'><DIV id='errorMessage' class='error' align='left' style='visibility:hidden;'>Message can not be empty</DIV></TD></TR>";
    box += "<TR><TD width='100px'><DIV align='right'>Message:</DIV></TD><TD width='200px'><DIV align='left'><textarea rows='3' id='messageMailBox'></textarea></DIV></TD></TR>";
    box += "</TABLE>";
    box += "</BR>";
    box += "<DIV align='center'><input type='button' onclick='btnShareOnClick()' id='shareBtnMailBox' value='Save & Share'/></DIV>";

    mailBox.innerHTML = box;

    document.body.insertBefore(mailBox, document.body.firstChild);
    putinCenter(mailBox);
    document.getElementById('titleMailBox').value = document.getElementsByTagName("title").length==0?"title":document.getElementsByTagName("title")[0].textContent;
}

function onCloseMailBox(){
    document.body.removeChild(document.getElementById("mailBox"));
}

function btnShareOnClick(){
    if(validateEmpty()){
        about = document.getElementById('titleMailBox').value;
        recipients = document.getElementById('recipientMailBox').value;
        message = document.getElementById('messageMailBox').value;
        flagmailandsave = true;
        onCloseMailBox();
        sendDataToServer();
    }
}

function validateEmpty(){
    var flagEmpty = true;
    if(document.getElementById('titleMailBox').value == ''){
        document.getElementById('errorPageAbout').style.visibility = 'visible';
        flagEmpty = false;
    }else{
        document.getElementById('errorPageAbout').style.visibility = 'hidden';
    }

    if(document.getElementById('recipientMailBox').value == ''){
        document.getElementById('errorRecipient').style.visibility = 'visible';
        flagEmpty = false;
    }else{
        document.getElementById('errorRecipient').style.visibility = 'hidden';
    }

    if(document.getElementById('messageMailBox').value == ''){
        document.getElementById('errorMessage').style.visibility = 'visible';
        flagEmpty = false;
    }else{
        document.getElementById('errorMessage').style.visibility = 'hidden';
    }

    if(!validateMultipleEmailsCommaSeparated()){
        document.getElementById('errorRecipient').style.visibility = 'visible';
        flagEmpty = false;
    }
    
    return flagEmpty;
}

function validateEmail(field) {
    var regex=/\b[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[com]{2,4}\b/i;
    return (regex.test(field)) ? true : false;
}

function validateMultipleEmailsCommaSeparated() {
    var value = document.getElementById('recipientMailBox').value;
    var result = value.split(",");
    for(var i = 0;i < result.length;i++)
    if(!validateEmail(trim(result[i])))
            return false;
    return true;
}

function trim(str){
    if(!str || typeof str != 'string')
        return null;

    return str.replace(/^[\s]+/,'').replace(/[\s]+$/,'').replace(/[\s]{2,}/,' ');
}