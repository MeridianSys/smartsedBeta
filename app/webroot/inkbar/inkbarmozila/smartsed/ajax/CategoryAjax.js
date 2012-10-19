function makePostRequestforCategory(){
    $.ajax({
        type: 'POST',
        url: baseURL + '/users/CategoryLoader.php',
        
        beforeSend:function(){
              var savingDiv = document.createElement("div");
//                var imgSaving = document.createElement("img");
//                imgSaving.src = baseURL +'/drsmarts/icons/loader.gif';
//                imgSaving.setAttribute("alt", "Saving...");
//                imgSaving.setAttribute("id", "savingImgID");

                savingDiv.setAttribute("id", "savingDivID");
                //savingDiv.appendChild(imgSaving);
                savingDiv.style.zIndex = "9999999999";
                savingDiv.style.position = "fixed";
                savingDiv.style.left = "0px";
                savingDiv.style.top = "0px";
                //savingDiv.style.border = "2px #414141 double";
                savingDiv.style.backgroundColor = "white";
                savingDiv.style.align = "center";
                savingDiv.innerHTML = '<div align="center"><img  src="'+baseURL+'/smartsed/icons/loader_1.gif"/></div>';

                document.body.insertBefore(savingDiv, document.body.firstChild);


                putinCenter(document.getElementById("savingDivID"));
        },

        success:function(data){
            document.body.removeChild(document.getElementById("savingDivID"));
            CategoryInnerHtml(data);
        },
        error:function(){
           
        }
    });
}