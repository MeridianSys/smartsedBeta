/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

function Annotation(selectionSeriaIDTemp, spanIDTemp, colorCodeTemp, rangyTemp){
    this.selectionSeriaID = selectionSeriaIDTemp;
    this.spanID = spanIDTemp;
    this.colorCode = colorCodeTemp;
    this.rangy = rangyTemp;
}

Annotation.prototype.getSelectionSeriaID = function(){
    return selectionSeriaID;
}

Annotation.prototype.setSelectionSeriaID = function(selectionSeriaIDTemp){
    selectionSeriaID = selectionSeriaIDTemp;
}

Annotation.prototype.getSpanID = function(){
    return spanID;
}

Annotation.prototype.setSpanID = function(spanIDTemp){
    spanID = spanIDTemp;
}

Annotation.prototype.getSpanID = function(){
    return spanID;
}

Annotation.prototype.setColorCode = function(colorCodeTemp){
    colorCode = colorCodeTemp;
}

Annotation.prototype.getColorCode = function(){
    return colorCode;
}

Annotation.prototype.setRangy = function(rangyTemp){
    rangy = rangyTemp;
}

Annotation.prototype.getRangy = function(){
    return rangy;
}

