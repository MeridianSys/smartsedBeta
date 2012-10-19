<?php
	include('../../config/db_connect.php');
	include('../../config/config.inc.php');
	include('../../config/error_handler.php');

       
if(isset($_POST['submitted']) && !empty($_POST['user_id'])){		//if a request has been sent
	
	if(!empty($_POST['user_id'])){
			$userid = $_POST['user_id'];
	}

	$errors = array(); //keeps a log of errors
	if(isset($_POST['addSticky'])){						//***perform ADD sticky annotation
		if(!empty($_POST['stickyId'])){
			$sid = escapeData($_POST['stickyId']);
		}	
		if(!empty($_POST['url'])){
			$surl = $_POST['url'];
		}
		if(!empty($_POST['annotation'])){
			$sannotation = escapeData($_POST['annotation']);
		}
		if(!empty($_POST['referredText'])){
			$sreferredText = escapeData($_POST['referredText']);
		}
		if(!empty($_POST['path'])){
			$spath = escapeData($_POST['path']);
		}
		if(!empty($_POST['span_id'])){
			$spanid = escapeData($_POST['span_id']);
		}
		if(!empty($_POST['mini_icon_id'])){
			$miniIconId = escapeData($_POST['mini_icon_id']);
		}
		if(!empty($_POST['access'])){
			$saccess = escapeData($_POST['access']);
		}
                if(!empty($_POST['title'])){
			$stitle = escapeData($_POST['title']);
		}
		
		header('Content-Type: text/xml');           //if association HAS been made successfully, send true
		$dom = new DOMDocument();
		$response = $dom->createElement('response');
		$dom -> appendChild($response);

		$exist_query = "SELECT annotate_id FROM annotation WHERE sticky_id = '$sid'";
		$exec_exist_query = mysql_query($exist_query);
		$exist_res = mysql_fetch_array($exec_exist_query);
		if(!$exist_res){													//if this sticky id hasn't been used
			
			$findUrl_id = "SELECT url_id FROM urls WHERE url= '$surl'";		//check if url already exists
			$execFindUrl_id = mysql_query($findUrl_id) OR die(mysql_error());
			$url_res = mysql_fetch_array($execFindUrl_id, MYSQL_NUM);
			if(mysql_num_rows($execFindUrl_id) == 1){ 												//if URL exists, fetch the url_id
				$uid = $url_res[0]; //the url_id of the URL
				
			}else{ 	
																			//if none found, add this url into url table
				$addUrl = "INSERT INTO urls(url) VALUES ('$surl')"; 
				$execAddUrl_id = mysql_query($addUrl) OR die(mysql_error());
				$url_result = mysql_affected_rows();
				
				if($url_result == 1){										//if added successfully, fetch the url_id of the newly added url
					$findUrl_id = "SELECT url_id FROM urls WHERE url= '$surl'"; 	//fetch the url_id of the newly added url
					$execFindUrl_id = mysql_query($findUrl_id) OR die(mysql_error());
					$url_res = mysql_fetch_array($execFindUrl_id, MYSQL_NUM);
					if($url_res){
																   //if URL exists, fetch the url_id
						$uid = $url_res[0]; //the url_id of the URL
						
					}
				}else{
					$error = "ERROR: unable to add url".mysql_error();
					$responseText = $dom->createTextNode('false');
					$response->appendChild($responseText);
				}
			}
			$newAnnotation = "INSERT INTO annotation(sticky_id, user_id, access, path, span_id, mini_icon_id,title, annotation, referredText, date_created, date_modified) VALUES ('$sid', $userid, $saccess, '$spath', '$spanid', '$miniIconId','$stitle', '$sannotation', '$sreferredText', NOW(), NOW())";
                       // echo $newAnnotation;
			$execNewAnno = mysql_query($newAnnotation) OR die(mysql_error());
			$r = mysql_affected_rows();
			if($r == 1){ 			
																		//if it has been added successfully, fetch its annotate_id
				$id_query = "SELECT annotate_id FROM annotation WHERE sticky_id = '$sid'";
				$execidq = mysql_query($id_query) OR die('ERROR'.mysql_error());
				$r1 = mysql_fetch_array($execidq);
				
				if(mysql_num_rows($execidq) == 1){
						$aid = $r1[0]; //the annotate_id for the newly added sticky
				}else{
					$error = "ERROR: unable to obtain id from the newly added annotation".mysql_error();
				}
			}else{
					$error = "ERROR: unable to add annotation".mysql_error();
			}
			//***add entries into assoc table using $userid, $uid, $aid to associate all three elements together
			$assoc_query = "INSERT INTO assoc(annotate_id, url_id) VALUES ($aid, $uid)";
			$exec_assoc_query = mysql_query($assoc_query) OR die(mysql_error());
			$eaq_res = mysql_affected_rows();
			if($eaq_res != 1){				
																	//if association has NOT been made successfully, send false
				$error = "ERROR: unable to add associate annotations with URL and user ids".mysql_error();
				$responseText = $dom->createTextNode('false');
				$response->appendChild($responseText);
			}else{
								          //if association HAS been made successfully, send true
				$responseText = $dom->createTextNode('true');
				$response->appendChild($responseText);
			}
		}
		else{
			$errors = "ERROR: the sticky id already exists!";
			$responseText = $dom->createTextNode('false');
			$response->appendChild($responseText);
		}
		$xmlString = $dom->saveXML();
		echo $xmlString;
	} //end of addSticky function

	if(isset($_POST['removeSticky'])){									//***perform REMOVE sticky annotation
			
			if(!empty($_POST['stickyId'])){
				$sid = escapeData($_POST['stickyId']);
			}	
				$a_query = "SELECT annotate_id FROM annotation WHERE (sticky_id = '$sid' AND user_id = '$userid')";	//delete sticky which has annotate_id
				$exec_a_query = mysql_query($a_query) OR die(mysql_error());
				$a_res = mysql_num_rows($exec_a_query);
				if($a_res != 1){
					header('Content-Type: text/xml');
				    $dom = new DOMDocument();
			        $response = $dom->createElement('response');
			        $dom -> appendChild($response);
					$errors = "ERROR: unable to delete sticky.";
					$responseText = $dom->createTextNode('false');
					$response->appendChild($responseText);
				}else{
					$a_array = mysql_fetch_array($exec_a_query);
					$aid = $a_array[0];
					$del_query = "DELETE FROM annotation WHERE annotate_id = $aid";	//delete sticky which has annotate_id
					$exec_del_query = mysql_query($del_query) OR die(mysql_error());
					$del_res = mysql_affected_rows();
					if($del_res != 1){
						header('Content-Type: text/xml');
				  	    $dom = new DOMDocument();
			     	    $response = $dom->createElement('response');
			        	$dom -> appendChild($response);
						$errors = "ERROR: unable to delete sticky.";
						$responseText = $dom->createTextNode('false');
						$response->appendChild($responseText);
					
					}else{													
						//if sticky has been removed successfully, remove its association entry in table 'assoc'
						$assoc_query = "DELETE FROM assoc WHERE annotate_id = $aid";
						$exec_assoc_query = mysql_query($assoc_query);
						$assoc_res = mysql_affected_rows();
						if($assoc_res != 1){
							header('Content-Type: text/xml');
				   			$dom = new DOMDocument();
			            	$response = $dom->createElement('response');
			            	$dom -> appendChild($response);
							$errors = "ERROR: unable to delete sticky.";
							$responseText = $dom->createTextNode('false');
							$response->appendChild($responseText);

						}else{
							header('Content-Type: text/xml');
							$dom = new DOMDocument();
			      	    	$response = $dom->createElement('response');
			       	    	$dom -> appendChild($response);
							$responseText = $dom->createTextNode('true');
							$response->appendChild($responseText);
						}
				    }
				}
		$xmlString = $dom->saveXML();
		echo $xmlString;
	}//end of removeSticky

	if(isset($_POST['modifySticky'])){					//*** perform MODIFY sticky function
		
		if(!empty($_POST['stickyId'])){
			$sid = escapeData($_POST['stickyId']);
		}	
		if(!empty($_POST['annotation'])){
			$sannotation = escapeData($_POST['annotation']);
		}
		
		header('Content-Type: text/xml');
		$dom = new DOMDocument();
		$response = $dom->createElement('response');
		$dom -> appendChild($response);
							//modify sticky if the sticky belongs to this user
			$mod_query = "UPDATE annotation SET annotation = '$sannotation' WHERE (sticky_id = '$sid' AND user_id = '$userid')"; 
			$exec_mod_query = mysql_query($mod_query) OR die(mysql_error());
			$mod_res = mysql_affected_rows();
			if($mod_res == 1){
				$mod_query = "UPDATE annotation SET date_modified = NOW() WHERE sticky_id = '$sid'"; //update modified date
				$exec_mod_query = mysql_query($mod_query) OR die(mysql_error());
				$responseText = $dom->createTextNode('true');
				$response->appendChild($responseText);
				
			}else{
				$errors = "ERROR: unable to modify sticky.";
				$responseText = $dom->createTextNode('false');
				$response->appendChild($responseText);
			}
		$xmlString = $dom->saveXML();
		echo $xmlString;
	}// end of modifySticky function 

	if(isset($_POST['showUserStickies'])){					//***perform SHOW USER's stickies function
		
			if(!empty($_POST['url'])){
					$surl = $_POST['url'];
			}
		
		$uid_query = "SELECT url_id FROM urls WHERE url = '$surl'";		//find the url_id of the URL
		$exec_uid_query = mysql_query($uid_query) OR die(mysql_error());
		$uid_array = mysql_fetch_array($exec_uid_query, MYSQL_NUM);
		if(mysql_num_rows($exec_uid_query) == 1){
			$uid = $uid_array[0];
			$stickyid_query = "SELECT annotate_id FROM assoc WHERE url_id = $uid";	
	
											//find the id of the stickies associated with the URL AND the user's id
			$exec_stickyid_query = mysql_query($stickyid_query) OR die(mysql_error());
			$numOfResults = mysql_num_rows($exec_stickyid_query);
			if($numOfResults > 0){			
				
				header('Content-Type: text/xml');			//if stickies are found
				$dom = new DOMDocument();
				$response = $dom->createElement('response');
				$dom -> appendChild($response);
				$numFound = 0;
				
				while($numOfResults != 0){
					$annotateid_array = mysql_fetch_array($exec_stickyid_query, MYSQL_NUM);

					$userid_query = "SELECT user_id FROM annotation WHERE annotate_id = $annotateid_array[0]";		//find the username of each sticky
					$exec_userid_query = mysql_query($userid_query) OR die(mysql_error());
					if(mysql_num_rows($exec_userid_query) == 1){
								$userid_array = mysql_fetch_array($exec_userid_query, MYSQL_NUM);
								$u_id = $userid_array[0];
								$usern_query = "SELECT username FROM users WHERE user_id = $u_id";
								$exec_usern_query = mysql_query($usern_query) OR die(mysql_error());
								if(mysql_num_rows($exec_usern_query) == 1){
											$username_array = mysql_fetch_array($exec_usern_query);
											$username = $username_array[0];
									
								}
					}
					
																		//for each sticky, obtain the relevant information and attach into XML response
					$stickyEle = $dom->createElement('sticky');
					$response->appendChild($stickyEle);
					
					$userStickies_query = "SELECT sticky_id, path, span_id, mini_icon_id, annotation, referredText, date_created, date_modified FROM annotation WHERE annotate_id = $annotateid_array[0] AND user_id = '$userid'";
					$exec_uSticky_query = mysql_query($userStickies_query) OR die(mysql_error());
					$sticky_array = mysql_fetch_array($exec_uSticky_query, MYSQL_NUM);
					if($sticky_array){
						$stickyidEle = $dom->createElement('stickyid');
						$stickyid_Data = $dom->createTextNode($sticky_array[0]);
						$stickyidEle->appendChild($stickyid_Data);
						$stickyEle->appendChild($stickyidEle);

						$pathEle = $dom->createElement('path');
						$path_Data = $dom->createTextNode($sticky_array[1]);
						$pathEle->appendChild($path_Data);
						$stickyEle->appendChild($pathEle);
					
						$authorEle = $dom->createElement('author');
						$author_data = $dom->createTextNode($username);
						$authorEle->appendChild($author_data);
						$stickyEle->appendChild($authorEle);

						$useridEle = $dom->createElement('user_id');
						$userid_Data = $dom->createTextNode($u_id);
						$useridEle->appendChild($userid_Data);
						$stickyEle->appendChild($useridEle);
						
						$spanEle = $dom->createElement('span_id');
						$span_Data = $dom->createTextNode($sticky_array[2]);
						$spanEle->appendChild($span_Data);
						$stickyEle->appendChild($spanEle);

						$miniEle = $dom->createElement('mini_icon_id');
						$mini_Data = $dom->createTextNode($sticky_array[3]);
						$miniEle->appendChild($mini_Data);
						$stickyEle->appendChild($miniEle);

						$annotationEle = $dom->createElement('annotation');
						$annotation_Data = $dom->createTextNode($sticky_array[4]);
						$annotationEle->appendChild($annotation_Data);
						$stickyEle->appendChild($annotationEle);

						$referredTextEle = $dom->createElement('referredText');
						$referredText_Data = $dom->createTextNode($sticky_array[5]);
						$referredTextEle->appendChild($referredText_Data);
						$stickyEle->appendChild($referredTextEle);

						$date_created_Ele = $dom->createElement('dateCreated');
						$date_created_Data = $dom->createTextNode($sticky_array[6]);
						$date_created_Ele->appendChild($date_created_Data);
						$stickyEle->appendChild($date_created_Ele);

						$date_mod_Ele = $dom->createElement('dateModified');
						$date_mod_Data = $dom->createTextNode($sticky_array[7]);
						$date_mod_Ele->appendChild($date_mod_Data);
						$stickyEle->appendChild($date_mod_Ele);
						
						$numFound++;
						
					}//end of if($sticky_array)
					$numOfResults--;
				}//end of while loop
				$foundEle = $dom->createElement('stickiesFound');
				$found_data = $dom->createTextNode($numFound);	//state the number of stickies found
				$foundEle->appendChild($found_data);
				$response->appendChild($foundEle);
			}//end of if($annotateid_array)
			else{
				$errors = "ERROR: No sticky is found for this URL!";
				header('Content-Type: text/xml');
				$dom = new DOMDocument();
				$response = $dom->createElement('response');
				$dom -> appendChild($response);
				$foundEle = $dom->createElement('stickiesFound');
				$found_data = $dom->createTextNode($numOfResults);	//state the number of stickies found
				$foundEle->appendChild($found_data);
				$response->appendChild($foundEle);

			}
		}//end of if(mysql_num_rows($exec_uid_query) == 1)
		else{
			$errors = "ERROR: This URL was not found in the database!";
			header('Content-Type: text/xml');
			$dom = new DOMDocument();
			$response = $dom->createElement('response');
			$dom -> appendChild($response);
			$foundEle = $dom->createElement('stickiesFound');
			$found_data = $dom->createTextNode(0);	//state the number of stickies found
			$foundEle->appendChild($found_data);
			$response->appendChild($foundEle);

		}
		$xmlString = $dom->saveXML();
		echo $xmlString;
	}//end of show user's stickies function
	

	if(isset($_POST['showAllStickies'])){					//***perform SHOW ALL stickies function
		if(!empty($_POST['url'])){
			$surl = $_POST['url'];
		}

		$uid_query = "SELECT url_id FROM urls WHERE url = '$surl'";		//find the url_id of the URL
                //echo $uid_query;
                $exec_uid_query = mysql_query($uid_query) OR die(mysql_error());
		$uid_array = mysql_fetch_array($exec_uid_query, MYSQL_NUM);
		if(mysql_num_rows($exec_uid_query) == 1){
			$uid = $uid_array[0];
			$stickyid_query = "SELECT annotate_id FROM assoc WHERE url_id = $uid";	//find the id of the stickies associated with the URL only
                	$exec_stickyid_query = mysql_query($stickyid_query) OR die(mysql_error());
			$numStickiesFound = mysql_num_rows($exec_stickyid_query);
                       // echo "==".$numStickiesFound;
                	if($numStickiesFound > 0){										//if stickies are found
					header('Content-Type: text/xml');
					$dom = new DOMDocument();
					$response = $dom->createElement('response');
					$dom -> appendChild($response);
					$numOfAnFound=0; ///ADDED on 14 july 2011
				while($numStickiesFound != 0){
					$annotateid_array = mysql_fetch_array($exec_stickyid_query, MYSQL_NUM);
					
					$userid_query = "SELECT user_id FROM annotation WHERE annotate_id = $annotateid_array[0]";		//find the username of each sticky
                                       // echo "++".$userid_query;
                			$exec_userid_query = mysql_query($userid_query) OR die(mysql_error());
					if(mysql_num_rows($exec_userid_query) == 1){
								$userid_array = mysql_fetch_array($exec_userid_query, MYSQL_NUM);
								$u_id = $userid_array[0];
								$usern_query = "SELECT username FROM users WHERE user_id = $userid_array[0]";
								$exec_usern_query = mysql_query($usern_query) OR die(mysql_error());
								if(mysql_num_rows($exec_usern_query) == 1){
											$username_array = mysql_fetch_array($exec_usern_query);
											$username = $username_array[0];
								}
					}
												//for each sticky, obtain the relevant information and attach into XML response
						$stickyEle = $dom->createElement('sticky');
						$response->appendChild($stickyEle);

						//$userStickies_query = "SELECT sticky_id, access, path, span_id, mini_icon_id, annotation, referredText, date_created, date_modified FROM annotation WHERE annotate_id = $annotateid_array[0] && user_id = $userid";
                                                $userStickies_query = "SELECT sticky_id, access, path, span_id, mini_icon_id, annotation, referredText, date_created, date_modified FROM annotation WHERE annotate_id = $annotateid_array[0]";
                                                //echo "(".$userStickies_query.")";
                				$exec_uSticky_query = mysql_query($userStickies_query) OR die(mysql_error());
						$sticky_array = mysql_fetch_array($exec_uSticky_query, MYSQL_NUM);
						if($sticky_array){
							$stickyidEle = $dom->createElement('stickyid');
							$stickyid_Data = $dom->createTextNode($sticky_array[0]);
							$stickyidEle->appendChild($stickyid_Data);
							$stickyEle->appendChild($stickyidEle);

							$accessEle = $dom->createElement('access');
							$access_Data = $dom->createTextNode($sticky_array[1]);
							$accessEle->appendChild($access_Data);
							$stickyEle->appendChild($accessEle);

							$pathEle = $dom->createElement('path');
							$path_Data = $dom->createTextNode($sticky_array[2]);
							$pathEle->appendChild($path_Data);
							$stickyEle->appendChild($pathEle);
					
							$authorEle = $dom->createElement('author');
							$author_data = $dom->createTextNode($username);
							$authorEle->appendChild($author_data);
							$stickyEle->appendChild($authorEle);

							$useridEle = $dom->createElement('user_id');
							$userid_Data = $dom->createTextNode($u_id);
							$useridEle->appendChild($userid_Data);
							$stickyEle->appendChild($useridEle);
						
							$spanEle = $dom->createElement('span_id');
							$span_Data = $dom->createTextNode($sticky_array[3]);
							$spanEle->appendChild($span_Data);
							$stickyEle->appendChild($spanEle);

							$miniEle = $dom->createElement('mini_icon_id');
							$mini_Data = $dom->createTextNode($sticky_array[4]);
							$miniEle->appendChild($mini_Data);
							$stickyEle->appendChild($miniEle);

							$annotationEle = $dom->createElement('annotation');
							$annotation_Data = $dom->createTextNode($sticky_array[5]);
							$annotationEle->appendChild($annotation_Data);
							$stickyEle->appendChild($annotationEle);

							$referredTextEle = $dom->createElement('referredText');
							$referredText_Data = $dom->createTextNode($sticky_array[6]);
							$referredTextEle->appendChild($referredText_Data);
							$stickyEle->appendChild($referredTextEle);

							$date_created_Ele = $dom->createElement('dateCreated');
							$date_created_Data = $dom->createTextNode($sticky_array[7]);
							$date_created_Ele->appendChild($date_created_Data);
							$stickyEle->appendChild($date_created_Ele);

							$date_mod_Ele = $dom->createElement('dateModified');
							$date_mod_Data = $dom->createTextNode($sticky_array[8]);
							$date_mod_Ele->appendChild($date_mod_Data);
							$stickyEle->appendChild($date_mod_Ele);
							$numOfAnFound++;
					}// end of if($sticky_array)
				$numStickiesFound--;
			  }//end of while loop
                	    $foundEle = $dom->createElement('stickiesFound');
                            $found_data = $dom->createTextNode($numOfAnFound);	//state the number of stickies found
                            $foundEle->appendChild($found_data);
			    $response->appendChild($foundEle);
			}//end of if($annotateid_array)
			else{ 
				$errors = "ERROR: No sticky is found for this URL!";
				header('Content-Type: text/xml');
				$dom = new DOMDocument();
				$response = $dom->createElement('response');
				$dom -> appendChild($response);
				$foundEle = $dom->createElement('stickiesFound');
				$found_data = $dom->createTextNode($numStickiesFound);	//state the number of stickies found
				$foundEle->appendChild($found_data);
				$response->appendChild($foundEle);
			}
		}//end of if(mysql_num_rows($exec_uid_query) == 1)
		else{
			$errors = "ERROR: This URL was not found in the database!";
			header('Content-Type: text/xml');
			$dom = new DOMDocument();
			$response = $dom->createElement('response');
			$dom -> appendChild($response);
			$foundEle = $dom->createElement('stickiesFound');
			$found_data = $dom->createTextNode(0);	//state the number of stickies found
			$foundEle->appendChild($found_data);
			$response->appendChild($foundEle);
		}
		$xmlString = $dom->saveXML();
		echo $xmlString;
	}//end of show all stickies function
	
}//end of handler
else{
	header('Content-Type: text/xml');
	$dom = new DOMDocument();
	$response = $dom->createElement('ERROR');
	$dom -> appendChild($response);
	$responseText = $dom->createTextNode('FALSE');
	$response->appendChild($responseText);
	$xmlString = $dom->saveXML();
	echo $xmlString;
}
		
?>