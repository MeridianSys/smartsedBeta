/*
 * DBConnect.js
 *
 * The purpose for this class is to establish connection with server
 * and provide server-side functionalities.
 *
 * @author Nikesh kumar
 * @version 12/07/2011
 */
var pageIndex = 0;
/**
  * Obtain a XMLHttpRequest object for the relevant web browser.
  */
function obtainXMLHttpRequestObj(){
var xmlHttp;
	try{
		//if Mozilla/Opera
		xmlHttp = new XMLHttpRequest();
	}
	catch(e){ //if Internet Explorer
		var xmlHttpVersions = new Array("MSXML2.XMLHTTP.6.0",
										"MSXML2.XMLHTTP.5.0",
										"MSXML2.XMLHTTP.4.0",
										"MSXML2.XMLHTTP.3.0",
										"MSXML2.XMLHTTP",
										"Microsoft.XMLHTTP");
		for(var i = 0; i<xmlHttpVersions.length && !xmlHttp; i++){
			try{
					xmlHttp = new ActiveXObject(xmlHttpVersions[i]);
			}catch(e){}
		}
	}
	if(!xmlHttp){
		alert("Error: unable to create XMLHttpRequest object.");
	}else{
		return xmlHttp;
	}
}

/**
  * Sends the AJAX requests.
  */
function process(xmlHTTP, userID, actions, stickyID, stickyURL, stickyPath, spanID, miniID, uReferredText, uAnnotation, ac, title){

	var xmlHttp = xmlHTTP;
	var action = actions;
	var serverAddress = baseURL + "/smartsed/db/class_DBConnectHandler.php";
        var user_id = userID; //the user's id
	var sticky_id = stickyID;
	var url = stickyURL; //the url of the annotated web page
	var path = stickyPath; //path of range object in a document
	var access = ac;
	var span_id = spanID;
	var mini_icon_id = miniID;
	var annotation = encodeURI(uAnnotation); //annotations
        var title = encodeURI(title); // titles
	var referredText = encodeURI(uReferredText); //the referred text in a document
	
	var addStickyRequestParams = "addSticky=true&user_id=" + user_id + "&stickyId=" + sticky_id + "&url=" + url + "&path=" + path +  "&span_id=" + span_id + "&mini_icon_id=" + mini_icon_id + "&annotation=" + annotation + "&referredText=" + referredText + "&access=" + access +"&title=" + title +"&submitted=true";
      //  alert(addStickyRequestParams);
        var removeStickyRequestParams = "removeSticky=true&user_id=" + user_id + "&stickyId=" + sticky_id + "&url=" + url + "&submitted=true";
	var modifyStickyRequestParams = "modifySticky=true&user_id=" + user_id + "&stickyId=" + sticky_id + "&annotation=" + annotation + "&submitted=true";
	var showUserStickiesParams = "showUserStickies=true&user_id=" + user_id + "&url=" + url + "&submitted=true";
	var showAllStickiesParams = "showAllStickies=true&user_id=" + user_id + "&url=" + url + "&submitted=true";
       
	var hideStickiesParams = "showAllStickies=true&user_id=" + user_id + "&url=" + url + "&submitted=true"; //same as show stickies so no need to change action name
	if(xmlHttp){
		
		try{
                        xmlHttp.open("POST",serverAddress, true);
                        xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                        alert(action);
			if(action === 'addSticky'){
				xmlHttp.onreadystatechange = function(){
					if(xmlHttp.readyState == 4){
        
						if(xmlHttp.status == 200) {//Call a function when the state changes.								
							handleAddStickyRequest(xmlHttp);
						}else{
							alert("Error: failed to read server availability:\n" + xmlHttp.statusText + "\n Server address: " + serverAddress);
						}
					}
				}
				xmlHttp.send(addStickyRequestParams);
			}
	
			if(action === 'removeSticky'){
				xmlHttp.onreadystatechange = function(){
					if(xmlHttp.readyState == 4){
						if( xmlHttp.status == 200) {//Call a function when the state changes.
								handleRemoveStickyRequest(xmlHttp);
						}else{
							alert("Error: failed to read server availability:\n" + xmlHttp.statusText + "\n Server address: " + serverAddress);
						}
					}
				}
				xmlHttp.send(removeStickyRequestParams);
			}

			if(action === 'modifySticky'){
				
				xmlHttp.onreadystatechange = function(){
					if(xmlHttp.readyState == 4){
						if( xmlHttp.status == 200) {//Call a function when the state changes.
							handleModifyStickyRequest(xmlHttp);
						}else{
							alert("Error: failed to read server availability:\n" + xmlHttp.statusText + "\n Server address: " + serverAddress);
						}
					}
				}
				xmlHttp.send(modifyStickyRequestParams);
			}
			
			if(action === 'showUserStickies'){
				xmlHttp.onreadystatechange = function(){
					if(xmlHttp.readyState == 4){
                                            //alert(xmlHttp.status);
        					if( xmlHttp.status == 200) {//Call a function when the state changes.
							handleShowUserStickiesRequest(xmlHttp, url);				
						}else{
							alert("Error: failed to read server availability:\n" + xmlHttp.statusText + "\n Server address: " + serverAddress);
						}
					}
				}
				xmlHttp.send(showUserStickiesParams);
			}

			if(action === 'showAllStickies'){
				
				xmlHttp.onreadystatechange = function(){
					if(xmlHttp.readyState == 4){
        					if( xmlHttp.status == 200) {//Call a function when the state changes.
                                                    //alert('nik');
							    handleShowAllStickiesRequest(xmlHttp, url, user_id);
						}else{
							alert("Error: failed to read server availability:\n" + xmlHttp.statusText + "\n Server address: " + serverAddress);
						}
					}
				}
				xmlHttp.send(showAllStickiesParams);
			}
			if(action === 'hideStickies'){
				
				xmlHttp.onreadystatechange = function(){
					if(xmlHttp.readyState == 4){
						if( xmlHttp.status == 200) {//Call a function when the state changes.
							    handleHideStickiesRequest(xmlHttp);
						}else{
							alert("Error: failed to read server availability:\n" + xmlHttp.statusText + "\n Server address: " + serverAddress);
						}
					}
				}
				xmlHttp.send(hideStickiesParams);
			}
			
		}catch(e){
			alert("Failed to connect to server:\n" + e.toString());
		}
	}
}

/**
  * Handles the response generated from server after an addSticky 
  * request.
  */
function handleAddStickyRequest(xmlHttp){
  //  alert(xmlHttp.responseXML);
	var xmlResponse = xmlHttp.responseXML;
	if(!xmlResponse || !xmlResponse.documentElement)
		throw("Invalid XML structure1:\n" + xmlHttp.responseText);

	var rootNodeName = xmlResponse.documentElement.nodeName;
	if(rootNodeName == "parseerror")
		throw("Invalid XML structure2:\n" + xmlHttp.responseText);

	var xmlRoot = xmlResponse.documentElement;
	
	if(rootNodeName != "response" || !xmlRoot.firstChild)
		throw("Invalid XML structure3:\n" + xmlHttp.responseText);

	var responseText = xmlRoot.firstChild.data;
	if(responseText == 'false'){
		alert('Failed to add sticky. Please contact administrator for assistance.');
	}
	
}

/**
  * Handles the response generated from server after an removeSticky 
  * request.
  */
function handleRemoveStickyRequest(xmlHttp){
	var xmlResponse = xmlHttp.responseXML;
	if(!xmlResponse || !xmlResponse.documentElement)
		throw("Invalid XML structure1:\n" + xmlHttp.responseText);

	var rootNodeName = xmlResponse.documentElement.nodeName;

	if(rootNodeName == "parseerror")
		throw("Invalid XML structure2:\n" + xmlHttp.responseText);

	var xmlRoot = xmlResponse.documentElement;
	
	if(rootNodeName != "response" || !xmlRoot.firstChild)
		throw("Invalid XML structure3:\n" + xmlHttp.responseText);

	var responseText = xmlRoot.firstChild.data;
	if(responseText == 'false'){
		alert('Failed to remove sticky. Please contact administrator for assistance.');
	}
}

/**
  * Handles the response generated from server after an modifySticky 
  * request.
  */
function handleModifyStickyRequest(xmlHttp){
	var xmlResponse = xmlHttp.responseXML;
	if(!xmlResponse || !xmlResponse.documentElement)
		throw("Invalid XML structure1:\n" + xmlHttp.responseText);

	var rootNodeName = xmlResponse.documentElement.nodeName;

	if(rootNodeName == "parseerror")
		throw("Invalid XML structure2:\n" + xmlHttp.responseText);

	var xmlRoot = xmlResponse.documentElement;
	
	if(rootNodeName != "response" || !xmlRoot.firstChild)
		throw("Invalid XML structure3:\n" + xmlHttp.responseText);

	var responseText = xmlRoot.firstChild.data;
	
	if(responseText == 'false'){
		alert('Failed to modify sticky. Please contact administrator for assistance.');
	}
}

/**
  * Handles the response generated from server after an showUserStickies 
  * request.
  */
function handleShowUserStickiesRequest(xmlHttp, url){
	var xmlResponse = xmlHttp.responseXML;
	if(!xmlResponse || !xmlResponse.documentElement)
			throw("Invalid XML structure1:\n" + xmlHttp.responseText);

	var rootNodeName = xmlResponse.documentElement.nodeName;
	if(rootNodeName == "parseerror")
			throw("Invalid XML structure2:\n" + xmlHttp.responseText);

	var xmlRoot = xmlResponse.documentElement;
	if(rootNodeName != "response" || !xmlRoot.firstChild)
			throw("Invalid XML structure3:\n" + xmlHttp.responseText);

	var stickiesFound = xmlRoot.getElementsByTagName('stickiesFound');
	var numOfStickiesFound = stickiesFound[0].firstChild.data;
	if(numOfStickiesFound != 0){	
		var stickyids_array = xmlRoot.getElementsByTagName('stickyid');
		var author_array = xmlRoot.getElementsByTagName('author');
		var userid_array = xmlRoot.getElementsByTagName('user_id');
		var paths_array  = xmlRoot.getElementsByTagName('path');
		var spanid_array  = xmlRoot.getElementsByTagName('span_id');
		var miniiconid_array  = xmlRoot.getElementsByTagName('mini_icon_id');
		var annotations_array  = xmlRoot.getElementsByTagName('annotation');
		var referredTexts_array  = xmlRoot.getElementsByTagName('referredText');
		var date_created_array  = xmlRoot.getElementsByTagName('dateCreated');
		var date_modified_array  = xmlRoot.getElementsByTagName('dateModified');
		for(var i=0; i< numOfStickiesFound; i++){
			var sid = stickyids_array[i].firstChild.data;
			var un =  author_array[i].firstChild.data;
			var uid = userid_array[i].firstChild.data;
			var pth = paths_array[i].firstChild.data; //used by creating spans only
			var spid = spanid_array[i].firstChild.data;
			var minid = miniiconid_array[i].firstChild.data;
			var anno = annotations_array[i].firstChild.data;
			var rtext = referredTexts_array[i].firstChild.data;
			var dc = date_created_array[i].firstChild.data;
			var dm = date_modified_array[i].firstChild.data;
			//create spans and ranges HERE
			var node = recreateNode(pth);			
			if(alreadyExist(spid) == false){
				if(attachSpan(node, spid) == true){	//if the <span> can be successfully attached then add sticky
        
					var s = new PartialEditableSticky(xmlHttp, uid, url, sid, spid, minid, un, rtext, anno, dc, dm, true);
					s.drawPartialEditableSticky();
				}else{
					process(xmlHttp, uid, 'removeSticky', sid, url, '', '', '', '', '','');
				}
			}
		}
	}
}

/**
  * Handles the response generated from server after an showAllStickies 
  * request.
  */
function handleShowAllStickiesRequest(xmlHttp, url, userid){
    //alert(url);
	var xmlResponse = xmlHttp.responseXML;
	if(!xmlResponse || !xmlResponse.documentElement)
			throw("Invalid XML structure1:\n" + xmlHttp.responseText);

	var rootNodeName = xmlResponse.documentElement.nodeName;
	if(rootNodeName == "parseerror")
			throw("Invalid XML structure2:\n" + xmlHttp.responseText);

	var xmlRoot = xmlResponse.documentElement;
	if(rootNodeName != "response" || !xmlRoot.firstChild)
			throw("Invalid XML structure3:\n" + xmlHttp.responseText);

       
	var stickiesFound = xmlRoot.getElementsByTagName('stickiesFound');
       
	var numOfStickiesFound = stickiesFound[0].firstChild.data;
       // alert(numOfStickiesFound);
	if(numOfStickiesFound != 0){	
		var stickyids_array = xmlRoot.getElementsByTagName('stickyid');
                
		var author_array = xmlRoot.getElementsByTagName('author');
		var userid_array = xmlRoot.getElementsByTagName('user_id');
               // alert(userid_array);
		var access_array  = xmlRoot.getElementsByTagName('access');
		var paths_array  = xmlRoot.getElementsByTagName('path');
		var spanid_array  = xmlRoot.getElementsByTagName('span_id');
		var miniiconid_array  = xmlRoot.getElementsByTagName('mini_icon_id');
		var annotations_array  = xmlRoot.getElementsByTagName('annotation');
		var referredTexts_array  = xmlRoot.getElementsByTagName('referredText');
		var date_created_array  = xmlRoot.getElementsByTagName('dateCreated');
		var date_modified_array  = xmlRoot.getElementsByTagName('dateModified');
		for(var i=0; i< numOfStickiesFound; i++){ 
			var sid = stickyids_array[i].firstChild.data;
			var un =  author_array[i].firstChild.data;
			var uid = userid_array[i].firstChild.data;
			var acc = access_array[i].firstChild.data;
			var pth = paths_array[i].firstChild.data; //used by creating spans only
			var spid = spanid_array[i].firstChild.data;
			var minid = miniiconid_array[i].firstChild.data;
			var anno = annotations_array[i].firstChild.data;
			var rtext = referredTexts_array[i].firstChild.data;
			var dc = date_created_array[i].firstChild.data;
			var dm = date_modified_array[i].firstChild.data;
			//create spans and ranges HERE
                        var node = recreateNode(pth);
                         //  alert(userid +'=='+ uid +'&&'+ acc +'<='+ numOfStickiesFound);
			//if(userid == uid && acc <= numOfStickiesFound){  // displaying only user annotation
                        if(acc <= numOfStickiesFound){   /// dispaly all notation
			if(alreadyExist(spid) == false){
				if(attachSpan(node, spid) == true){	//if the <span> can be successfully attached then add sticky
					var s = new NonEditableSticky(xmlHttp, uid, url, sid, spid, minid, un, rtext, anno, dc, dm, true);
					s.drawNonEditableSticky();
					
				}else{
					process(xmlHttp, uid, 'removeSticky', sid, url, '', '', '', '', '','');
				}
			  }
			}
		}
	}
}

/**
  * Handles the response generated from server after an hideStickies 
  * request.
  */
function handleHideStickiesRequest(xmlHttp){
	var xmlResponse = xmlHttp.responseXML;
	if(!xmlResponse || !xmlResponse.documentElement)
			throw("Invalid XML structure1:\n" + xmlHttp.responseText);

	var rootNodeName = xmlResponse.documentElement.nodeName;
	if(rootNodeName == "parseerror")
			throw("Invalid XML structure2:\n" + xmlHttp.responseText);

	var xmlRoot = xmlResponse.documentElement;
	if(rootNodeName != "response" || !xmlRoot.firstChild)
			throw("Invalid XML structure3:\n" + xmlHttp.responseText);

	var stickiesFound = xmlRoot.getElementsByTagName('stickiesFound');
	var numOfStickiesFound = stickiesFound[0].firstChild.data;
	if(numOfStickiesFound != 0){	
		var stickyids_array = xmlRoot.getElementsByTagName('stickyid');
		var spanid_array  = xmlRoot.getElementsByTagName('span_id');
		var miniiconid_array  = xmlRoot.getElementsByTagName('mini_icon_id');
		
		for(var i=0; i< numOfStickiesFound; i++){
			var sid = stickyids_array[i].firstChild.data;
			var spid = spanid_array[i].firstChild.data;
			var minid = miniiconid_array[i].firstChild.data;
			var stickyElement = document.getElementById(spid);
			if(stickyElement != null){
				if(document.getElementById(sid) != null){
					stickyElement.removeChild(document.getElementById(sid));
				}
				if(document.getElementById(minid) != null){
					stickyElement.removeChild(document.getElementById(minid));
				}
			}
		}
	}
}

/**
  * Check if sticky is already displayed.
  */
function alreadyExist(spanId){
	if(document.getElementById(spanId) == null){
		return false;
	}else{
		return true;
	}
}
/**
  * Create and attach a <span> tag with the given id to
  * the node provided.
  */
function attachSpan (node, spanId){
	
	if(node != null){
		var elementContainer;
		var furtherContainer;
		var spanEle = document.createElement("span"); //create a <span>
	 	spanEle.setAttribute("id", spanId);

		if(node.nodeType == 3){	//if the node is a text node, obtain its parent node
	 		elementContainer = node.parentNode;
   		}else{
			elementContainer = node;
		}
	    if(elementContainer.nodeName == "A"){	//if the elementContainer is an anchor element
			furtherContainer = elementContainer.parentNode;
			furtherContainer.insertBefore(spanEle, elementContainer);
		}else{
	 		elementContainer.insertBefore(spanEle, elementContainer.firstChild);
		}
		return true;
	}
	return false;
}

/**
 * Find node where the annotation has been made from the given path.
 */
function recreateNode(path)
{
	var node;
	var path1 = new String(path);
	var root = document.getElementsByTagName("body")[0]; //obtain <body> element
	node = root;
	nodes = path1.split( '/' ); //split the given path by the forward slash
		for ( var i = 1;  i < nodes.length;  ++i )
		{
			var count = Number( nodes[ i ] ); //number of siblings
			for (node = node.firstChild;  node != null;  node = node.nextSibling) //traverse through the number of siblings given in the path
			{
				if (node.nodeType == 1) //if sibling is an element
				{
					count -= 1;
					if (count == 0)
						break;
				}
			}
			if (count != 0) //incorrect integer
				return null;
		}
	
	return node;
}
