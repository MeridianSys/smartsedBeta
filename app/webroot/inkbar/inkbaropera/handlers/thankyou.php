<?php
	$page_title = 'Thank you for registering with Drsmarts bar';
	include('../includes/header.php');
	require_once('../config/config.inc.php');
	
	if((!empty($_GET['n'])) && (!empty($_GET['em']))){
		$n = $_GET['n'];
		$em = $_GET['em'];
		$m = "<p>An email has been sent to the email address at <span id='user'>$em</span>. Please check and follow the instructions in the email to confirm your registration.<p>";
		echo "Thank you for registering <span id='user'>$n</span>! You may now log in.";
	}else{
		echo "System ERROR: please contact administrator";
	}

	include('../includes/footer.php');
?>

