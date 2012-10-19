
dragDropElements = {
	keyHTML: '<a href="#" class="keyLink">#</a>',
	keySpeed: 10, // pixels per keypress event
	initialMouseX: undefined,
	initialMouseY: undefined,
	startX: undefined,
	startY: undefined,
	dXKeys: undefined,
	dYKeys: undefined,
        draggedObject: undefined,
	initElement: function (childElement) {
                childElement.onmousedown = dragDropElements.startDragMouse;
	},
	startDragMouse: function (e) {
		dragDropElements.startDrag(getParentsStickyTitle(e));
		var evt = e || window.event;
		dragDropElements.initialMouseX = evt.clientX;
		dragDropElements.initialMouseY = evt.clientY;
		addEventSimpleNew(document,'mousemove',dragDropElements.dragMouse);
		addEventSimpleNew(document,'mouseup',dragDropElements.releaseElement);
		return false;
	},
	startDragKeys: function () {
		dragDropElements.startDrag(getParentsStickyTitle(e));
		dragDropElements.dXKeys = dragDropElements.dYKeys = 0;
		addEventSimpleNew(document,'keydown',dragDropElements.dragKeys);
		addEventSimpleNew(document,'keypress',dragDropElements.switchKeyEvents);
		this.blur();
		return false;
	},
	startDrag: function (obj) {
		if (dragDropElements.draggedObject)
			dragDropElements.releaseElement();
		dragDropElements.startX = obj.offsetLeft;
		dragDropElements.startY = obj.offsetTop;
		dragDropElements.draggedObject = obj;
		obj.className += ' dragged';
	},
	dragMouse: function (e) {
		var evt = e || window.event;
		var dX = evt.clientX - dragDropElements.initialMouseX;
		var dY = evt.clientY - dragDropElements.initialMouseY;
		dragDropElements.setPosition(dX,dY);
		return false;
	},
	dragKeys: function(e) {
		var evt = e || window.event;
		var key = evt.keyCode;
		switch (key) {
			case 37:	// left
			case 63234:
				dragDropElements.dXKeys -= dragDropElements.keySpeed;
				break;
			case 38:	// up
			case 63232:
				dragDropElements.dYKeys -= dragDropElements.keySpeed;
				break;
			case 39:	// right
			case 63235:
				dragDropElements.dXKeys += dragDropElements.keySpeed;
				break;
			case 40:	// down
			case 63233:
				dragDropElements.dYKeys += dragDropElements.keySpeed;
				break;
			case 13: 	// enter
			case 27: 	// escape
				dragDropElements.releaseElement();
				return false;
			default:
				return true;
		}
		dragDropElements.setPosition(dragDropElements.dXKeys,dragDropElements.dYKeys);
		if (evt.preventDefault) // also solves problem in Saf; keypress part of default ???
			evt.preventDefault();
		return false;
	},
	setPosition: function (dx,dy) {
		dragDropElements.draggedObject.style.left = dragDropElements.startX + dx + 'px';
		dragDropElements.draggedObject.style.top = dragDropElements.startY + dy + 'px';
	},
	switchKeyEvents: function () {

		// for Opera and Safari 1.3

		removeEventSimpleNew(document,'keydown',dragDropElements.dragKeys);
		removeEventSimpleNew(document,'keypress',dragDropElements.switchKeyEvents);
		addEventSimpleNew(document,'keypress',dragDropElements.dragKeys);
	},
	releaseElement: function() {
		removeEventSimpleNew(document,'mousemove',dragDropElements.dragMouse);
		removeEventSimpleNew(document,'mouseup',dragDropElements.releaseElement);
		removeEventSimpleNew(document,'keypress',dragDropElements.dragKeys);
		removeEventSimpleNew(document,'keypress',dragDropElements.switchKeyEvents);
		removeEventSimpleNew(document,'keydown',dragDropElements.dragKeys);
		dragDropElements.draggedObject.className = dragDropElements.draggedObject.className.replace(/dragged/,'');
		dragDropElements.draggedObject = null;
	}
}

function addEventSimpleNew(obj,evt,fn) {
	if (obj.addEventListener)
		obj.addEventListener(evt,fn,false);
	else if (obj.attachEvent)
		obj.attachEvent('on'+evt,fn);
}

function removeEventSimpleNew(obj,evt,fn) {
	if (obj.removeEventListener)
		obj.removeEventListener(evt,fn,false);
	else if (obj.detachEvent)
		obj.detachEvent('on'+evt,fn);
}

function getParentsStickyTitle(e){
    var currentParent = e.currentTarget.parentNode;
    while(currentParent.localName.toString().toLowerCase() != "span"){
        currentParent = currentParent.parentNode;
    }
    return currentParent;
}