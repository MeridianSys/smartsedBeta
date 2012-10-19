<?php
/* 
#################################################################################
#																				#
#   Poll Controller 															#
#   file name        	: polls_controller.php									#
#   Developed        	: Meridian Radiology 									#	
#   Intialised Variables: name , helpers ,components , uses , layout = (admin)  #
#																				#
#																				#
#################################################################################
*/
class PollsController extends AppController {

 	 var $name = 'Polls';
     var $helpers = array('html', 'Form', 'javascript','Ajax','session','Thickbox');
	 var $layout = 'admin';
	
	 var $uses = array('User','Userprofile','Poll','Pollanswer','Polluserattempt');
	 var $components = array('Usercomponent');
    
/*___________________________________________________________________________________________________
* 
* Method     : index
* Purpose    : making list of the ediv values
* Parameters : None
* 
* ___________________________________________________________________________________________________
*/	 
function index(){
		
	    // Check admin athontication // 
		$this->checkadmin();
                 $calender = $this->Poll->find('all',array('order'=>'Poll.day_slot'));
                 $this->set('calender',$calender);
                 
                 if(isset($this->data['Poll']['date_of_poll']) && empty($this->data['Poll']['date_of_poll'])){
                    $this->Session->setFlash('Please select the date from calender');
                 }
                 if(!empty($this->data['Poll']['date_of_poll'])){
			
			$dt = $this->data["Poll"]["date_of_poll"];
			$this->Poll->find('all');
		     $this->paginate = array(
                         'limit' => 25,
                         'page'=>1,
                         
    				 );
    		
    		$data = $this->paginate('Poll',array('Poll.date_of_poll="'.$dt.'"'));
    		
            $this->set('data',$data);
             $calender = $this->Poll->find('all',array('order'=>'Poll.day_slot'));
                 $this->set('calender',$calender);
		}else{
			
		$this->Poll->find('all');
		$this->paginate = array(
                         'limit' => 25,
                         'page'=>1,
                         
    				 );
	     
	     $data = $this->paginate('Poll');
         $this->set('data',$data);
          $calender = $this->Poll->find('all',array('order'=>'Poll.day_slot'));
                 $this->set('calender',$calender);
		}
		
}	 
/*___________________________________________________________________________________________________
 * 
* Method     : newediv
* Purpose    : new ediv values
* Parameters : None
* 
* ___________________________________________________________________________________________________
*/	 
function newpoll(){
		
	    // Check admin athontication // 
		$this->checkadmin();
		if(!empty($this->data['Poll']['val'])){
        	$this->Poll->set($this->data);
				
        	
			if($this->Poll->validates()){
                           
				if(!empty($this->data['Poll']['date_of_poll']) && !empty($this->data['Poll']['day_slot'])){
				   if($this->polldatesessioncheck($this->data['Poll']['date_of_poll'],$this->data['Poll']['day_slot'])){
						
					 $comingdate = strtotime($this->data['Poll']['date_of_poll']);
                                       $presentDate = strtotime(date('Y-m-d'));

                                       if($comingdate >= $presentDate){
                                         $this->Poll->save($this->data);
					$qid = $this->autoincrementid('Poll');
					$data = array();

					$data['poll_qid']=$qid-1;

					for($i=1;$i<=count($this->params['form']);$i++){
						$data['poll_answer'] = $this->params["form"]["name".$i];
						$data['id']='';
						$this->Pollanswer->save($data);

					}
                                        $this->Session->setFlash('Your poll posted successfully');
						$this->redirect('/polls/index');
                                       }else{
                                         $this->Session->setFlash('Polling date should be present or comming one');

                                       }
                                }else{
                                    //$this->Session->setFlash('This Polling date previously selected please change the Polling data');

                                }
                        }else{
                          $this->Session->setFlash('Please enter the date and session of the poll');
                        }


				
	}// poll validation
			
				
        }// final submition 
		
}
/*___________________________________________________________________________________________________
* 
* Method     : newediv
* Purpose    : new ediv values
* Parameters : None
* 
* ___________________________________________________________________________________________________
*/	 
function editpoll($id=NULL){
		
	    // Check admin athontication // 
	   $this->checkadmin();
	   $this->Poll->id = $id;
         if (empty($this->data)) {
          $this->data = $this->Poll->read();
          // For answer retreval //
          
          $data = $this->Pollanswer->find('all',array('conditions'=>'poll_qid = '.$id));
          
          $this->set('id',$id);
          $this->set('data',$data);
       }else{
        
       	if(!empty($this->data['Poll']['date_of_poll']) && !empty($this->data['Poll']['day_slot'])){
            $present_date = $this->data['Poll']['dt'];
                 $todays_date = date("Y-m-d");
                 $today = strtotime($todays_date);
                 $sys_date = strtotime($present_date);
                 $checkBack = $this->data['Poll']['date_of_poll'];
//echo $sys_date.' < '.$todays_date;
//echo $sys_date .'>='. $todays_date .'&&'. strtotime($checkBack). '>='. $todays_date;
                  if($sys_date >= $today && strtotime($checkBack) >= $today && $this->data['Poll']['dt'] !='0000-00-00'){


       		if($this->polldatesessioncheck($this->data['Poll']['date_of_poll'],$this->data['Poll']['day_slot'],$id)){
       			
       				if($this->Poll->save($this->data)) {
		    
        				$ansVal = array();
        	
        					foreach($this->params["form"] as $id=>$value){
        						$val = explode('_',$id);
				        	
        						$ansVal['id'] = $val[1];
        						$ansVal['poll_answer'] = $value;
        		
        						$this->Pollanswer->save($ansVal);
        		
        		
        				}
        	
                                                 $this->Session->setFlash('Poll updated successfully ');
						$this->redirect(array('action' => 'index'));
		 		}
       	 		$this->set('id',$id);
       		}else{
       			
       			
       			$data = $this->Pollanswer->find('all',array('conditions'=>'poll_qid = '.$id));
          
                        $this->set('id',$id);
                        $this->set('data',$data);
       	    }

            }
            elseif($this->data['Poll']['dt'] =='0000-00-00'){
                if($this->polldatesessioncheck($this->data['Poll']['date_of_poll'],$this->data['Poll']['day_slot'],$id)){

       				if($this->Poll->save($this->data)) {

        				$ansVal = array();

        					foreach($this->params["form"] as $id=>$value){
        						$val = explode('_',$id);

        						$ansVal['id'] = $val[1];
        						$ansVal['poll_answer'] = $value;

        						$this->Pollanswer->save($ansVal);


        				}

                                                 $this->Session->setFlash('Poll updated successfully ');
						$this->redirect(array('action' => 'index'));
		 		}
       	 		$this->set('id',$id);
       		}else{


       			$data = $this->Pollanswer->find('all',array('conditions'=>'poll_qid = '.$id));

                        $this->set('id',$id);
                        $this->set('data',$data);
       	    }

            }
            else{
                $this->Session->setFlash('Previous Date poll can not be updated');
                        $data = $this->Pollanswer->find('all',array('conditions'=>'poll_qid = '.$id));
                        $this->set('id',$id);
                        $this->set('data',$data);
                
            }




       	}else{
            $this->Session->setFlash('Please enter the date and session of the poll');
            $data = $this->Pollanswer->find('all',array('conditions'=>'poll_qid = '.$id));
            $this->set('id',$id);
            $this->set('data',$data);
        }
       	 
       }// first else //
	   
}	 

	
/*
 _______________________________________________________________________________
* Method polledivactivedeactive
* Purpose: admin can make deactive the user  
* Parameter : None
* 
* _______________________________________________________________________________
*/
function pollactivedeactive($id=NULL,$page=NULL){
        	$this->checkadmin();
        	$a=0;
        $this->autoRender=false;
          if($this->RequestHandler->isAjax()){
             Configure::write('debug', 0);
			   	if(!empty($id)){
				    $results=$this->Poll->find('all',array('conditions'=>array('MD5(Poll.id)' =>$id)));
				    	if($results[0]['Poll']['status']=='1'){
				    		$a = 'inactive_'.$id;
							$data=array(
							'status'=>0,
							'id' =>$results[0]['Poll']['id']);
						}else{
							$a = 'active_'.$id;
							$data=array(
							'status'=>1,
							'id' =>$results[0]['Poll']['id']);
						}
						$this->Poll->save($data);
						echo $a;
						
										
					}
          }	
   	
   	
   }

   
/*
 _______________________________________________________________________________
* Method viewdetail
* Purpose: admin can view the user  
* Parameter : None
* 
* _______________________________________________________________________________
*/   
function viewdetail($id=NULL){
	    $this->checkadmin();
    	$this->layout = '';
    	$data = $this->Poll->find('all',array('conditions'=>'id ='.$id));
    	// Set question //
    	$this->set('qdetail',$data[0]['Poll']['poll_questions']);
    	// Find answers //
    	$adata = $this->Pollanswer->find('all',array('conditions'=>'poll_qid ='.$id));
    	// Set poll answers //
    	$this->set('adetail',$adata);
    	$this->set('idd',$id);
    	// 
    	
	
} 

/*
 _______________________________________________________________________________
* Method userpolldetail
* Purpose: admin can view the all polled user  
* Parameter : $id
* 
* _______________________________________________________________________________
*/   
function userpolldetail($id=NULL,$qid=NULL){
	    $this->checkadmin();
    	$this->layout = '';
        
    	$this->paginate = array(
                         'limit' => 25,
                         'page'=>1,
    	                 'conditions'=>'poll_ans_id ='.$id
                         
    				 );
    		$data = $this->paginate('Polluserattempt');
	       $this->set('result',$data);
	       $this->set('qid',$qid);
	
	
} 
/*______________________________________________________________________________
* Method xlsqdownload
* Purpose: admin can take detail of each question with user poll 
* Parameter : $id
* 
* _______________________________________________________________________________
*/
function xlsdownload($qid){
	
	$data = $this->Poll->find('all',array('conditions'=>'Poll.id='.$qid));

	$rval = $this->Usercomponent->imageGenerateKey();
    
    if(count($data)>0){

		    header("Pragma: public");
		    header("Expires: 0");
		    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		    header("Content-Type: application/force-download");
		    header("Content-Type: application/octet-stream");
		    header("Content-Type: application/download");;
		    header("Content-Disposition: attachment;filename=".$rval.".xls "); 
		    header("Content-Transfer-Encoding: binary");
    	

    
    ?>
    <table width="100%" border="1">
    		 <tr bgcolor="#F4D695"><td colspan="5"  width="100%">
    		 <table width="250" bgcolor="#E0D484" border="1">
    		 <tr><td><strong>Organisation  :</strong></td><td>Smartsed</td></tr>
    		 <tr><td><strong>Type :</strong></td><td>User Polling</td></tr>
    		 <tr><td><strong>Date of download :</strong></td><td><?php echo date('Y-m-d');?></td></tr>
    		 </table>
    		 </td></tr>
			  <tr bgcolor="#F4D695">
			    <td valign="top" style="padding-left:30px;"><b>Date of Poll</b></td>
			    <td valign="top" style="padding-left:30px;"><b>Question</b></td>
			    <td valign="top" style="padding-left:30px;"><b>Answers</b></td>
			    <td valign="top" style="padding-left:30px;"><b>Count User</b></td>
				<td valign="top" style="padding-left:30px;"><b>User Name</b></td>
			  </tr>
	
			  <tr>
			   <td rowspan="<?php echo count($data[0]['Pollanswer']); ?>" valign="middle" style="padding-left:30px;"><?php echo $data[0]['Poll']['date_of_poll'];?></td>
			    <td rowspan="<?php echo count($data[0]['Pollanswer']);?>" valign="middle" style="padding-left:30px;"><?php echo $data[0]['Poll']['poll_questions'];?></td>
			  <?php
			  
				for($i=0;$i<count($data[0]['Pollanswer']);$i++){
					$qu ="SELECT GROUP_CONCAT(uid,'') AS userid , COUNT(id) AS cnt FROM poll_user_attempts WHERE poll_ans_id = ".$data[0]['Pollanswer'][$i]['id']." GROUP BY poll_user_attempts.poll_ans_id";
					
					$username =	$this->Polluserattempt->query($qu); 
					
					?>
					<td valign="middle" style="padding-left:30px;"><?php echo $data[0]['Pollanswer'][$i]['poll_answer'];?></td>
					<td valign="middle" style="padding-left:30px;">
					<?php 
					if(!empty($username)){
							echo $username[0][0]['cnt'];
					}else{
							    echo '0';
						}
					?>
					</td>
					<td valign="middle" style="padding-left:30px;">
					
					<?php
					
					
					if(!empty($username)){
						
						if(strpos($username[0][0]['userid'],",")){
							$uval = explode(',',$username[0][0]['userid']);
							for($j=0;$j<count($uval);$j++){
								if($j==0){
									
								}else{
									echo ',';
								}
								$userInfo = $this->get_user_info($uval[$j],'Userprofile.first_name,Userprofile.last_name',false);
            					$userName = $userInfo['Userprofile']['first_name'].' '.$userInfo['Userprofile']['last_name'];
								echo $userName;	
							}
						}else{
							    $userInfoo = $this->get_user_info($username[0][0]['userid'],'Userprofile.first_name,Userprofile.last_name',false);
            					$userNamee = $userInfoo['Userprofile']['first_name'].' '.$userInfoo['Userprofile']['last_name'];
								echo $userNamee; 
						}
						
					}else{
						echo 'No Poll';
						
					}
					
					?>				
					
					
					</td>
			  		</tr>	
					<?php 
				
				}
			   ?>  
	 </table> 
    <?php 
        exit();
    }else{
    	$this->Session->setFlash('Your required file is unable to create ');
		$this->redirect(array('action' => 'index'));
    } 
   exit();
}

/*______________________________________________________________________________
* Method xlsalldownload
* Purpose: admin can take detail of all question with user poll 
* Parameter : $id
* 
* _______________________________________________________________________________
*/
function xlsalldownload(){
	$data = $this->Poll->find('all');
	$rval = $this->Usercomponent->imageGenerateKey();
	if(count($data)>0)
	{
		  header("Pragma: public");
		    header("Expires: 0");
		    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		    header("Content-Type: application/force-download");
		    header("Content-Type: application/octet-stream");
		    header("Content-Type: application/download");;
		    header("Content-Disposition: attachment;filename=".$rval.".xls "); 
		    header("Content-Transfer-Encoding: binary");
    	
	
	?>
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<style type="text/css"> 
	.cellText{
		font-size:font-size:200%;
		font-weight:bold;
		color:#666666;
		margin-left:3px;
	
	
	}
	td{
	color:red;
	
	}
	</style>
	</head>
	<body>
	 <table width="100%" border="1" cellspacing="2" cellpadding="3">
	  <tr bgcolor="#F4D695"><td colspan="5"  width="100%">
    		 <table width="400" bgcolor="#E0D484" border="1" cellspacing="2" cellpadding="3">
    		 <tr><td><strong>Organisation  :</strong></td><td>Smartsed</td></tr>
    		 <tr><td><strong>Type :</strong></td><td>All User Polling Questions</td></tr>
    		 <tr><td><strong>Date of download :</strong></td><td><?php echo date('Y-m-d');?></td></tr>
    		 </table>
    		 </td></tr>
		
		<tr bgcolor="#F4D695">
			    <td valign="top"  width="5%" style="font-size:font-size:200%;">Date of Poll</td>
			    <td valign="top"  width="30%" class="cellText">Question</td>
			    <td valign="top"  width="30%" class="cellText">Answers</td>
			    <td valign="top"  width="5%" class="cellText">Count User</td>
				<td valign="top"  width="30%" class="cellText">User Name</td>
			  </tr>
		
			  <tr><td colspan="5"></td></tr>	
			 <!--  Repeting The Pattern -->
			 <?php for($i=0;$i<count($data);$i++){
			 	?>
			 <tr bgcolor="#ECF48B">
			  	<td rowspan="<?php echo count($data[$i]['Pollanswer']);?>"><?php echo $data[$i]['Poll']['date_of_poll'];?></td>
			  	<td rowspan="<?php echo count($data[$i]['Pollanswer']);?>"><?php echo $data[$i]['Poll']['poll_questions'];?></td>
			  	 <?php for($j=0;$j<count($data[$i]['Pollanswer']);$j++){
			  	 	$qu ="SELECT GROUP_CONCAT(uid,'') AS userid , COUNT(id) AS cnt FROM poll_user_attempts WHERE poll_ans_id = ".$data[$i]['Pollanswer'][$j]['id']." GROUP BY poll_user_attempts.poll_ans_id";
					$username =	$this->Polluserattempt->query($qu); 
			  	 	
			  	 	
			  	 	?>
			  		<td bgcolor="#ECF48B"> <?php echo $data[$i]['Pollanswer'][$j]['poll_answer'];?></td>
			  		<td bgcolor="#ECF48B"> 
			  		<?php 
					if(!empty($username)){
							echo $username[0][0]['cnt'];
					}else{
							    echo '0';
						}
					?>
					</td>
			  		<td bgcolor="#ECF48B">
			  		<?php
					
					
					if(!empty($username)){
						
						if(strpos($username[0][0]['userid'],",")){
							$uval = explode(',',$username[0][0]['userid']);
							for($k=0;$k<count($uval);$k++){
								if($k==0){
									
								}else{
									echo ',';
								}
								$userInfo = $this->get_user_info($uval[$k],'Userprofile.first_name,Userprofile.last_name',false);
            					$userName = $userInfo['Userprofile']['first_name'].' '.$userInfo['Userprofile']['last_name'];
								echo $userName;	
							}
						}else{
							    $userInfoo = $this->get_user_info($username[0][0]['userid'],'Userprofile.first_name,Userprofile.last_name',false);
            					$userNamee = $userInfoo['Userprofile']['first_name'].' '.$userInfoo['Userprofile']['last_name'];
								echo $userNamee; 
						}
						
					}else{
						echo 'No Poll';
						
					}
			  		?>
			  		
			  		
			  		</td>
			  		</tr>
			  	 	<?php 
			  	 }
			 	?>
			    <tr><td colspan="5"></td></tr>
		
			 	
			 	<?php 
			 } ?>
			  
		      
	     </table>
	     </body>
	     </html>
	<?php 
	}
	exit();
}

/*______________________________________________________________________________
* Method uploadpoll
* Purpose: admin can poll questions directly
* Parameter : none
*
* _______________________________________________________________________________
*/
function uploadpoll(){
    $this->checkadmin();
    $this->layout = '';
    if(!empty($this->data)){
     $check= explode('.',$this->data['Poll']['file']['name']);
     if($check[1]=='csv'){
         $rval = $this->Usercomponent->imageGenerateKey();
         $iname=$rval.'_'.$this->data['Poll']['file']['name'];
         if(move_uploaded_file($this->data['Poll']['file']['tmp_name'] ,_ROOT_BASE_PATH."img/csv_poll/".$iname)){

             if (($handle = fopen(_ROOT_BASE_PATH."img/csv_poll/".$iname, "r")) !== FALSE) {
                       while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                                
                                $question  = $data[0];
                                $answer1   = $data[1];
                                $answer2   = $data[2];
                                $answer3   = $data[3];
                                $answer4   = $data[4];
                                $answer5   = $data[5];
                                $questionAr = array();// for question //
                                $firstAnsAr=array();// for first answer //
                                $secondAnsAr=array();// for second answer //
                                $thirdAnsAr=array();// for third answer //
                                $fourthAnsAr=array();// for fourth answer //
                                $fifthAnsAr=array();// for fifth answer //
                                $questionAr['id']='';
                                if(!empty($question) && $question!=''){
                                    $questionAr['poll_questions']=$question;
                                    $questionAr['status']='1';
                                    $questionAr['datetime']=date('Y-m-d H:i:s');
                                    $questionAr['date_of_poll']='0000-00-00';
                                    $this->Poll->save($questionAr);
                                    $qid = $this->autoincrementid('Poll');
                                    if(!empty($answer1) && $answer1!=''){
                                        $firstAnsAr['id']      ='';
                                        $firstAnsAr['poll_qid']=$qid-1;
                                        $firstAnsAr['poll_answer']=$answer1;
                                        $this->Pollanswer->save($firstAnsAr);
                                        
                                    }
                                    if(!empty($answer2) && $answer2!=''){
                                        $secondAnsAr['id']      ='';
                                        $secondAnsAr['poll_qid']=$qid-1;
                                        $secondAnsAr['poll_answer']=$answer2;
                                        $this->Pollanswer->save($secondAnsAr);

                                    }
                                    if(!empty($answer3) && $answer3!=''){
                                        $thirdAnsAr['id']      ='';
                                        $thirdAnsAr['poll_qid']=$qid-1;
                                        $thirdAnsAr['poll_answer']=$answer3;
                                        $this->Pollanswer->save($thirdAnsAr);

                                    }
                                    if(!empty($answer4) && $answer4!=''){
                                        $fourthAnsAr['id']      ='';
                                        $fourthAnsAr['poll_qid']=$qid-1;
                                        $fourthAnsAr['poll_answer']=$answer4;
                                        $this->Pollanswer->save($fourthAnsAr);

                                    }
                                    if(!empty($answer5) && $answer5!=''){
                                        $fifthAnsAr['id']      ='';
                                        $fifthAnsAr['poll_qid']=$qid-1;
                                        $fifthAnsAr['poll_answer']=$answer5;
                                        $this->Pollanswer->save($fifthAnsAr);

                                    }

                              }
                                
                        }
                       
                        fclose($handle);
                        $this->Session->setFlash('[csv] file process sucessfully for poll');
                        $this->redirect(array('action' => 'index'));

                  }

            }
      }
      else{
          $this->Session->setFlash('File extention is not valid ');
	  $this->redirect(array('action' => 'index'));
      }
   }
   
}















 //


}// END CLASS 

?>
