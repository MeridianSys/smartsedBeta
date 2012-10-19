<?php
		//Start new session
		//session_name('Drsmarts');
		//session_start();
		$url = 'http://'.$_SERVER['HTTP_HOST'].'/toolbaropera';

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title><?php if(!empty($page_title)){ echo "$page_title";}else{echo "Drsmarts bar - a free web annotation tool for everyone";}  ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" href=<?php echo"$url"."/styles.css"; ?> type="text/css"/>
</head>

<body>
<div id="content">
	<div id="header">
		<h1 id="logo">Drsmarts <span style="font-size: 12px; font-weight: bold;"> a general purpose web annotation tool</span></h1>
	
        </div>
   <div id="nav">
			<h3>Menu</h3>
			<ul id="nav_list">
					<li class="navtop"><a href=<?php echo"$url"."/index.php";?> title="Go to the Home Page">Home</a></li>
					<li><a href=<?php echo"$url"."/allAnnotated.php"; ?> title="All annotated web pages">Drsmarts pages</a></li>
					
				<?php 
					
					//Create a login/logout link
					if((isset($_SESSION['drsmarts_userid'])) && (!strpos($_SERVER['PHP_SELF'], 'logout.php'))){
							echo "<li><a href='".$url."/users/downloads.php'"." title='Download'>Downloads</a></li>
								  <li><a href='".$url."/users/account.php'"."  title='My account'>My account</a></li>
								  <li><a href='".$url."/logout.php'"." title='Log out'>Log out</a></li>";
					}else{
							echo "<li><a href='".$url."/registration.php'"."title='Register'>Register</a></li>
								  <li><a href='".$url."/login.php'"."title='Log in'>Log in</a></li>";
					} 
				?>
			</ul>
	</div>

