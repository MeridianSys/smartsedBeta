//For Selecting/ deselecting check boxed
function selectDeselect(field, isCheck) {
	var boxes = document.getElementsByName(field);
	var boxes_checked = anyChecked();

	if(isCheck){
		if(document.getElementsByName(isCheck)[0].checked) setChecks(true);
		else setChecks(false);			
	}else{
		if(!boxes_checked) setChecks(true);
		else setChecks(false);
	}

	function setChecks( setting ) {
		for( var i=0; i < boxes.length; i++ ) {
			boxes[ i ].checked = setting;
		}
	}
	function anyChecked() {
		for( var i=0; i < boxes.length; i++ ) {
			if( boxes[i].checked == true) {
				return (true);
			} 
		}
		return (false);
	}
}

//To check wheather user have selected box or not
function anyChecked(field) {
	var boxes = document.getElementsByName(field);
	for( var i=0; i < boxes.length; i++ ) {
		if( boxes[i].checked == true) {
			return (true);
		} 
	}
	return (false);
}

function checkSelection(field, name) { 
	ifAnyChecked(document.getElementsByName(field), name)
}

function ifAnyChecked(obj, name) {
	var count = 0;
	for( var i=0; i < obj.length; i++ ) {
            
		if( obj[i].checked == false) {
			count++;
		} 
	}

	if(count > 0){ 
            	if(document.getElementById(name).checked){
			document.getElementById(name).checked = false;
		}
	}  else {
		if(document.getElementById(name).checked == false){
			document.getElementById(name).checked = true;
		}
	}
}

function delConfirm(field)
{
	 if(!anyChecked(field))
	 {
	 	alert('Please select record(s)');
	 	return (false);
	 }
	 else{
		con  = confirm("Are you sure you want to perform the action?");
		return con;
	 }
}

function newsletterAndsubScribersConfirm(field)
{
	 if(!anyChecked(field))
	 {
	 	alert('Please select subscriber(s)!');
	 	return (false);
	 }
	 else{
	 	selectNewsletter = document.getElementById('SubscriberNewsletter').options[document.getElementById('SubscriberNewsletter').selectedIndex].value;
	 	
	 	
	 	
	 	if(selectNewsletter < 1 ){
	 		alert("Please select newslette !");
	 		document.getElementById('SubscriberNewsletter').focus();
	 		return (false);
	 	}else{
	 		
	 		selectedEmails = countChecked(field);
	 		maxEmail = document.getElementById('SubscriberMaxEmail').value;
	 		
	 		if(selectedEmails > maxEmail ){
	 		  alert("You can't select more than "+maxEmail+" !");
	 		  return (false);
	 		}
	 	}
	 	
		return (true);
	 }

}
	
	//To count  selected box
function countChecked(field) {
	var boxes = document.getElementsByName(field);
	count = 0;
	for( var i=0; i < boxes.length; i++ ) {
		if( boxes[i].checked == true) {
			count++;;
		} 
	}
	return (count);
}