<?php
session_start();
/*
 * Remove all slashes and then apply mysql_real_escape_string.
 */
function escapeData($data){
	$data = stripslashes($data); 
	return mysql_real_escape_string($data);
}

/*
 * Check whether the user has logged in.
 */
function checkLoggedin(){
		session_name('Drsmarts');
		
	//Check if user has logged in
	if(!isset($_SESSION['drsmarts_userid'])){
		//redirect user to thank you page
		$url = 'http://'.$_SERVER['HTTP_HOST'].'/toolbaropera';
	
		//remove unnecssary backslash
		if((substr($url, -1) == '/') || (substr($url, -1) == '\\')){
			$url = subtr($url, 0, -1);
		}

		$url .= '/index.php';
		header("Location: $url");
		exit();
	}
}

/*
 * Check whether the user has logged out.
 */
function checkLoggedOut(){
	session_name('Drsmarts');
		session_start();
	//Check if user hasn't logged out
	if(isset($_SESSION['drsmarts_userid'])){
		//redirect user to thank you page
		$url = 'http://'.$_SERVER['HTTP_HOST'].'/toolbaropera';
	
		//remove unnecssary backslash
		if((substr($url, -1) == '/') || (substr($url, -1) == '\\')){
			$url = subtr($url, 0, -1);
		}

		$url .= '/index.php';
		header("Location: $url");
		exit();
	}
}
?>
