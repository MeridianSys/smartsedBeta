<?php
	
	include('../config/db_connect.php');
	require_once('../config/config.inc.php');
	
	$fn = escapeData($_POST['fName']);
	$ln = escapeData($_POST['lName']);
	$cm = escapeData($_POST['company']);
	$po = escapeData($_POST['position']);
	$un = escapeData($_POST['uName']);
	$pw = escapeData($_POST['password']);
	$em = escapeData($_POST['email']);
	$a = md5(uniqid(rand(), true));//Generate activation code
	
//Send email to client
function emailClient($mailAddress,$fn, $ln, $a){
	
	$body  = "Dear $fn $ln,\n\n";
	$body .= "Thank you for registering with Drsmarts bar.";// You must now activate your account before you may log in and gain access to the source codes. To activate your account, please click on the link below: \n\n";
	$body .= "http://www.drsmarts.com/toolbaropera/handlers/activation.php?a=".mysql_insert_id()."&b=$a";
	
	mail($mailAddress, 'Your registration details with Drsmarts bar', $body, 'From: drsmartsnoreply@drsmarts.net');
}

	//Add user
	$query = "INSERT INTO users(username, password, email, first_name, last_name, company, position, reg_date, active) VALUES ('$un', SHA('$pw'), '$em', '$fn', '$ln', '$cm', '$po', NOW(), '$a')";
	$result = @mysql_query($query) OR trigger_error('MySQL query error: '.mysql_error());

	if(mysql_affected_rows() == 1){  //if execution was successful
		emailClient($_POST['email'],$fn, $ln, $a); //can't test without a mail server
		
		//redirect user to thank you page
		$url = 'http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']);
	
		//remove unnecssary backslash
		if((substr($url, -1) == '/') || (substr($url, -1) == '\\')){
			$url = subtr($url, 0, -1);
		}

		$url .= '/thankyou.php?n='.urlencode("$fn $ln").'&em='.urlencode("$em");
	
		header("Location: $url");
		exit();
	}else{
		echo "System ERROR: please contact administrator.";
	}
	$page_title = 'Drsmarts bar - registration';
	include('../includes/header.php');
//Closing database connection
mysql_close();

include('../includes/footer.php');
?>
