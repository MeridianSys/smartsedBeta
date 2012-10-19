/**
  * class_StickyNote.js
  *
  * This is a superclass has the properties common to all sticky note objects.
  *
  * @author: Nikesh kumar
  * @version: 12/07/2011
  */
  
/**
  * This is the function constructor for the class which should contain all properties and 
  * functions all sticky note subclasses should have.
  *
  * @param stickyId - the id of the sticky object
  * @param spanId - the span tag the sticky and the minimized note icon is attached to
  * @param minimized_icon_Id - the id of the minimized note icon this sticky is attached to
  * @param authorName - the author of the annotation
  * @param selectedText - the text the sticky refers to
  * @param annotations - the annotations
  * @param newSticky - whether to draw a minimized note icon on finish or not
  */



function StickyNote(xmlHttpObj, userID, pageURL, stickyID, spanID, mini_icon_ID, authorName, selectedText, annotations){
    //define and assign instance properties/ state variables, making each instance different
    this.userid = userID;
    this.pageurl = pageURL;
    this.stickyId = stickyID;
    this.spanId = spanID;
    this.mini_icon_Id = mini_icon_ID;
    this.author = authorName,
    this.referredText = selectedText;
    this.comments = annotations;
    this.xmlHttpObj = xmlHttpObj;
}
 
 /**
   *Creates a div as the sticky note frame.
   *
   * @return stickyNote - the sticky note frame <div>
   */
StickyNote.prototype.createStickyNoteFrame = function(stickyID){
    var stickyNote = document.createElement( "div" );
    stickyNote.setAttribute("id", stickyID);
    stickyNote.setAttribute("class", "stickyBody");
    stickyNote.style.position = "absolute";
    //stickyNote.style.position = "relative";
    stickyNote.style.border = "solid";
    stickyNote.style.borderColor = "#ADADAD";
    stickyNote.style.width = "370px";
    stickyNote.style.backgroundColor = "#F4F965";
    stickyNote.style.fontFamily = "Verdana";
    stickyNote.style.fontWeight = "normal";
    stickyNote.style.fontStyle = "normal";
    stickyNote.style.color = "#000000";
    stickyNote.style.fontSize = "10px";
    stickyNote.style.padding = "0px";
    stickyNote.style.margin = "0px";
    stickyNote.style.textAlign = "left";
    stickyNote.style.zIndex = "10000";
   
    return stickyNote;
}

/**
   * Creates a textarea for annotations.
   *
   * @return textPane - the textarea
   */
StickyNote.prototype.createTextArea = function(){
//the textpane
    var textPane = document.createElement("textarea");
    textPane.value = "Enter here...";
    textPane.setAttribute("class", "textArea");
    textPane.style.position = 'absolute';
    textPane.style.fontFamily = "Verdana";
    textPane.style.fontWeight = "normal";
    textPane.style.fontStyle = "normal";
    textPane.style.color = "#000000";
    textPane.style.fontSize = "10px";
    textPane.style.backgroundColor = "#FFFFFF";
    textPane.style.width = "366px";
    textPane.style.height = "140px";
    textPane.style.textAlign = "left";
    textPane.style.padding = "0px";
    textPane.style.margin = "0px";
    return textPane;
}



 /**
   * Creates a div as to enter tile, sticky id, the minimize and delete funcitons.
   *
   * @return textBar - the <div>
   */
StickyNote.prototype.createTextField = function(){

    var textbar = document.createElement("input");
    textbar.type = "text";
    textbar.name = "tit";
    textbar.value = "Enter title...";
    textbar.style.backgroundColor = "#FFFFFF";
    //titlebar.style.height = "20px";
    textbar.style.width = "270px";
    textbar.style.textAlign = "left";
    textbar.style.padding = "0px";
    textbar.style.margin = "0px";
    return textbar;
}
	 
  /**
   * Creates a div as to display author name, sticky id, the minimize and delete funcitons.
   *
   * @return titleBar - the <div>
   */
StickyNote.prototype.createTitlebar = function(){

    var titlebar = document.createElement("div");
    titlebar.style.backgroundColor = "#F4F965";
    //titlebar.style.height = "20px";
    titlebar.style.width = "370px";
    titlebar.style.textAlign = "right";
    titlebar.style.padding = "0px";
    titlebar.style.margin = "0px";
    titlebar.style.backgroundColor = "#F4F965";
    return titlebar;
}
 
 /**
   * Creates a div to display the date the sticky was created.
   *
   * @return stickyIdBar - the <div> which displays the sticky id
   */
StickyNote.prototype.createDateCreatedBar = function(date){
    var stickyIdBar = document.createElement("div");
    var stickyIdPara = document.createElement("p");
    stickyIdPara.appendChild(document.createTextNode("Created: " + date));
    stickyIdPara.style.width = "360px";
    stickyIdPara.style.padding = "5px";
    stickyIdBar.appendChild(stickyIdPara);
    stickyIdBar.setAttribute("class", "stickyIdBar");
    stickyIdBar.style.textAlign = "left";
    stickyIdBar.style.width = "370px";
    stickyIdBar.style.color = "#000000";
    stickyIdBar.style.border = "solid";
    stickyIdBar.style.borderColor = "#ADADAD";
    stickyIdBar.style.borderLeft = "0px";
    stickyIdBar.style.borderRight = "0px";
    stickyIdBar.style.borderBottom = "0px";
    stickyIdBar.style.fontFamily = "Verdana";
    stickyIdBar.style.fontSize = "10px";
    stickyIdBar.style.fontWeight = "bold";
    return stickyIdBar;
}

 /**
   * Creates a div to display the date the sticky was created.
   *
   * @return stickyIdBar - the <div> which displays the sticky id
   */
StickyNote.prototype.createDateModifiedBar = function(date){
    var stickyIdBar = document.createElement("div");
    var stickyIdPara = document.createElement("p");
    stickyIdPara.appendChild(document.createTextNode("Modified: " + date));
    stickyIdPara.style.width = "360px";
    stickyIdPara.style.padding = "5px";
    stickyIdBar.appendChild(stickyIdPara);
    stickyIdBar.setAttribute("class", "stickyIdBar");
    stickyIdBar.style.textAlign = "left";
    stickyIdBar.style.width = "370px";
    stickyIdBar.style.color = "#000000";
    stickyIdBar.style.border = "solid";
    stickyIdBar.style.borderColor = "#ADADAD";
    stickyIdBar.style.borderLeft = "0px";
    stickyIdBar.style.borderRight = "0px";
    stickyIdBar.style.borderBottom = "0px";
    stickyIdBar.style.fontFamily = "Verdana";
    stickyIdBar.style.fontSize = "10px";
    stickyIdBar.style.fontWeight = "bold";
    return stickyIdBar;
}
 
  /**
   * Creates a div to display the author of the note.
   *
   * @return authorBar - the <div> which displays the author
   */
StickyNote.prototype.createAuthorBar = function(authorName){
	 
    var authorBar = document.createElement("div");
    var authorPara = document.createElement("p");
    authorPara.appendChild(document.createTextNode("Author: " + authorName));
    authorBar.appendChild(authorPara);
    authorPara.style.width = "360px";
    authorPara.style.padding = "5px";
    authorBar.setAttribute("class", "authorBar");
    authorBar.style.textAlign = "left";
    authorBar.style.width = "370px";
    authorBar.style.color = "#000000";
    // authorBar.style.height = "30px";
    // authorBar.style.padding = "5px";
    authorBar.style.border = "solid";
    authorBar.style.borderColor = "#ADADAD";
    authorBar.style.borderLeft = "0px";
    authorBar.style.borderRight = "0px";
    authorBar.style.borderBottom = "0px";
    authorBar.style.fontFamily = "Verdana";
    authorBar.style.fontSize = "10px";
    authorBar.style.fontWeight = "bold"
    return authorBar;
}



 /**
   * Creates a div to display the title of the note.
   *
   * @return titleBar - the <div> which displays the title
   */
 StickyNote.prototype.createTitleBar = function(){

    var titleBar = document.createElement("div");
    var titlePara = document.createElement("p");
    titlePara.appendChild(document.createTextNode("Title: "));
    titlePara.appendChild(StickyNote.prototype.createTextField());
    titleBar.appendChild(titlePara);
    titlePara.style.width = "360px";
    titlePara.style.padding = "5px";
    titleBar.setAttribute("class", "authorBar");
    titleBar.style.textAlign = "left";
    titleBar.style.width = "370px";
    titleBar.style.color = "#000000";
    // authorBar.style.height = "30px";
    // authorBar.style.padding = "5px";
    titleBar.style.border = "solid";
    titleBar.style.borderColor = "#ADADAD";
    titleBar.style.borderLeft = "0px";
    titleBar.style.borderRight = "0px";
    titleBar.style.borderBottom = "0px";
    titleBar.style.fontFamily = "Verdana";
    titleBar.style.fontSize = "10px";
    titleBar.style.fontWeight = "bold"
    return titleBar;
}

     /**
   * Creates a div to display the URL of the note.
   *
   * @return URLBar - the <div> which displays the URL
   */
 StickyNote.prototype.createURLbar = function(URL){

    var urlBar = document.createElement("div");
    var urlPara = document.createElement("p");
    urlPara.appendChild(document.createTextNode("URL: " + URL));
    urlPara.style.width = "360px";
    urlPara.style.padding = "5px";
    urlPara.style.fontFamily = "Verdana";
    urlPara.style.fontSize = "10px";
    urlPara.style.fontWeight = "bold";
    urlBar.appendChild(urlPara);

    urlBar.setAttribute("class", "authorBar");
    urlBar.style.textAlign = "left";
    urlBar.style.width = "370px";
    urlBar.style.color = "#000000";
    // authorBar.style.height = "30px";
    // authorBar.style.padding = "5px";
    urlBar.style.border = "solid";
    urlBar.style.borderColor = "#ADADAD";
    urlBar.style.borderLeft = "0px";
    urlBar.style.borderRight = "0px";
    urlBar.style.borderBottom = "0px";
    urlBar.style.fontFamily = "Verdana";
    urlBar.style.fontSize = "10px";
    /*urlBar.style.fontWeight = "bold";*/
    return urlBar;
}
	 
  /**
   * Creates a div to display the text the sticky note is referring to.
   *
   * @return newContainer - the <div> which displays the texts
   */
StickyNote.prototype.createNewContainer = function(selectedText){
    var referingToDiv = document.createElement("div");
    var referingToPara = document.createElement("p");
    referingToPara.appendChild(document.createTextNode("Refering to:"));
    referingToPara.style.width = "360px";
    referingToPara.style.padding = "5px";
    referingToDiv.appendChild(referingToPara);
    referingToDiv.style.fontFamily = "Verdana";
    referingToDiv.style.fontSize = "10px";
    referingToDiv.style.color = "#FF0000";
    referingToDiv.style.fontWeight = "bold";
    referingToDiv.style.backgroundColor = "#F4F965";
    referingToDiv.style.width = "370px";
    // referingToDiv.style.padding = "5px";
    var selectedTextDiv = document.createElement("div");
    var selectedTextPara = document.createElement("p");
    selectedTextPara.appendChild(document.createTextNode(selectedText));
    selectedTextDiv.appendChild(selectedTextPara);
    selectedTextPara.style.width = "360px";
    selectedTextPara.style.padding = "5px";
    selectedTextDiv.style.fontFamily = "Verdana";
    selectedTextDiv.style.fontStyle = "italic";
    selectedTextDiv.style.color = "#434343";
    selectedTextDiv.style.fontSize = "10px";
    selectedTextDiv.style.backgroundColor = "#F4F965";
    selectedTextDiv.style.width = "370px";
    //selectedTextDiv.style.padding = "5px";
    var newContainer = document.createElement("div");
    newContainer.appendChild(referingToDiv);
    newContainer.appendChild(selectedTextDiv);
    newContainer.style.border = "solid";
    newContainer.style.borderColor = "#ADADAD";
    newContainer.style.borderLeft = "0px";
    newContainer.style.borderRight = "0px";
    newContainer.style.marginBottom = "10px";
    newContainer.style.width = "370px";
    return newContainer;
}
 
 /**
   * This method attaches a mini note icon to the start of the string this note is
   * referred to.
   */
StickyNote.prototype.attachMiniIcon = function(stickyId, spanId, minimized_icon_ID){
    var elementContainer = document.getElementById(spanId);
    if(elementContainer != null){
         var minimized_note_icon = create_img(baseURL + "/smartsed/icons/minimized_note_icon.gif");
         minimized_note_icon.setAttribute("id", minimized_icon_ID);
         minimized_note_icon.style.margin = "3px";
         elementContainer.appendChild(minimized_note_icon);
         minimized_note_icon.onmouseover = function() {StickyNote.prototype.showSticky(stickyId);};
    }
}

 /**
   * This method attaches a mini note icon denoting the user's annotations to the start of the string this note is
   * referred to.
   */
StickyNote.prototype.attachUserMiniIcon = function(stickyId, spanId, minimized_icon_ID){

    var elementContainer = document.getElementById(spanId);
    //another attempt to override CSS styles
    elementContainer.style.textAlign = "left";
    elementContainer.style.width = "0px";
    elementContainer.style.height = "0px";
    elementContainer.style.color = "#000000";
    elementContainer.style.border = "none";
    elementContainer.style.borderColor = "#ADADAD";
    elementContainer.style.borderLeft = "0px";
    elementContainer.style.borderRight = "0px";
    elementContainer.style.borderBottom = "0px";
    elementContainer.style.fontFamily = "Verdana";
    elementContainer.style.fontSize = "10px";
    elementContainer.style.fontWeight = "normal";
    if(elementContainer != null){
         var minimized_note_icon = create_img(baseURL + "/smartsed/icons/user_minimized_note_icon.gif");
         minimized_note_icon.setAttribute("id", minimized_icon_ID);
         minimized_note_icon.alt = "Annotation made using Drsmarts";
         minimized_note_icon.style.margin = "3px";
         elementContainer.appendChild(minimized_note_icon);
         minimized_note_icon.onmouseover = function() {StickyNote.prototype.showSticky(stickyId);};
    }
}

 /**
   * This method removes a mini note icon from the start of the string this note is
   * referred to.
   */
StickyNote.prototype.removeMiniIcon = function(spanId){
    var elementContainer = document.getElementById(spanId);
    if(elementContainer != null){
         document.body.removeChild(elementContainer);
    }
}
   
 /**
   * This method attaches a sticky note to a span tag.
   */
StickyNote.prototype.attachSticky = function(spanID, sNote){
    var spanNode = document.getElementById(spanID);
    if(spanNode != null){
        spanNode.appendChild(sNote);
    }
}
   
 /**
   * This method removes a sticky note pane. Used for switching pane purposes only.
   */
StickyNote.prototype.detachSticky = function(stickyID, spanID){
    var stickyElement = document.getElementById(spanID);
    if(stickyElement != null){
        stickyElement.removeChild(document.getElementById(stickyID));
    }
}

 /**
   * This method makes a sticky note visible
   */
StickyNote.prototype.showSticky = function(searchId){
    var foundSticky = document.getElementById(searchId);
    if(foundSticky != null){
        foundSticky.style.visibility = "visible";
    }
}

 /**
   * This method makes asticky note disappear from screen.
   */
StickyNote.prototype.hideSticky = function(searchId){
    var foundSticky = document.getElementById(searchId);
    foundSticky.style.visibility = "hidden";
}
 
 /**
   * This method removes sticky and minimized icon from browser.
   */
StickyNote.prototype.deleteSticky = function(stickyId, spanId, minimized_icon_ID){
    var stickyElement = document.getElementById(spanId);
    stickyElement.removeChild(document.getElementById(stickyId));
    if(document.getElementById(minimized_icon_ID) != null){
        stickyElement.removeChild(document.getElementById(minimized_icon_ID));
    }
}
		
	   
 /**
    * This method returns the id of the sticky note.
    */
StickyNote.prototype.getStickyId = function(){return this.stickyId;};
   
   /**
   * This method returns the id of the span tag the sticky note object belongs to.
   */
StickyNote.prototype.getSpanId = function(){return this.spanId;};
   
   /**
   * This method returns the id of the mini icon the sticky noteis attached to.
   */
StickyNote.prototype.getMiniIconId = function(){return this.mini_icon_Id;};
   
   /**
   * This method returns the author of the annotations on the sticky note.
   */
StickyNote.prototype.getAuthor = function(){return this.author;};
   
   /**
   * This method returns the text referred to by the sticky note.
   */
StickyNote.prototype.getReferredText = function(){return this.referredText;};
   
   /**
   * This method returns the comments/annotations on the sticky note.
   */
StickyNote.prototype.getComments = function(){return this.comments;};
   
   /**
   * This method returns string representation of the sticky note.
   */
StickyNote.prototype.toString = function(){ 
    var id = "The id of this note is: " + this.stickyId;
    var span = "The span tag this note belongs to is: " +  this.spanId;
    var icon = "The mini note icon this note belongs to is: " + this.mini_icon_Id;
    var author_name = "The comments are made by: " + this.author;
    var rText = "The texts referred to by this note is: " + this.referredText;
    var c = "The comments are: " + this.comments;

    return id + "\n" + span + "\n" + icon + "\n" + author_name + "\n" + rText + "\n" + c + "\n";
}
   