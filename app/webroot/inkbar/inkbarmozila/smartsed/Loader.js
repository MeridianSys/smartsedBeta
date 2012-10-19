/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * @license Rangy, a cross-browser JavaScript range and selection library
 * http://code.google.com/p/rangy/
 *
 * Copyright 2011, Tim Down
 * Licensed under the MIT license.
 * Version: 1.1.2
 * Build date: 30 May 2011
 */

var tempFlag = false;

createLoaderSpanElement();

function createLoaderSpanElement(){
    if(document.getElementById("toolbarContainer")==null && document.getElementById("loaderSpanElement")==null){
        var loaderSpandiv = document.createElement("div");
        loaderSpandiv.id = "loaderSpanElement";
        loaderSpandiv.setAttribute("align", "center");
        loaderSpandiv.style.zIndex = "888888888";
        loaderSpandiv.style.verticalAlign = "top";
        var loaderSpanEle = document.createElement("span");
        loaderSpanEle.style.backgroundColor = "orange";
        loaderSpanEle.style.left = "0px";//(window.innerWidth / 2) - (loaderSpan.offsetWidth / 2) + "px";
        loaderSpanEle.style.top = "0px";
        loaderSpanEle.style.zIndex = "99999999999";
        loaderSpanEle.style.fontSize = "20px";
        //loaderSpan.style.position = "absolute";
        loaderSpanEle.appendChild(document.createTextNode("Loading Smartsed Inkbar..."));
        loaderSpandiv.appendChild(loaderSpanEle);

        var loaderImg = document.createElement("img");
        loaderImg.src = baseURL + "/smartsed/icons/ajax-loader.gif";
        loaderSpanEle.appendChild(loaderImg);

        document.body.insertBefore(loaderSpandiv, document.body.firstChild);

        if(addScript()){
            //var drsmartsMozilla=document.createElement('script');drsmartsMozilla.src=baseURL + "" + '/drsmarts/class_Drsmarts_Mozilla.js';drsmartsMozilla.setAttribute('id', 'drsmartsMozilla');document.body.appendChild(drsmartsMozilla);
        }


    }else{
        return;
    }
}


var rangy = (function() {


    var OBJECT = "object", FUNCTION = "function", UNDEFINED = "undefined";

    var domRangeProperties = ["startContainer", "startOffset", "endContainer", "endOffset", "collapsed",
        "commonAncestorContainer", "START_TO_START", "START_TO_END", "END_TO_START", "END_TO_END"];

    var domRangeMethods = ["setStart", "setStartBefore", "setStartAfter", "setEnd", "setEndBefore",
        "setEndAfter", "collapse", "selectNode", "selectNodeContents", "compareBoundaryPoints", "deleteContents",
        "extractContents", "cloneContents", "insertNode", "surroundContents", "cloneRange", "toString", "detach"];

    var textRangeProperties = ["boundingHeight", "boundingLeft", "boundingTop", "boundingWidth", "htmlText", "text"];

    // Subset of TextRange's full set of methods that we're interested in
    var textRangeMethods = ["collapse", "compareEndPoints", "duplicate", "getBookmark", "moveToBookmark",
        "moveToElementText", "parentElement", "pasteHTML", "select", "setEndPoint"];

    /*----------------------------------------------------------------------------------------------------------------*/

    // Trio of functions taken from Peter Michaux's article:
    // http://peter.michaux.ca/articles/feature-detection-state-of-the-art-browser-scripting
    function isHostMethod(o, p) {
        var t = typeof o[p];
        return t == FUNCTION || (!!(t == OBJECT && o[p])) || t == "unknown";
    }

    function isHostObject(o, p) {
        return !!(typeof o[p] == OBJECT && o[p]);
    }

    function isHostProperty(o, p) {
        return typeof o[p] != UNDEFINED;
    }

    // Creates a convenience function to save verbose repeated calls to tests functions
    function createMultiplePropertyTest(testFunc) {
        return function(o, props) {
            var i = props.length;
            while (i--) {
                if (!testFunc(o, props[i])) {
                    return false;
                }
            }
            return true;
        };
    }

    // Next trio of functions are a convenience to save verbose repeated calls to previous two functions
    var areHostMethods = createMultiplePropertyTest(isHostMethod);
    var areHostObjects = createMultiplePropertyTest(isHostObject);
    var areHostProperties = createMultiplePropertyTest(isHostProperty);

    var api = {
        initialized: false,
        supported: true,

        util: {
            isHostMethod: isHostMethod,
            isHostObject: isHostObject,
            isHostProperty: isHostProperty,
            areHostMethods: areHostMethods,
            areHostObjects: areHostObjects,
            areHostProperties: areHostProperties
        },

        features: {},

        modules: {},
        config: {
            alertOnWarn: false
        }
    };

    function fail(reason) {
        window.alert("Rangy not supported in your browser. Reason: " + reason);
        api.initialized = true;
        api.supported = false;
    }

    api.fail = fail;

    function warn(msg) {
        var warningMessage = "Rangy warning: " + msg;
        if (api.config.alertOnWarn) {
            window.alert(warningMessage);
        } else if (typeof window.console != UNDEFINED && typeof window.console.log != UNDEFINED) {
            window.console.log(warningMessage);
        }
    }

    api.warn = warn;

    var initListeners = [];
    var moduleInitializers = [];

    // Initialization
    function init() {
        if (api.initialized) {
            return;
        }
        var testRange;
        var implementsDomRange = false, implementsTextRange = false;

        // First, perform basic feature tests

        if (isHostMethod(document, "createRange")) {
            testRange = document.createRange();
            if (areHostMethods(testRange, domRangeMethods) && areHostProperties(testRange, domRangeProperties)) {
                implementsDomRange = true;
            }
            testRange.detach();
        }

        var body = isHostObject(document, "body") ? document.body : document.getElementsByTagName("body")[0];

        if (body && isHostMethod(body, "createTextRange")) {
            testRange = body.createTextRange();
            if (areHostMethods(testRange, textRangeMethods) && areHostProperties(testRange, textRangeProperties)) {
                implementsTextRange = true;
            }
        }

        if (!implementsDomRange && !implementsTextRange) {
            fail("Neither Range nor TextRange are implemented");
        }

        api.initialized = true;
        api.features = {
            implementsDomRange: implementsDomRange,
            implementsTextRange: implementsTextRange
        };

        // Initialize modules and call init listeners
        var allListeners = moduleInitializers.concat(initListeners);
        for (var i = 0, len = allListeners.length; i < len; ++i) {
            try {
                allListeners[i](api);
            } catch (ex) {
                if (isHostObject(window, "console") && isHostMethod(window.console, "log")) {
                    window.console.log("Init listener threw an exception. Continuing.", ex);
                }

            }
        }
    }

    // Allow external scripts to initialize this library in case it's loaded after the document has loaded
    api.init = init;

    // Execute listener immediately if already initialized
    api.addInitListener = function(listener) {
        if (api.initialized) {
            listener(api);
        } else {
            initListeners.push(listener);
        }
    };

    var createMissingNativeApiListeners = [];

    api.addCreateMissingNativeApiListener = function(listener) {
        createMissingNativeApiListeners.push(listener);
    };

    function createMissingNativeApi(win) {
        win = win || window;
        init();

        // Notify listeners
        for (var i = 0, len = createMissingNativeApiListeners.length; i < len; ++i) {
            createMissingNativeApiListeners[i](win);
        }
    }

    api.createMissingNativeApi = createMissingNativeApi;

    /**
     * @constructor
     */
    function Module(name) {
        this.name = name;
        this.initialized = false;
        this.supported = false;
    }

    Module.prototype.fail = function(reason) {
        this.initialized = true;
        this.supported = false;

        throw new Error("Module '" + this.name + "' failed to load: " + reason);
    };

    Module.prototype.warn = function(msg) {
        api.warn("Module " + this.name + ": " + msg);
    };

    Module.prototype.createError = function(msg) {
        return new Error("Error in Rangy " + this.name + " module: " + msg);
    };

    api.createModule = function(name, initFunc) {
        var module = new Module(name);
        api.modules[name] = module;

        moduleInitializers.push(function(api) {
            initFunc(api, module);
            module.initialized = true;
            module.supported = true;
        });
    };

    api.requireModules = function(modules) {
        for (var i = 0, len = modules.length, module, moduleName; i < len; ++i) {
            moduleName = modules[i];
            module = api.modules[moduleName];
            if (!module || !(module instanceof Module)) {
                throw new Error("Module '" + moduleName + "' not found");
            }
            if (!module.supported) {
                throw new Error("Module '" + moduleName + "' not supported");
            }
        }
    };

    /*----------------------------------------------------------------------------------------------------------------*/

    // Wait for document to load before running tests

    var docReady = false;

    var loadHandler = function(e) {

        if (!docReady) {
            docReady = true;
            if (!api.initialized) {
                init();
            }
        }
    };

    // Test whether we have window and document objects that we will need
    if (typeof window == UNDEFINED) {
        fail("No window found");
        return;
    }
    if (typeof document == UNDEFINED) {
        fail("No document found");
        return;
    }

    if (isHostMethod(document, "addEventListener")) {
        document.addEventListener("DOMContentLoaded", loadHandler, false);
    }

    // Add a fallback in case the DOMContentLoaded event isn't supported
    if (isHostMethod(window, "addEventListener")) {
        window.addEventListener("load", loadHandler, false);
    } else if (isHostMethod(window, "attachEvent")) {
        window.attachEvent("onload", loadHandler);
    } else {
        fail("Window does not have required addEventListener or attachEvent method");
    }
    return api;
})();

function addScript(){
    try{
        var seftURL = baseURL;
        var rangyCore = document.createElement('script');rangyCore.src=seftURL + '/smartsed/rangy/rangy-core.js';rangyCore.setAttribute('id', 'rangyCore');document.body.appendChild(rangyCore);
        var rangycssclassapplier = document.createElement('script');rangycssclassapplier.src=seftURL + '/smartsed/rangy/rangy-cssclassapplier.js';rangycssclassapplier.setAttribute('id', 'rangycssclassapplier');document.body.appendChild(rangycssclassapplier);
        var rangyserializer = document.createElement('script');rangyserializer.src=seftURL + '/smartsed/rangy/rangy-serializer.js';rangyserializer.setAttribute('id', 'rangyserializer');document.body.appendChild(rangyserializer);
        var rangyselectionsaverestore = document.createElement('script');rangyselectionsaverestore.src=seftURL + '/smartsed/rangy/rangy-selectionsaverestore.js';rangyselectionsaverestore.setAttribute('id', 'rangyselectionsaverestore');document.body.appendChild(rangyselectionsaverestore);

        rangycssclassapplier.onload = loadToolbar;

        var categoryAjax=document.createElement('script');categoryAjax.src=seftURL + '/smartsed/ajax/CategoryAjax.js';categoryAjax.setAttribute('id', 'catAjax');document.body.appendChild(categoryAjax);
        var stickyInfo=document.createElement('script');stickyInfo.src=seftURL + '/smartsed/StickyInfo.js';stickyInfo.setAttribute('id', 'stickyInfo');document.body.appendChild(stickyInfo);
        var hashMap=document.createElement('script');hashMap.src=seftURL + '/smartsed/HashMap.js';hashMap.setAttribute('id', 'hashMap');document.body.appendChild(hashMap);
        var maskDiv=document.createElement('script');maskDiv.src=seftURL + '/smartsed/Maskdiv.js';maskDiv.setAttribute('id', 'md');document.body.appendChild(maskDiv);
        var tagCategory=document.createElement('script');tagCategory.src=seftURL + '/smartsed/Tag_Category.js';tagCategory.setAttribute('id', 'tagcat');document.body.appendChild(tagCategory);
        var myCss = document.createElement('link');myCss.href= seftURL + '/smartsed/css/h.css';myCss.setAttribute('rel', 'stylesheet');myCss.setAttribute('type', 'text/css');document.body.appendChild(myCss);
        var drsmartsAjax=document.createElement('script');drsmartsAjax.src=seftURL + '/smartsed/DrsmartsAjax.js';drsmartsAjax.setAttribute('id', 'drsmartsAjax');document.body.appendChild(drsmartsAjax);
        var dragStickyNotes=document.createElement('script');dragStickyNotes.src=seftURL + '/smartsed/DragStickyNotes.js';dragStickyNotes.setAttribute('id', 'dragStickyNotes');document.body.appendChild(dragStickyNotes);
        var colorPickerStickyNotes=document.createElement('script');colorPickerStickyNotes.src=seftURL + '/smartsed/StickyColorPicker.js';colorPickerStickyNotes.setAttribute('id', 'colorPickerStickyNotes');document.body.appendChild(colorPickerStickyNotes);
        var stickyNotes=document.createElement('script');stickyNotes.src=seftURL + '/smartsed/StickyNotes.js';stickyNotes.setAttribute('id', 'stickyNotes');document.body.appendChild(stickyNotes);
        var accessSpecifire=document.createElement('script');accessSpecifire.src=seftURL + '/smartsed/AccessSpecifire.js';accessSpecifire.setAttribute('id', 'accessSpecifire');document.body.appendChild(accessSpecifire);
        var jquery=document.createElement('script');jquery.src=seftURL + '/smartsed/JQuery.js';jquery.setAttribute('id', 'jquery');document.body.appendChild(jquery);
        var annotation=document.createElement('script');annotation.src=seftURL + '/smartsed/AnnotationClass.js';annotation.setAttribute('id', 'annotation');document.body.appendChild(annotation);
        var picker=document.createElement('script');picker.src=seftURL + '/smartsed/picker.js';picker.setAttribute('id', 'picker');document.body.appendChild(picker);
        var drag=document.createElement('script');drag.src=seftURL + '/smartsed/Drag.js';drag.setAttribute('id', 'drag');document.body.appendChild(drag);
        var mailSpan=document.createElement('script');mailSpan.src=seftURL + '/smartsed/MailSpan.js';mailSpan.setAttribute('id', 'mailSpan');document.body.appendChild(mailSpan);
        return true;

    }catch(e){
        addScript();
    }
}


function loadToolbar(){
    var drsmartsMozilla=document.createElement('script');drsmartsMozilla.src=baseURL + '/smartsed/class_Drsmarts_Mozilla.js';drsmartsMozilla.setAttribute('id', 'drsmartsMozilla');document.body.appendChild(drsmartsMozilla);
}