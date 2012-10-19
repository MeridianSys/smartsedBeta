<?php
		include('../config/config.inc.php');
		//Check if user has logged in
		checkLoggedin();
		$page_title = "Drsmarts bar - my account";
		include('../includes/header.php');
?>

<div id="main">
	<h2>My account details</h2>
	<table class="table_style" cellspacing="0px" cellpadding="0px">
<?php
		
		include('../config/db_connect.php');

		if(isset($_SESSION['drsmarts_userid'])){

			$id = $_SESSION['drsmarts_userid'];
			$query = "SELECT username, email, first_name, last_name, company, position, reg_date FROM users WHERE user_id='$id'";
			$exec = @mysql_query($query);
			$row = mysql_fetch_array($exec, MYSQL_NUM);
		
			if($row){
				$un = $row[0];
				$em = $row[1];
				$fn = $row[2];
				$ln = $row[3];
				$cm = $row[4];
				$po = $row[5];
				$rd = $row[6];
			
				if($cm == "/"){ //if no company provided
					$cm = "n/a";
				}
				if($po == "/"){ //if no position provided
					$po = "n/a";
				}

				echo"<tr><td><strong>Username:</strong></td><td><span>$un</span></td></tr><tr><td id='email'><strong>Email:</strong></td><td><span>$em</span></td></tr><tr><td><strong>First name:</strong></td><td><span>$fn</span></td></tr><tr><td><strong>Last name:</strong></td><td><span>$ln</span></td></tr><tr><td><strong>Company/institution:</strong></td><td><span>$cm</span></td></tr><tr><td><strong>Position:</strong></td><td><span>$po</span></td></tr><tr><td><strong>Registration date:</strong></td><td><span>$rd</span></td></tr>";
			}else{
				echo"An error has occurred.".mysql_error()." Please contact administrator.";
			}
				
		}else{
			echo"You must log in before you can view your account details!";
		}
?>
	</table>

	<div id="edit_div"></div>
	<h3>My annotated web pages</h3>
<?php							
			$aid_query = "SELECT annotate_id FROM annotation WHERE user_id = $id"; //find the stickies by the user
			$exec_aid_query = mysql_query($aid_query) OR die(mysql_error());
			$num_resa = mysql_num_rows($exec_aid_query);
                        //echo $num_resa;
			if($num_resa > 0){
				echo'<table class="table_style"><th>Index</th><th>URLs</th>';
			    while($num_resa != 0){										//for each sticky
						$aid_array = mysql_fetch_array($exec_aid_query);
						$aid = $aid_array[0];
						$rurlid = "SELECT url_id FROM assoc WHERE annotate_id = $aid"; //obtain the url id of the stickies
                                                $exec_rurlid = mysql_query($rurlid) OR die(mysql_error());
						$num_res = mysql_num_rows($exec_rurlid);
                                                //echo $num_res;
						if($num_res > 0){	//if annotation has been made
							//$ar = array();
							while($num_res != 0){
								$urlid_array = mysql_fetch_array($exec_rurlid);
								$rurl = "SELECT url FROM urls WHERE url_id = $urlid_array[0]"; //find url for each url id
                                                                //echo $rurl."<br />";
								$exec_rurl = mysql_query($rurl) OR die(mysql_error());
								if(mysql_num_rows($exec_rurl) == 1){

									$url_array = mysql_fetch_array($exec_rurl);
									$unique = true;
									foreach($ar as $s){
										if($s == $url_array[0]){
											$unique = false;
										}
									}
									$length = strlen($url_array[0]); 
									if($unique == true){
										$ar[] = $url_array[0];
									}
						
								}else{
									echo'An unknown error occured'.mysql_error();
								}
							$num_res--;
							}
							
						}
					$num_resa--;
				}
			$num = 1;
                        //echo '<pre>';
                        //print_r($ar);
                        //echo '</pre>';
			foreach($ar as $u){
				echo"<tr><td align='center'>$num</td><td style='background-color: #eee;'><a class='link' href='$u'>$u</a></td></tr>";
				$num++;
			}
			echo'</table>';
			}else{
				echo'No annotation has been made yet.';
			}
?>

</div>
<?php include('../includes/footer.php'); ?>
