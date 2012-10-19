<?php
	$page_title = 'Account activation';
	include('../includes/header.php');
	require_once('../config/config.inc.php');

	//Check to see if both values are provided and record them.
	if(isset($_GET['a'])){
		$id = (int)$_GET['a'];
	}else{
		$id = 0;
	} 
	
	if(isset($_GET['b'])){
		$ac = $_GET['b'];
	}else{
		$ac = 0;
	} 

	//Perform check to see if they match the values on record.
	if(($id > 0) && (strlen($ac) == 32)){
		require_once('../config/db_connect.php');

		$query = "UPDATE users SET active=NULL WHERE (user_id=$id AND active='".escapeData($ac)."') LIMIT 1";
                //echo $query;
		$result = mysql_query($query) OR die('Error: '.mysql_error());

		if(mysql_affected_rows() == 1){
			echo "<p>Congratulation! Your account has been activated successfully. You may now log in. </p>";
		}else{
			echo "<p>Your account could not be activated. Please re-check the link or contact administrator for assistance.</p>";
		}
		
		//Close mysql connection
		mysql_close();
	} else{
		echo "<p>Your account could not be activated. Please re-check the link or contact administrator for assistance.</p>";
	}

	include('../includes/footer.php');
?>
	
</body>
</html>
