function makePostRequestforCategory(){

   /* $.getJSON(baseURL + '/users/CategoryLoader.php?'+ "jsoncallback=?" , function(data){
        alert(data.item1);
        alert(data.item2);
        alert(data.item3);
    });*/
 $.ajax({
  dataType: 'jsonp',
  jsonp: 'jsonp_callback',
  url: baseURL + '/users/CategoryLoader.php',
  success: function (data) {
        //document.body.removeChild(document.getElementById("savingDivID"));
        CategoryInnerHtml(data);
  },
  error:function(xhr, textStatus, errorThrown){
       alert(textStatus);
  }
});
   
//    $.ajax({
//        datatype:'jsonp',
//        jsonp: 'jsonp_callback',
//        url: baseURL + '/users/CategoryLoader.php',
//
//        beforeSend:function(){
//              var savingDiv = document.createElement("div");
//
//                savingDiv.setAttribute("id", "savingDivID");
//                //savingDiv.appendChild(imgSaving);
//                savingDiv.style.zIndex = "9999999999";
//                savingDiv.style.position = "fixed";
//                savingDiv.style.left = "0px";
//                savingDiv.style.top = "0px";
//                //savingDiv.style.border = "2px #414141 double";
//                savingDiv.style.backgroundColor = "white";
//                savingDiv.style.align = "center";
//                savingDiv.innerHTML = '<div align="center"><img  src="'+baseURL+'/drsmartsOpera/icons/loader_1.gif"/></div>';
//
//                document.body.insertBefore(savingDiv, document.body.firstChild);
//
//
//                putinCenter(document.getElementById("savingDivID"));
//        },
//
//        success:function(data){
//            document.body.removeChild(document.getElementById("savingDivID"));
//            CategoryInnerHtml(data);
//        },
//        error:function(xhr, textStatus, errorThrown){
//           alert(errorThrown);
//        }
//    });
    
}

function jsonp_callback(data){
        //alert('dddd');
       // alert(data);
    }