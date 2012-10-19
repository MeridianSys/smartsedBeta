var windowFocus = true;
var username;
var uid;
var sid;
var chatHeartbeatCount = 0;
var minChatHeartbeat = 2000;
var maxChatHeartbeat = 33000;
var chatHeartbeatTime = minChatHeartbeat;
var originalTitle;
var blinkOrder = 0;

var chatboxFocus = new Array();
var newMessages = new Array();
var newMessagesWin = new Array();
var chatBoxes = new Array();


function smiley_convert(text)
{
    
var smileyTranslator =
{
':-)' : 'happy.jpg',
':-D' : 'biggrin.jpg',
';-)' : 'wink.jpg',
'^_^' : 'happyeyes.gif',
'>:o' : 'laughingeyes.gif',
':3' : 'catsmile.jpg',
'>:-(' : 'grumpy.gif',
':-(' : 'sad.gif',
':-o' : 'shocked.jpg',
'8-)' : 'glasses.jpg',
'8-|' : 'coolshades.gif',
':-P' : 'tongue.gif',
'O.o' : 'woot.gif',
'-_-' : 'dork.jpg',
':/' : 'uncertain.jpg',
'3:' : 'devil.gif',
'O:)' : 'angel.gif',
':-*' : 'kiss.jpg',
'<3' : 'love.jpg',
':v' : 'pacman.jpg',
':|]' : 'robot.jpg',
'-__-' : 'unimpressed',
':putnam:' : 'guyface.jpg'

},url = "/smartsed/css/emotions/", patterns = [],
     metachars = /[[\]{}()*+?.\\|^$\-,&#\s]/g;

// build a regex pattern for each defined property
  for (var i in smileyTranslator) {
    if (smileyTranslator.hasOwnProperty(i)){ // escape metacharacters
      patterns.push('('+i.replace(metachars, "\\$&")+')');
    }
  }

  // build the regular expression and replace
  return text.replace(new RegExp(patterns.join('|'),'g'), function (match) {
    return typeof smileyTranslator[match] != 'undefined' ? 
           '<img src="'+url+smileyTranslator[match]+'"/>' :
           match;
  });



}


$(document).ready(function(){
	originalTitle = document.title;
	startChatSession();

	$([window, document]).blur(function(){
		windowFocus = false;
	}).focus(function(){
		windowFocus = true;
		document.title = originalTitle;
	});
});

function restructureChatBoxes() {
	align = 0;
	for (x in chatBoxes) {
		chatboxtitle = chatBoxes[x];

		if ($("#chatbox_"+chatboxtitle).css('display') != 'none') {
			if (align == 0) {
				$("#chatbox_"+chatboxtitle).css('right', '20px');
			} else {
				width = (align)*(225+7)+20;
				$("#chatbox_"+chatboxtitle).css('right', width+'px');
			}
			align++;
		}
	}
}

function chatWith(chatuser,uid) {
   chatuser = chatuser.replace(/\s/g , "-");
  // alert(chatuser);
	createChatBox(chatuser,uid);
	$("#chatbox_"+chatuser+" .chatboxtextarea").focus();
}

function smiley(tag,title,id) {
   $("#chatbox_"+title+" .chatboxtextarea").val($("#chatbox_"+title+" .chatboxtextarea").val() + tag + ' ');
   $("#smiley"+id).hide();
}

function createChatBox(chatboxtitle,uid,minimizeChatBox) {
    //alert(chatboxtitle);
    //alert(uid);
	if ($("#chatbox_"+chatboxtitle).length > 0) {
		if ($("#chatbox_"+chatboxtitle).css('display') == 'none') {
			$("#chatbox_"+chatboxtitle).css('display','block');
			restructureChatBoxes();
		}
		$("#chatbox_"+uid+" .chatboxtextarea").focus();
		return;
	}

	$(" <div />" ).attr("id","chatbox_"+chatboxtitle)
	.addClass("chatbox")
	.html('<div class="chatboxhead"><div class="chatboxtitle" style="color:#FFFFFF; font-weight:bold;">'+chatboxtitle+'</div><div class="chatboxoptions"><a href="javascript:void(0)" onclick="javascript:toggleChatBoxGrowth(\''+chatboxtitle+'\')">-</a> <a href="javascript:void(0)" onclick="javascript:closeChatBox(\''+chatboxtitle+'\')">X</a></div><br clear="all"/></div><div class="chatboxcontent"></div><div class="chatboxinput"><div class="chattext"><table><tr><td><textarea class="chatboxtextarea" onkeydown="javascript:return checkChatBoxInputKey(event,this,\''+chatboxtitle+'\',\''+uid+'\');"></textarea></td><td><div id="smiley'+uid+'" class="smiley" style="display: none; background-color:#cfcfcf; border:1px solid #000; margin:-157px 0 0 -116px; position:absolute; float:right; width:105px; padding:15px 11px 10px 15px;"><a href="javascript:smiley(\':-)\',\''+chatboxtitle+'\',\''+uid+'\')"><img alt="" src="/smartsed/app/webroot/css/emotions/happy.jpg"></a><a href="javascript:smiley(\':-D\',\''+chatboxtitle+'\',\''+uid+'\')"><img alt="" src="/smartsed/app/webroot/css/emotions/biggrin.jpg"></a><a href="javascript:smiley(\';-)\',\''+chatboxtitle+'\',\''+uid+'\')"><img alt="" src="/smartsed/app/webroot/css/emotions/wink.jpg"></a><a href="javascript:smiley(\'^_^\',\''+chatboxtitle+'\',\''+uid+'\')"><img alt="" src="/smartsed/app/webroot/css/emotions/happyeyes.gif"></a><a href="javascript:smiley(\'>:o\',\''+chatboxtitle+'\',\''+uid+'\')"><img alt="" src="/smartsed/app/webroot/css/emotions/laughingeyes.gif"></a><a href="javascript:smiley(\':3\',\''+chatboxtitle+'\',\''+uid+'\')"><img alt="" src="/smartsed/app/webroot/css/emotions/catsmile.jpg"></a><a href="javascript:smiley(\'>:-(\',\''+chatboxtitle+'\',\''+uid+'\')"><img alt="" src="/smartsed/app/webroot/css/emotions/grumpy.gif"></a><a href="javascript:smiley(\':-(\',\''+chatboxtitle+'\',\''+uid+'\')"><img alt="" src="/smartsed/app/webroot/css/emotions/sad.gif"></a><a href="javascript:smiley(\':-o\',\''+chatboxtitle+'\',\''+uid+'\')"><img alt="" src="/smartsed/app/webroot/css/emotions/shocked.jpg"></a><a href="javascript:smiley(\'8-)\',\''+chatboxtitle+'\',\''+uid+'\')"><img alt="" src="/smartsed/app/webroot/css/emotions/glasses.jpg"></a><a href="javascript:smiley(\'8-|\',\''+chatboxtitle+'\',\''+uid+'\')"><img alt="" src="/smartsed/app/webroot/css/emotions/coolshades.gif"></a><a href="javascript:smiley(\':-P\',\''+chatboxtitle+'\',\''+uid+'\')"><img alt="" src="/smartsed/app/webroot/css/emotions/tongue.gif"></a><a href="javascript:smiley(\'O.o\',\''+chatboxtitle+'\',\''+uid+'\')"><img alt="" src="/smartsed/app/webroot/css/emotions/woot.gif"></a><a href="javascript:smiley(\'-_-\',\''+chatboxtitle+'\',\''+uid+'\')"><img alt="" src="/smartsed/app/webroot/css/emotions/dork.jpg"></a><a href="javascript:smiley(\':/\',\''+chatboxtitle+'\',\''+uid+'\')"><img alt="" src="/smartsed/app/webroot/css/emotions/uncertain.jpg"></a><a href="javascript:smiley(\'3:\',\''+chatboxtitle+'\',\''+uid+'\')"><img alt="" src="/smartsed/app/webroot/css/emotions/devil.gif"></a><a href="javascript:smiley(\'O:)\',\''+chatboxtitle+'\',\''+uid+'\')"><img alt="" src="/smartsed/app/webroot/css/emotions/angel.gif"></a><a href="javascript:smiley(\':-*\',\''+chatboxtitle+'\',\''+uid+'\')"><img alt="" src="/smartsed/app/webroot/css/emotions/kiss.jpg"></a><a href="javascript:smiley(\'<3\',\''+chatboxtitle+'\',\''+uid+'\')"><img alt="" src="/smartsed/app/webroot/css/emotions/love.jpg"></a><a href="javascript:smiley(\':v\',\''+chatboxtitle+'\',\''+uid+'\')"><img alt="" src="/smartsed/app/webroot/css/emotions/pacman.jpg"></a><a href="javascript:smiley(\':|]\',\''+chatboxtitle+'\',\''+uid+'\')"><img alt="" src="/smartsed/app/webroot/css/emotions/robot.jpg"></a><a href="javascript:smiley(\':putnam:\',\''+chatboxtitle+'\',\''+uid+'\')"><img alt="" src="/smartsed/app/webroot/css/emotions/guyface.jpg"></a></div><div style="float:right; z-index:10000;" class="sml"><a class="sm" href="javascript:void(0)" ></a></div></td></tr></table></div></div>')
	.appendTo($( "body" ));
        
	$("#chatbox_"+chatboxtitle).css('bottom', '80px');

	chatBoxeslength = 0;

	for (x in chatBoxes) {
		if ($("#chatbox_"+chatBoxes[x]).css('display') != 'none') {
			chatBoxeslength++;
		}
	}

	if (chatBoxeslength == 0) {
		$("#chatbox_"+chatboxtitle).css('right', '20px');
	} else {
		width = (chatBoxeslength)*(225+7)+20;
		$("#chatbox_"+chatboxtitle).css('right', width+'px');
	}

	chatBoxes.push(chatboxtitle);

	if (minimizeChatBox == 1) {
		minimizedChatBoxes = new Array();

		if ($.cookie('chatbox_minimized')) {
			minimizedChatBoxes = $.cookie('chatbox_minimized').split(/\|/);
		}
		minimize = 0;
		for (j=0;j<minimizedChatBoxes.length;j++) {
			if (minimizedChatBoxes[j] == chatboxtitle) {
				minimize = 1;
			}
		}

		if (minimize == 1) {
			$('#chatbox_'+chatboxtitle+' .chatboxcontent').css('display','none');
			$('#chatbox_'+chatboxtitle+' .chatboxinput').css('display','none');
		}
	}

	chatboxFocus[chatboxtitle] = false;

        $('#chatbox_'+chatboxtitle+' .sml').click(function() {
            $('#smiley'+uid).toggle();
            return false;
        });
        

	$("#chatbox_"+chatboxtitle+" .chatboxtextarea").blur(function(){
		chatboxFocus[chatboxtitle] = false;
		//$("#chatbox_"+chatboxtitle+" .chatboxtextarea").removeClass('chatboxtextareaselected');
                $("#chatbox_"+chatboxtitle+" .chattext").removeClass('chatboxtextareaselected');
	}).focus(function(){
		chatboxFocus[chatboxtitle] = true;
		newMessages[chatboxtitle] = false;
		$('#chatbox_'+chatboxtitle+' .chatboxhead').removeClass('chatboxblink');
		//$("#chatbox_"+chatboxtitle+" .chatboxtextarea").addClass('chatboxtextareaselected');
                $("#chatbox_"+chatboxtitle+" .chattext").addClass('chatboxtextareaselected');

	});

	$("#chatbox_"+chatboxtitle).click(function() {
		if ($('#chatbox_'+chatboxtitle+' .chatboxcontent').css('display') != 'none') {
			$("#chatbox_"+chatboxtitle+" .chatboxtextarea").focus();
                        $("#smiley"+uid).hide();
		}
	});

	$("#chatbox_"+chatboxtitle).show();
                
}


function chatHeartbeat(){

	var itemsfound = 0;

	if (windowFocus == false) {

		var blinkNumber = 0;
		var titleChanged = 0;
		for (x in newMessagesWin) {
			if (newMessagesWin[x] == true) {
				++blinkNumber;
				if (blinkNumber >= blinkOrder) {
					document.title = x+' says...';
					titleChanged = 1;
					break;
				}
			}
		}

		if (titleChanged == 0) {
			document.title = originalTitle;
			blinkOrder = 0;
		} else {
			++blinkOrder;
		}

	} else {
		for (x in newMessagesWin) {
			newMessagesWin[x] = false;
		}
	}

	for (x in newMessages) {
		if (newMessages[x] == true) {
			if (chatboxFocus[x] == false) {
				//FIXME: add toggle all or none policy, otherwise it looks funny
				$('#chatbox_'+x+' .chatboxhead').toggleClass('chatboxblink');
			}
		}
	}

	$.ajax({
	  url: SITE_URL+"chats/chatheartbeat",
	  cache: false,
	  dataType: "json",
	  success: function(data) {
                 //sid = data.sid;
                 //if(sid!='') {
                        $.each(data.items, function(i,item){
                                if (item)	{ // fix strange ie bug
                                   // alert(item.f);
                                    if(item.f != '' && item.s != 2 && item.g != '' && item.m != '') {
                                        chatboxtitle = item.f;
                                        //alert(chatboxtitle);
                                        if ($("#chatbox_"+chatboxtitle).length <= 0) {
                                                createChatBox(chatboxtitle,item.g);
                                        }
                                        if ($("#chatbox_"+chatboxtitle).css('display') == 'none') {
                                                $("#chatbox_"+chatboxtitle).css('display','block');
                                                restructureChatBoxes();
                                        }

                                        if (item.s == 1) {
                                                item.f = username;
                                        }

                                        if (item.s == 2) {
                                                $("#chatbox_"+chatboxtitle+" .chatboxcontent").append('<div class="chatboxmessage"><span class="chatboxinfo">'+item.m+'</span></div>');
                                        } else {
                                                newMessages[chatboxtitle] = true;
                                                newMessagesWin[chatboxtitle] = true;
                                                if(item.f == "me"){
                                                $("#chatbox_"+chatboxtitle+" .chatboxcontent").append('<div class="chatboxmessage"><span class="chatboxmessagefrom" style="color:#59923d;">'+item.f+':&nbsp;&nbsp;</span><span class="chatboxmessagecontent">'+item.m+'</span></div>');
                                                } else {
                                                    var se = item.f;
                                                    se = se.replace(/-/gi," ");
                                                    var ms = item.m;
                                                    ms = ms.replace(/&lt;/g,"<").replace(/&gt;/g,">").replace(/&quot;/g,"\"");
                                                  $("#chatbox_"+chatboxtitle+" .chatboxcontent").append('<div class="chatboxmessage"><span class="chatboxmessagefrom" style="color:#228de7;">'+se+':&nbsp;&nbsp;</span><span class="chatboxmessagecontent">'+ms+'</span></div>');
                                                }
                                        }

                                        $("#chatbox_"+chatboxtitle+" .chatboxcontent").scrollTop($("#chatbox_"+chatboxtitle+" .chatboxcontent")[0].scrollHeight);
                                        itemsfound += 1;
                                }
                      }
             
    });
               //  }
		chatHeartbeatCount++;

		if (itemsfound > 0) {
			chatHeartbeatTime = minChatHeartbeat;
			chatHeartbeatCount = 1;
		} else if (chatHeartbeatCount >= 10) {
			chatHeartbeatTime *= 2;
			chatHeartbeatCount = 1;
			if (chatHeartbeatTime > maxChatHeartbeat) {
				chatHeartbeatTime = maxChatHeartbeat;
			}
		}

		setTimeout('chatHeartbeat();',chatHeartbeatTime);
     
	}});
}

function closeChatBox(chatboxtitle) {
	$('#chatbox_'+chatboxtitle).css('display','none');
	restructureChatBoxes();

	$.post(SITE_URL+"chats/closechat", { chatbox: chatboxtitle} , function(data){
	});

}

function toggleChatBoxGrowth(chatboxtitle) {
	if ($('#chatbox_'+chatboxtitle+' .chatboxcontent').css('display') == 'none') {

		var minimizedChatBoxes = new Array();

		if ($.cookie('chatbox_minimized')) {
			minimizedChatBoxes = $.cookie('chatbox_minimized').split(/\|/);
		}

		var newCookie = '';

		for (i=0;i<minimizedChatBoxes.length;i++) {
			if (minimizedChatBoxes[i] != chatboxtitle) {
				newCookie += chatboxtitle+'|';
			}
		}

		newCookie = newCookie.slice(0, -1)


		$.cookie('chatbox_minimized', newCookie);
		$('#chatbox_'+chatboxtitle+' .chatboxcontent').css('display','block');
		$('#chatbox_'+chatboxtitle+' .chatboxinput').css('display','block');
		$("#chatbox_"+chatboxtitle+" .chatboxcontent").scrollTop($("#chatbox_"+chatboxtitle+" .chatboxcontent")[0].scrollHeight);
	} else {

		var newCookie = chatboxtitle;

		if ($.cookie('chatbox_minimized')) {
			newCookie += '|'+$.cookie('chatbox_minimized');
		}


		$.cookie('chatbox_minimized',newCookie);
		$('#chatbox_'+chatboxtitle+' .chatboxcontent').css('display','none');
		$('#chatbox_'+chatboxtitle+' .chatboxinput').css('display','none');
	}

}

function checkChatBoxInputKey(event,chatboxtextarea,chatboxtitle,uid) {

	if(event.keyCode == 13 && event.shiftKey == 0)  {
		message = $(chatboxtextarea).val();
		message = message.replace(/^\s+|\s+$/g,"");
               var msg;
		$(chatboxtextarea).val('');
		$(chatboxtextarea).focus();
		$(chatboxtextarea).css('height','44px');
		if (message != '') {
			$.post(SITE_URL+"chats/sendchat", {to: uid, sename: chatboxtitle, message: message} , function(data){
                                //message = message.replace(/</g,"&lt;").replace(/>/g,"&gt;").replace(/\"/g,"&quot;");
                              msg =  smiley_convert(message);
                                $("#chatbox_"+chatboxtitle+" .chatboxcontent").append('<div class="chatboxmessage"><span class="chatboxmessagefrom" style="color:#59923d;">'+username+':&nbsp;&nbsp;</span><span class="chatboxmessagecontent">'+msg+'</span></div>');
				$("#chatbox_"+chatboxtitle+" .chatboxcontent").scrollTop($("#chatbox_"+chatboxtitle+" .chatboxcontent")[0].scrollHeight);
			});
		}
		chatHeartbeatTime = minChatHeartbeat;
		chatHeartbeatCount = 1;

		return false;
	}

	var adjustedHeight = chatboxtextarea.clientHeight;
	var maxHeight = 94;

	if (maxHeight > adjustedHeight) {
		adjustedHeight = Math.max(chatboxtextarea.scrollHeight, adjustedHeight);
		if (maxHeight)
			adjustedHeight = Math.min(maxHeight, adjustedHeight);
		if (adjustedHeight > chatboxtextarea.clientHeight)
			$(chatboxtextarea).css('height',adjustedHeight+8 +'px');
	} else {
		$(chatboxtextarea).css('overflow','auto');
	}

}

function startChatSession(){
	$.ajax({
          type: "POST",
	  url: SITE_URL+"chats/startchatsession",
	  cache: false,
	  dataType: "json",
	  success: function(data) { 

		username = data.username;
                uid = data.uid;
                
		$.each(data.items, function(i,item){  
			if (item)	{ // fix strange ie bug
                      
                            if(item.f != '' && item.g != '') {
                                var senderid = item.g;
                                    chatboxtitle = item.f;
                               
				if ($("#chatbox_"+chatboxtitle).length <= 0) {
					createChatBox(chatboxtitle,senderid,1);
				}

				if (item.s == 1) {
					item.f = username;
				}

				if (item.s == 2) {
					$("#chatbox_"+chatboxtitle+" .chatboxcontent").append('<div class="chatboxmessage"><span class="chatboxinfo">'+item.m+'</span></div>');
				} else {
                                    if(item.f == "me"){
                                        var mess = item.m;
                                          mess = mess.replace(/&lt;/g,"<").replace(/&gt;/g,">").replace(/&quot;/g,"\"");
					$("#chatbox_"+chatboxtitle+" .chatboxcontent").append('<div class="chatboxmessage"><span class="chatboxmessagefrom" style="color:#59923d;">'+item.f+':&nbsp;&nbsp;</span><span class="chatboxmessagecontent">'+mess+'</span></div>');
                                    } else {
                                        var se = item.f;
                                        se = se.replace(/-/gi," ");
                                         var mes = item.m;
                                          mes = mes.replace(/&lt;/g,"<").replace(/&gt;/g,">").replace(/&quot;/g,"\"");
                                        $("#chatbox_"+chatboxtitle+" .chatboxcontent").append('<div class="chatboxmessage"><span class="chatboxmessagefrom" style="color:#228de7;">'+se+':&nbsp;&nbsp;</span><span class="chatboxmessagecontent">'+mes+'</span></div>');
                                    }
				}

			}
                     }
		});

		for (i=0;i<chatBoxes.length;i++) {
			chatboxtitle = chatBoxes[i];
			$("#chatbox_"+chatboxtitle+" .chatboxcontent").scrollTop($("#chatbox_"+chatboxtitle+" .chatboxcontent")[0].scrollHeight);
			setTimeout('$("#chatbox_"+chatboxtitle+" .chatboxcontent").scrollTop($("#chatbox_"+chatboxtitle+" .chatboxcontent")[0].scrollHeight);', 100); // yet another strange ie bug
		}

	setTimeout('chatHeartbeat();',chatHeartbeatTime);

	}});
}

/**
 * Cookie plugin
 *
 * Copyright (c) 2006 Klaus Hartl (stilbuero.de)
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 *
 */

jQuery.cookie = function(name, value, options) {
    if (typeof value != 'undefined') { // name and value given, set cookie
        options = options || {};
        if (value === null) {
            value = '';
            options.expires = -1;
        }
        var expires = '';
        if (options.expires && (typeof options.expires == 'number' || options.expires.toUTCString)) {
            var date;
            if (typeof options.expires == 'number') {
                date = new Date();
                date.setTime(date.getTime() + (options.expires * 24 * 60 * 60 * 1000));
            } else {
                date = options.expires;
            }
            expires = '; expires=' + date.toUTCString(); // use expires attribute, max-age is not supported by IE
        }
        // CAUTION: Needed to parenthesize options.path and options.domain
        // in the following expressions, otherwise they evaluate to undefined
        // in the packed version for some reason...
        var path = options.path ? '; path=' + (options.path) : '';
        var domain = options.domain ? '; domain=' + (options.domain) : '';
        var secure = options.secure ? '; secure' : '';
        document.cookie = [name, '=', encodeURIComponent(value), expires, path, domain, secure].join('');
    } else { // only name given, get cookie
        var cookieValue = null;
        if (document.cookie && document.cookie != '') {
            var cookies = document.cookie.split(';');
            for (var i = 0; i < cookies.length; i++) {
                var cookie = jQuery.trim(cookies[i]);
                // Does this cookie string begin with the name we want?
                if (cookie.substring(0, name.length + 1) == (name + '=')) {
                    cookieValue = decodeURIComponent(cookie.substring(name.length + 1));
                    break;
                }
            }
        }
        return cookieValue;
    }
};