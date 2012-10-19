<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=big5">
</head>

<body>

<form name="myform1" action="class_DBConnectHandler.php" method="post">
	<input type="text" name="user_id" value="1"/>
	<input type="text" name="url" value="http://www.aston.ac.uk"/>
	<input type="text" name="stickyId" value="sticky1"/>
	<input type="text" name="path" value="5/3/4/3"/>
	<input type="text" name="annotation" value="HAHAHAHAHA, it works!"/>
	<input type="text" name="referredText" value="I hope this works!"/>
	<input type="text" name="span_id" value="stick1http://www.aston.ac.uk"/>
	<input type="text" name="mini_icon_id" value="mini1http://www.aston.ac.uk"/>
	<input type="hidden" name="submitted" value="true"/>
	<input type="hidden" name="addSticky" value="true"/>
	<input type="checkbox" name="access_checkbox" onclick = "makePrivate()"/>Private
	<input type="hidden" name="access" value="0"/>
	<input type="Submit" name="submit_button" value="submit" />
</form>
<script type="text/javascript">
var uid = 1;
document.myform1.onsubmit = function(){ document.myform1.user_id.value = 1; alert (document.myform1.user_id.value);}


function makePrivate(){
	document.myform1.access.value = 2;
}
</script>

<form name="myform2" action="class_DBConnectHandler.php" method="post">
	<input type="hidden" name="submitted" value="true"/>
	<input type="text" name="user_id" value="1"/>
	<input type="text" name="stickyId" value="sticky1"/>
	<input type="hidden" name="removeSticky" value="true"/>
	<input type="Submit" name="submit_button" value="Remove" />
</form>
<form name="myform3" action="class_DBConnectHandler.php" method="post">
	<input type="text" name="user_id" value="1"/>
	<input type="text" name="stickyId" value="sticky1"/>
	<input type="text" name="annotation" value="MODIFY WORKS!"/>
	<input type="hidden" name="submitted" value="true"/>
	<input type="hidden" name="modifySticky" value="true"/>
	<input type="Submit" name="submit_button" value="submit" />
</form>

<form name="myform4" action="class_DBConnectHandler.php" method="post">
	<input type="text" name="user_id" value="1"/>
	<input type="text" name="url" value="http://www.aston.ac.uk"/>
	<input type="hidden" name="submitted" value="true"/>
	<input type="hidden" name="showUserStickies" value="true"/>
	<input type="Submit" name="submit_button" value="showMyStickies" />
</form>
<form name="myform5" action="class_DBConnectHandler.php" method="post">
	<input type="text" name="user_id" value="1"/>
	<input type="text" name="url" value="http://www.aston.ac.uk"/>
	<input type="hidden" name="submitted" value="true"/>
	<input type="hidden" name="showAllStickies" value="true"/>
	<input type="Submit" name="submit_button" value="showAllStickies" />
</form>
<form name="myform6" action="class_DBConnectHandler.php" method="post">
	<input type="text" name="user_id" value="1"/>
	<input type="text" name="url" value="http://www.aston.ac.uk"/>
	<input type="hidden" name="submitted" value="true"/>
	<input type="hidden" name="obtainIndex" value="true"/>
	<input type="Submit" name="submit_button" value="obtainIndex" />
</form>
</body>
</html>
