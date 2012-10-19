var strimg='<div style="float:left;" ><img src="'+webURL+'/img/confirm.jpg" height="40px" width="40px"></div>';
//alert('"'+webURL+'/img/confirm.jpg"');
function isEmpty(field,msg)
{
	if(document.getElementById(field).value=='')
	{
		alert(msg);
		document.getElementById(field).focus();
		return false;
	}
	return true;
}
function paginate(pageurl,sortby,pageno,pagecount,divid,id)
{
	if(id)
	{
		url = webURL+pageurl+sortby+'/'+pageno+'/'+pagecount+'/'+id;
	}else{
		url = webURL+pageurl+sortby+'/'+pageno+'/'+pagecount;
	}
	//divid = 'userarea';
	$.ajax(
	{
		type: "POST",
		url: url,
		data:'',
		success:function(msg)
		{
			document.getElementById(divid).innerHTML = msg;
		}
	});  
}
function adsearchonstatus(status)
{
	if(!status)
	{
		url = webURL+'admins/advertisers/1';
	}else
	{
		url = webURL+'admins/adsearchonstatus/'+status;
	}
	divid = 'advertiserarea';
	$.ajax(
	{
		type: "POST",
		url: url,
		data:'',
		success:function(msg)
		{
			document.getElementById(divid).innerHTML = msg;
		}
	});  

}
function searchonstatus(status,divid,action)
{
	if(!status)
	{
		url = webURL+'admins/home/1';
	}else
	{
		url = webURL+'admins/'+action+'/'+status;
	}
	
	//divid = 'userarea';
	$.ajax(
	{
		type: "POST",
		url: url,
		data:'',
		success:function(msg)
		{
			document.getElementById(divid).innerHTML = msg;
		}
	});  
}
function confirm_change(status)
{
	c = confirm('Are you sure you want to Make '+status+'?');
	if(!c){
		return false;
	}
}
function confirmation()
{
	var r=confirm("You are sure ? And want to delete the user ");
	if (r==true)
	  {
	 return true;
	  }
	else
	  {
	  return false;
	  }
}
function confirm_locationdelete()
{
	
	c = confirm('Are you sure you want to Detete this location');
	if(!c){
		return false;
	}
}

function confirm_emaildelete(){
	c = confirm('Are you sure you want to Detete this Template');
	if(!c){
		return false;
	}
	
}

function confirm_config_delete()
{
	c = confirm('Are you sure you want to delete this config variable?');
	if(!c){
		return false;
	}
}
function confirm_eventtype_delete()
{
	cd = confirm('Are you sure you want to delete this Event Type variable?');
	if(!cd){
		return false;
	}
}



function confirm_plan_delete()
{
	c = confirm('Are you sure you want to delete this Plan ?');
	if(!c){
		return false;
	}
}
function confirm_feedback_delete()
{
	c = confirm('Are you sure you want to delete this Feedback ?');
	if(!c){
		return false;
	}
}

function confirmdelete(msg)
{
	return confirm(msg);
}
function confirm_delete()
{
	c = confirm('Are you sure you want to Remove this vendor ?');
	if(!c){
		return false;
	}
}
function validatefrmemailtemplates()
{
	flag = true;
	if(document.getElementById('subject').value == 'Title')
	{
		alert('Subject can not be blank');
		return false;
	}
	strRE = new RegExp( );
	strRE.compile( '^[\s ]*$', 'gi' );
	if(strRE.test( $("#subject").val() ) == true){
		alert('Subject can not be blank');
		return false;
	}
	if(tinyMCE.get('message').getContent() == "")
	{
		alert('Message body can not be blank');
		return false;
	}
}
function validatefrmconfig()
{
	flag = true;
	flag = isEmpty('name','Name field can not be blank.');
	if(flag)
		flag = isEmpty('value','Value field can not be blank.');
	return flag;

	
}
function getStates(cid)
{
	url = webURL+'/admins/get_state_list/'+cid;
	$.ajax(
			{
				type: "POST",
				url: url,
				data:'',
				success:function(msg)
				{
					document.getElementById('idstate').innerHTML = msg;
					if(!cid)
						getCities('');
				}
			});  
}
function getCities(sid)
{
	url = webURL+'/admins/get_city_list/'+sid;
	$.ajax(
			{
				type: "POST",
				url: url,
				data:'',
				success:function(msg)
				{
					document.getElementById('idcity').innerHTML = msg;
				}
			});  
	
}
function selectallcards(id)
{
	var checkboxes = document.frmcard.chkcards;
	document.getElementById("chkcards").checked=true;
	if(id)
	{
		if(!checkboxes[0])
		{
			if(document.getElementById("checkallid").checked==false)
			{
				document.getElementById("chkcards").checked=false;
			}
			//checkboxes.checked==true;
		}else{
			for(i = 0; i < checkboxes.length; i++)
				{
					checkboxes[i].checked=true;
				}
		}
	}
	else
	{
		if(!checkboxes[0])
		{
			if(document.getElementById("checkallid").checked==false)
			{
				document.getElementById("chkcards").checked=false;
			}
			//checkboxes.checked==false;
		}else{
			for(i = 0; i < checkboxes.length; i++)
				{
					checkboxes[i].checked=false;
				}
		}
	}
}

function deselectcheckallcards()
{
	
	var checkboxes = document.frmcard.chkcards;
	document.getElementById("checkallid").checked=true;

	if(!checkboxes[0])
	{
		if(checkboxes.checked==false)
		{
			document.getElementById("checkallid").checked=false;
		}
	}else{
		for(i = 0; i < checkboxes.length; i++)
			{
				if(checkboxes[i].checked==false)
				{
					document.getElementById("checkallid").checked=false;
				}
			}
	}
}

function validateCheckbox(name)
{
	var chObj	=	document.getElementsByName(name);
	var result	=	false;	
	for(var i=0;i<chObj.length;i++)
	{
		if(chObj[i].checked)
		{
		  result=true;
		  break;
		}
	}
	return result;
}	
function delete_payrate(id)
{
	document.getElementById('payrateids').value = id;
	str=strimg+'<span class="error_popup">';
	str+='Are you sure you want to delete this payrate?';
	str+='</span>';
	$('#confirm_dialog').html(str);
	$('#confirm_dialog').dialog('open');
	return false;
			
}
function confirm_payrate_delete()
{
	var flag=true;
	var checkboxes = document.frmpayrate.chkpayrates;
	// alert(checkboxes[0]);return false;
	var temp="";
	if(!checkboxes[0])
	{
		if(checkboxes.checked==true)
		{
			temp=temp+checkboxes.value+",";
			flag=false;
		}
	}else{
		
		for (i = 0; i < checkboxes.length; i++) {
			if(checkboxes[i].checked==true)
			{
				temp=temp+checkboxes[i].value+",";
				//checkboxes[i].checked=false;
				flag=false;
			}
		}
	}
	
	if(flag)
	{
		str=strimg+'<span class="error_popup">Select newsletter to delete</span>';
		$('#dialog').html(str);
		$('#dialog').dialog('open');
		return false;
		//alert('Select payrate to delete');
		//return false;
	}
	else
	{ 	
		document.getElementById('payrateids').value = temp;
		str=strimg+'<span class="error_popup">';
		str+= 'Are you sure you want to delete these newsletters?';
		str+='</span>';
		$('#delete_selected_dialog').html(str);
		$('#delete_selected_dialog').dialog('open');
		//if(confirm('Are you sure you want to remove these payrates ?'))
		//{
		//	document.frmpayrate.submit();
		//}else{
		//	return false;
		//}
	}
	
}
function selectallpayrates(id)
{
	var checkboxes = document.frmpayrate.chkpayrates;
	document.getElementById("chkpayrates").checked=true;
	if(id)
	{
		if(!checkboxes[0])
		{
			if(document.getElementById("checkallid").checked==false)
			{
				document.getElementById("chkpayrates").checked=false;
			}
			//checkboxes.checked==true;
		}else{
			for(i = 0; i < checkboxes.length; i++)
				{
					checkboxes[i].checked=true;
				}
		}
	}
	else
	{
		if(!checkboxes[0])
		{
			if(document.getElementById("checkallid").checked==false)
			{
				document.getElementById("chkleaves").checked=false;
			}
			//checkboxes.checked==false;
		}else{
			for(i = 0; i < checkboxes.length; i++)
				{
					checkboxes[i].checked=false;
				}
		}
	}
}

function deselectcheckallpayrates()
{
	
	var checkboxes = document.frmpayrate.chkpayrates;
	document.getElementById("checkallid").checked=true;

	if(!checkboxes[0])
	{
		if(checkboxes.checked==false)
		{
			document.getElementById("checkallid").checked=false;
		}
	}else{
		for(i = 0; i < checkboxes.length; i++)
			{
				if(checkboxes[i].checked==false)
				{
					document.getElementById("checkallid").checked=false;
				}
			}
	}
}


