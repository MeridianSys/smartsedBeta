<?php
/* 
#################################################################################
#										#
#   User Controller 								#
#   file name        	: admins_controller.php					#
#   Developed        	: Meridian Radiology 					#
#   Intialised Variables: name , helpers ,components , uses , layout = (admin)  #
#																				#
#																				#
#################################################################################
*/
class AdminsController extends AppController {

	var $name = 'Admins';
	var $helpers = array('Html', 'Form', 'javascript','Ajax','session','Thickbox','Fck');
	 var $layout = 'admin';
	 var $conf = array();
	 var $uses = array('User','Userprofile','Admin','Charity','Testimonial','Staticcontent','users_professional_tracks','Institution_list','Ediv_user_master','Statickeyvalue','Adminaddbook','Staticloungevalue');
	 var $components = array('Usercomponent');
	 
	
	/**************************************************************/
    /* Method Name : index                                 */
    /* Purpose : Application handles  login process of admin */
    /**************************************************************/
	function index(){
		
		//$this->checkadmin();
		if(!empty($this->params['url']['url'])){
    		$url = $this->params['url']['url'];
    	    $urlArray	=	explode(':',$url);
          	if(!empty($urlArray[1]))  {
               $returnUrl	=	$urlArray[1];
          		  $this->set('returnUrl',$returnUrl);
          	 }
		}

		$adminID=$this->Session->read('adminid');
		$IsAdmin=$this->Session->read('isadmin');
	
		if(!empty($adminID) && !empty($IsAdmin)){//START :: if admin is already logged in
				
			$this->redirect('/admins/home'); //if logged in redirect to admin dashboard
			
		} //END : : if admin alreday logged in
		else
		{
			if(isset($this->data)) 	{ // START:: isset $this->data
				if(empty($this->data['Admin']['username']) || empty($this->data['Admin']['password'])){ //  START ::  If username or password is empty	       
					$this->Session->setFlash("Please Enter your username and passwword");
				}//END ::username and password is empty
      	        else
      	        {	//START :: if username and password not empty			
      	        	//echo Configure::read('Role.admin');exit;

    				if($results = $this->Admin->find('all',array('conditions'=>array('username'=>$this->data['Admin']['username'])))){ // START :: set result set for current user
    					if($results[0]['Admin']['username']==$this->data['Admin']['username'] && $results[0]['Admin']['password']==md5($this->data['Admin']['password'].$results[0]['Admin']['salt']) && ($results[0]['Admin']['isactive']==Configure::read('Status.active'))){ // START :: matching data  with exising User credntials 
     	  				 		$this->Session->write('adminid',$results[0]['Admin']['id']); //set Session for user id
     	  				 		$this->Session->write('username',$results[0]['Admin']['username']);
     	  				 		$this->Session->write('isadmin',1);
    	  				 		
						   	 	$this->Admin->updateAll(array("last_login"=>"now()"), array("id"=>$results[0]['Admin']['id'])); //update user information after successful logged in
						   	 		if(!empty($this->data['Admin']['returnUrl'])){// START  ::  if user is redirected from any other url
	                    				$returnUrl	=	$this->data['Admin']['returnUrl'];	
	                    				$this->redirect('/'.$returnUrl);
                    				}// END ::  if user is redirected from any other url
                    			else
                    			{// START  :: else admin is redirected to home
									
                    				$this->redirect('/admins/home/');//redirect to the user dashboard
								}	
									
		 				} //END :: user credentials matching with existing users
					 	else {	
			 		        $this->Session->setFlash("Sorry Your Account has not been activated");
						}				
					}// END :: set result for existing users 
					else { 						
						$this->Session->setFlash("OOps !! User Name or Password is wrong");	// display message if username / password is wrong
					 }
      	      } //END :: username and password is not empty	
						
    	 } // END:: isset $this->data
    	 
	   } 
	 

	}
	
	
    /*___________________________________________________________________________________________________
     * 
 	* Method     : home
 	* Purpose    : making listing new user registration
    * Parameters : None
    * 
    * ___________________________________________________________________________________________________
    */
	
	

	function home() {
	   	$this->checkadmin();
	   	$cond=array();
	   /*
	   * Diffrent type of array(User=> User,Userschool,Usertype);
	   */
	  if(!empty($this->params['named'])){
	  	if(isset($this->params['named']['fields']) && isset($this->params['named']['criteria'])){
	  	        if($this->params['named']['fields']=='first_name'){
		        	$cond = array("Userprofile.first_name LIKE'%".$this->params['named']['criteria']."%'");
		           
		           }elseif($this->data['Admin']['fields']=='last_name'){
		
		           	$cond = array("Userprofile.last_name LIKE '%".$this->params['named']['criteria']."%'");
		           
		           }elseif($this->data['Admin']['fields']=='email'){
		           	
		           	$cond = array("User.email LIKE '%".$this->params['named']['criteria']."%'");
		           }
		           $this->set('field',$this->params['named']['fields']);
			    	$this->set('criteria',$this->params['named']['criteria']);
			    	
	  	     }
	       if(isset($this->params['named']['institute'])){
	  	          $cond = array("Userprofile.institute ='".$this->params['named']['institute']."'");
		           $this->set('institute',$this->params['named']['institute']);
	       }
	      if(isset($this->params['named']['usertype'])){
	  	    	if($this->params['named']['usertype']=='All'){
	  	    		$cond='';
	  	    	}else{
	  	    		 $cond = array("User.usertype ='".$this->params['named']['usertype']."'");
	  	    	}      
	      	       $this->set('usertype',$this->params['named']['usertype']);
	       }         
	  	
	  	}else{
	  	switch ($this->data['Admin']['type']) {
	  		case 'User':
			  	if(!empty($this->data['Admin']['criteria'])){
			    	
			    if($this->data['Admin']['fields']=='first_name'){
		        	
		           	$cond = array("Userprofile.first_name LIKE'%".$this->data["Admin"]["criteria"]."%'");
		           
		           }elseif($this->data['Admin']['fields']=='last_name'){
		
		           	$cond = array("Userprofile.last_name LIKE '%".$this->data["Admin"]["criteria"]."%'");
		           
		           }elseif($this->data['Admin']['fields']=='email'){
		           	
		           	$cond = array("User.email LIKE '%".$this->data["Admin"]["criteria"]."%'");
		           }
                    
		            $this->set('field',$this->data['Admin']['fields']);
			    	$this->set('criteria',$this->data['Admin']['criteria']);
		        }
	  		break;
	  		case 'Userschool':
			  	if(!empty($this->data['Admin']['institute'])){
		            $cond = array("Userprofile.institute ='".$this->data["Admin"]["institute"]."'");
		            $this->set('institute',$this->data['Admin']['institute']);
		        }
	  		
	  		break;
	  		case 'Usertype':
				if(!empty($this->data['Admin']['status_fields'])){
			          if($this->data['Admin']['status_fields']=='All'){
			               $cond = '';
			        }else{
			             $cond = array("User.usertype ='".$this->data["Admin"]["status_fields"]."'");
			       }
			       $this->set('usertype',$this->data['Admin']['status_fields']);
			      }
	  		
	  		break;	
	  		default:
	  			;
	  		break;
	  	}
	  	
	  } 	
	  
	    $this->User->find('all',array('fields' => array('User.block','User.id','Userprofile.first_name', 'User.email','Userprofile.payment_status')));
	    $this->paginate = array(
                                    'conditions' =>$cond,
                                    'order' => array('User.id' => 'DESC'),
                                    'limit' => 25
   				  );
	    $data = $this->paginate('User');
	    $this->set('data',$data);
	}
	
	/*_____________________________________________________________________________
	 *
 	* Method     : logout
 	* Purpose    : making new user registration
    * Parameters : None
    * 
    * ______________________________________________________________________________
 	*/
	

    function logout()
           {

                       //$this->checkUserSession();
                       $UserID=$this->Session->read('adminid');
                       $UserName=$this->Session->read('username');
                       $IsAdmin=$this->Session->read('isadmin');
                       $this->Session->delete('adminid');
                       $this->redirect('/admins/');

        }
    
	#_______________________________________________________________________________	
	/*
	*
 	* Method changePassword
 	* Purpose: admin can change their password from this function 
 	* Parameter : None
 	* 
 	* ________________________________________________________________________________
 	*/
    
	function changepassword()
	{      

      // To check whether Admin is logged in or not.
      $this->checkadmin();
      $id 	 	= $this->Session->read('adminid');	
	  $results  = $this->Admin->find('all',array('conditions'=>array('Admin.id' => $id)));	
      
      if(!empty($this->data))//start of if this->data is set
      {    
      		if($this->data['Admin']['oldpassword']=="" && $this->data['Admin']['newpassword']=="" && $this->data['Admin']['confirmpassword']=="")
      		{
		    	$this->Session->setFlash('Please enter your Old, New and Confirm password.');
	  		}
	  		elseif($this->data['Admin']['oldpassword']=="")
	  		{
	  			$this->Session->setFlash('Old password can not be empty.');
	  		}
	  		elseif($this->data['Admin']['newpassword']=="")
	  		{
       		    $this->Session->setFlash('New password can not be empty.');
      		}
      		elseif($this->data['Admin']['confirmpassword']=="")
      		{
                $this->Session->setFlash('New password and confirm password should be matched.');
            }
            elseif(strlen($this->data['Admin']['newpassword'])<7)
            {
                $this->Session->setFlash('New Password should be mininum 7 characters.');
            }
            else // start of else this->data is not blank
            { 
               
 
                if(trim($this->data['Admin']['newpassword'])!='' && $this->data['Admin']['newpassword']==$this->data['Admin']['confirmpassword'] && trim($this->data['Admin']['confirmpassword'])!='')//  START ::    If NEW and Confirm Password match 
                { 
                	if(!empty($results) && $results[0]['Admin']['password']==md5(trim($this->data['Admin']['oldpassword']).$results[0]['Admin']['salt']))//  start of if user exists with old password 
                	{ 
                		$newPassword	=	$this->data['Admin']['newpassword'];
                        if($this->Admin->updateAll(array("Admin.password" => "'".md5($newPassword.$results[0]['Admin']['salt'])."'"),array('Admin.id'=>$id)))
                        {
                        	$this->Session->setFlash('Your Password has been changed successfully.');
                        }
                        else
                        {
                        	$this->Session->setFlash('Here are some error occurs during password changed.');
                        }
					} //  end of If user exists with old password
                  	else //  start of if user entered incorrect old password 
                  	{
                             $this->Session->setFlash('Incorrect Old Password.');                  
                  	}//  end of if user entered incorrect old password
                } //  end of If NEW and Confirm Password match
                else // start of else NEW and Confirm Password do not match
                { 
                             $this->Session->setFlash('New Password and Confirm Password do not match.');
                } //  end of Else NEW and Confirm Password do not match
            } //    end of if this->data is set 

       }
     
     
    
   }
   
   
   

	#_______________________________________________________________________________	
	/*
	* Method activedeactiveuser
 	* Purpose: admin can make active / deactive the user  
 	* Parameter : None
 	* 
 	* _______________________________________________________________________________
 	*/

   function activedeactiveuser($id=NULL,$page=NULL){
        	$this->checkadmin();
        	$a=0;
        $this->autoRender=false;
          if($this->RequestHandler->isAjax()){
             Configure::write('debug', 0);
			   	if(!empty($id)){
				    $results=$this->User->find('all',array('conditions'=>array('MD5(User.id)' =>$id)));
				    pr($results);	
				    
				    if($results[0]['User']['block']=='1'){
				    		$a = 'inactive_'.$id;
							$data=array(
							'block'=>0,
							'id' =>$results[0]['User']['id']);
						}else{
							$a = 'active_'.$id;
							$data=array(
							'block'=>1,
							'id' =>$results[0]['User']['id']);
						}
			   			$qq = "update users set block = '".$data['block']."' where users.id = ".$data['id'];
						$this->User->query($qq);
						echo $a;
										
					}

          }	
   	
   	
   }	
   
  
   	#_______________________________________________________________________________	
	/*
	* Method delete user
 	* Purpose: admin can make deactive the user  
 	* Parameter : None
 	* 
 	* ______________________________________________________________________________
 	*/
    function deleteuser($id=null,$page=null){
    if(!empty($page)){
		   		$page='page:'.$page;
		   	}
    	$this->redirect(array('controller' => 'admins','action' => 'home/'.$page));
    	
    	
    	
    }
   
 	#_______________________________________________________________________________	
	/*
	* Method view detail
 	* Purpose: admin can make deactive the user  
 	* Parameter : None
 	* 
 	* ______________________________________________________________________________
 	*/
    function viewdetail($id=null){
    		$this->checkadmin();
    	$this->layout = '';
    	$results=$this->User->find('all',array('conditions'=>array('MD5(User.id)' =>$id)));
    	
    	$lanuageNative = $this->languagename($results[0]['Userprofile']['nativelanguage']);
    	$lanuageFluent = $this->languagename($results[0]['Userprofile']['fluentlanguage']);
    	$lanuageLearn = $this->languagename($results[0]['Userprofile']['learnlanguage']);
    	$userCountry = $this->countryname($results[0]['Userprofile']['country']);
    	
    	if($results[0]['Userprofile']['state']==0){
	    		$this->set('userState',$results[0]['Userprofile']['otherstate']);
	    		$this->set('userCity',$results[0]['Userprofile']['othercity']);
	    		
    		}else{
	    		$this->set('userState',$this->statename($results[0]['Userprofile']['state']));
	    		$this->set('userCity',$this->cityname($results[0]['Userprofile']['city']));
	    		
    	}
    	
    	
    	
    	$this->set('result',$results[0]);
		$this->set('lanuageNative',$lanuageNative);
		$this->set('lanuageFluent',$lanuageFluent);
		$this->set('lanuageLearn',$lanuageLearn);
		$this->set('userCountry',$userCountry);    		
    	
    	
    		
    }
  /*_______________________________________________________________________________	
	*
	* Method view edivdetail
 	* Purpose: admin can view ediv user detail 
 	* Parameter : $id
 	* 
 	* ______________________________________________________________________________
 	*/  
 function edivdetail($id=NULL){
 		$this->checkadmin();
            $this->layout = '';
 	    $results=$this->User->find('all',array('conditions'=>array('MD5(User.id)' =>$id)));
 	    $this->set('name',ucwords($results[0]['Userprofile']['first_name']).' '.ucwords($results[0]['Userprofile']['last_name']));
 		$edivpoints = $this->Ediv_user_master->find('all',array('conditions'=>array('MD5(Ediv_user_master.uid)' =>$id)));
 	    $this->set('edivpoints',$edivpoints);
 	    //// For ediv points ///
            $edivData=array();
            $edivQuery = $this->Ediv_user_master->query('SELECT DATE_FORMAT(DATETIME,"%M")  AS MONTH , SUM(points) AS edivTotPoints FROM ediv_user_masters WHERE MD5(uid) ="'.$id.'" AND ediv_type NOT IN(6) GROUP BY DATE_FORMAT(DATETIME,"%M")');
            for($i=0;$i<count($edivQuery);$i++){
                $edivData[$i]['month']=$edivQuery[$i][0]['MONTH'];
                $edivData[$i]['totPts']=round($edivQuery[$i][0]['edivTotPoints']);
                $edivData[$i]['ref']=0;
                $edivData[$i]['poll']=0;
                $edivData[$i]['lang']=0;
                $edivData[$i]['quiz']=0;
                $edivData[$i]['sponsor']=0;
                $edivData[$i]['studentQuiz']=0;
                $edivData[$i]['studentPoll']=0;
                $edivData[$i]['cafe']=0;

            $edivDetails = $this->Ediv_user_master->query('SELECT ediv_type, SUM(points) AS edivPoints FROM `ediv_user_masters` where MD5(uid) ="'.$id.'" AND DATE_FORMAT(DATETIME,"%M")="'.$edivQuery[$i][0]['MONTH'].'" GROUP BY ediv_type');
            for($j=0;$j<count($edivDetails);$j++){

                if($edivDetails[$j]['ediv_user_masters']['ediv_type']=='1'){
                $edivData[$i]['ref'] = round($edivDetails[$j][0]['edivPoints']);
                }
                if($edivDetails[$j]['ediv_user_masters']['ediv_type']=='3'){
                $edivData[$i]['poll'] = round($edivDetails[$j][0]['edivPoints']);
                }
                if($edivDetails[$j]['ediv_user_masters']['ediv_type']=='4'){
                $edivData[$i]['lang'] = round($edivDetails[$j][0]['edivPoints']);
                }
                if($edivDetails[$j]['ediv_user_masters']['ediv_type']=='8'){
                $edivData[$i]['quiz'] = round($edivDetails[$j][0]['edivPoints']);
                }
                if($edivDetails[$j]['ediv_user_masters']['ediv_type']=='9'){
                $edivData[$i]['sponsor'] = round($edivDetails[$j][0]['edivPoints']);
                }
                if($edivDetails[$j]['ediv_user_masters']['ediv_type']=='10'){
                $edivData[$i]['studentQuiz'] = round($edivDetails[$j][0]['edivPoints']);
                }
                if($edivDetails[$j]['ediv_user_masters']['ediv_type']=='11'){
                $edivData[$i]['studentPoll'] = round($edivDetails[$j][0]['edivPoints']);
                }
                if($edivDetails[$j]['ediv_user_masters']['ediv_type']=='12'){
                $edivData[$i]['cafe'] = round($edivDetails[$j][0]['edivPoints']);
                }
           
            }
	}
         $this->set('edivAllValues',$edivData);
 }

function referdetails($id=NULL) {
    $this->checkadmin();
    	$this->layout = '';
        $results=$this->User->find('all',array('conditions'=>array('MD5(User.id)' =>$id)));
 	    $this->set('name',ucwords($results[0]['Userprofile']['first_name']).' '.ucwords($results[0]['Userprofile']['last_name']));
        $colectRef = $this->Userprofile->find('all',array('conditions'=>'Userprofile.referrals = "'.$results[0]['User']['id'].'"  '));
        $this->set('colectRef',$colectRef);
        $this->set('backid',$id);
       //echo $id;
 }
 
#_______________________________________________________________________________	
	/*
	* Method view detail
 	* Purpose: admin can make deactive the user  
 	* Parameter : None
 	* 
 	* ______________________________________________________________________________
 	*/
    function viewrefdetail($id=null,$back=NULL){
    		$this->checkadmin();
    	$this->layout = '';
    	$results=$this->User->find('all',array('conditions'=>array('MD5(User.id)' =>$id)));
    	
    	$lanuageNative = $this->languagename($results[0]['Userprofile']['nativelanguage']);
    	$lanuageFluent = $this->languagename($results[0]['Userprofile']['fluentlanguage']);
    	$lanuageLearn = $this->languagename($results[0]['Userprofile']['learnlanguage']);
    	$userCountry = $this->countryname($results[0]['Userprofile']['country']);
    	
    	if($results[0]['Userprofile']['state']==0){
	    		$this->set('userState',$results[0]['Userprofile']['otherstate']);
	    		$this->set('userCity',$results[0]['Userprofile']['othercity']);
	    		
    		}else{
	    		$this->set('userState',$this->statename($results[0]['Userprofile']['state']));
	    		$this->set('userCity',$this->cityname($results[0]['Userprofile']['city']));
	    		
    	}
    	
    	
    	
    	$this->set('result',$results[0]);
		$this->set('lanuageNative',$lanuageNative);
		$this->set('lanuageFluent',$lanuageFluent);
		$this->set('lanuageLearn',$lanuageLearn);
		$this->set('userCountry',$userCountry); 
		
		$this->set('refid',$back);
		   		
    	
    	
    		
    }

/*_______________________________________________________________________________
* Method staticinfo
* Purpose: admin can make new staticinfo
* Parameter : None
*
* ______________________________________________________________________________
*/
function staticinfo(){

    $this->layout = 'admin';
     // Check admin athontication //
            $this->checkadmin();
            $this->Staticcontent->find('all');
            $this->paginate = array(
                     'limit' => 25,
                     'page'=>1,

                             );

         $data = $this->paginate('Staticcontent');
     $this->set('data',$data);

}

/*_______________________________________________________________________________
* Method newaboutus
* Purpose: admin can make new newcharity
* Parameter : None
*
* ______________________________________________________________________________
*/
 function newstaticinfo(){
    	$this->layout = 'admin';
   	 // Check admin athontication //
		$this->checkadmin();
    		  if(!empty($this->data)){
    				$this->data['Staticcontent']['status'] = 1;
    				if($this->Staticcontent->save($this->data)){
    		  			$this->Session->setFlash("New Content save ");
    		  			$this->redirect('staticinfo');
    	 		}

            }
   }

/*_______________________________________________________________________________
* Method editaboutus
* Purpose: admin can edit about us
* Parameter : None
*
* ______________________________________________________________________________
*/
 function editstaticinfo($id=NULL){
  // Check admin athontication //
	   $this->checkadmin();
	   $this->Staticcontent->id = $id;
       if(empty($this->data)) {
          $this->data = $this->Staticcontent->read();
          $this->set('id',$id);
       }else{
       	//$this->data['Aboutus']['content'] = addslashes(htmlspecialchars_decode(($this->data['Aboutus']['content'])));
        if($this->Staticcontent->save($this->data)) {
		    	$this->Session->setFlash('Your post has been updated.');
				$this->redirect(array('action' => 'staticinfo'));
		 }
       	 $this->set('id',$id);
       }
   }
#_______________________________________________________________________________
/*
* Method aboutusactivedeactive
* Purpose: admin can make active / deactive the tab
* Parameter : None
*
* _______________________________________________________________________________
*/

   function staticinfoactivedeactive($id=NULL){
        	$this->checkadmin();
        	$a=0;
        $this->autoRender=false;
          if($this->RequestHandler->isAjax()){
             Configure::write('debug', 0);
			   	if(!empty($id)){
				    $results=$this->Staticcontent->find('all',array('conditions'=>array('MD5(Staticcontent.id)' =>$id)));

				    if($results[0]['Staticcontent']['status']=='1'){
				    		$a = 'inactive_'.$id;
							$data=array(
							'status'=>0,
							'id' =>$results[0]['Staticcontent']['id']);
						}else{
							$a = 'active_'.$id;
							$data=array(
							'status'=>1,
							'id' =>$results[0]['Staticcontent']['id']);
						}
			   			$qq = "update static_contents set status = '".$data['status']."' where static_contents.id = ".$data['id'];
						$this->Staticcontent->query($qq);
						echo $a;

					}

          }


   }
/*_______________________________________________________________________________
* Method deletestaticinfo
* Purpose: admin can delete record
* Parameter : None
*
* ______________________________________________________________________________
*/
function deletestaticinfo($id=NULL){

	$this->Staticcontent->delete($id);
 	$this->Session->setFlash('Your post deleted successfully.');
 	$this->redirect(array('action' => 'staticinfo'));
 }
/*_______________________________________________________________________________
* Method statickeyval
* Purpose: admin can make static key values
* Parameter : None
*
* ______________________________________________________________________________
*/
function statickeyval(){

     $this->layout = 'admin';
     $this->checkadmin();
     if($this->data['check']['final']=='final'){
         for($i=0;$i<count($this->data['Admin']);$i++){
            $j=$i+1;
            $this->Statickeyvalue->updateAll(array('value' => '"'.$this->data["Admin"]["val_eng_".$j].'"', 'value_chn' => '"'.$this->data["Admin"]["val_chn_".$j].'"'),array('id' =>$j));
         }
         $this->Session->setFlash('Your post has been updated.');
        // $this->redirect("statickeyval");
     }
     $data = $this->Statickeyvalue->find('all');
     $this->set('data',$data);

}
/*_______________________________________________________________________________
* Method adminemailaddress
* Purpose: admin can make his own address group
* Parameter : None
*
* ______________________________________________________________________________
*/
function adminemailaddress(){

       // Check admin athontication //
		$this->checkadmin();
		$this->Adminaddbook->find('all');
		$this->paginate = array(
                         'limit' => 25,
                         'page'=>1,

    				 );

	     $data = $this->paginate('Adminaddbook');
             $this->set('data',$data);

}
/*_______________________________________________________________________________
* Method newsadminadd
* Purpose: admin can make new address of email
* Parameter : None
*
* ______________________________________________________________________________
*/
 function newadminadd(){
    	$this->layout = 'admin';
   	 // Check admin athontication //
                pr($this->data);
		$this->checkadmin();
                                  $system_email_address ='';
                                 if(!empty($this->params['form']['systememailadmin'])){
                                    for($i=0;$i<count($this->params['form']['systememailadmin']);$i++){
                                        $system_email_address =$system_email_address .','. $this->params['form']['systememailadmin'][$i];
                                     }
                                     $sysemail = substr($system_email_address,1);

                                    }else{
                                        $sysemail = '';
                                    }
                                    if(!empty($this->data)){
                                        $this->data['Adminaddbook']['status'] = 1;
                                        $this->data['Adminaddbook']['system_email_address'] = $sysemail;

                                        if($this->Adminaddbook->save($this->data)){
                                            $this->Session->setFlash("New Content save ");
                                            $this->redirect('adminemailaddress');
                                        }
                                    }

            $user=$this->User->find('all',array('field'=>'id,email'));

            $this->set('user',$user);
   }

/*_______________________________________________________________________________
* Method editadminadd
* Purpose: admin can edit address of email
* Parameter : None
*
* ______________________________________________________________________________
*/
function editadminadd($id=NULL){


         $system_email_address ='';
         if(!empty($this->params['form']['systememailadmin'])){
            for($i=0;$i<count($this->params['form']['systememailadmin']);$i++){
                $system_email_address =$system_email_address .','. $this->params['form']['systememailadmin'][$i];
             }
             $sysemail = substr($system_email_address,1);

            }else{
                $sysemail = '';
            }
        if(!empty($this->data)){
                $this->data['Adminaddbook']['id'] = $id;
                $this->data['Adminaddbook']['status'] = 1;
                $this->data['Adminaddbook']['system_email_address'] = $sysemail;

                if($this->Adminaddbook->save($this->data)){
                    $this->Session->setFlash("Edit contact saved ");
                    $this->redirect('adminemailaddress');
                }

        }else{
            $str = '';
            $data = $this->Adminaddbook->find('all',array('conditions'=>'id = '.$id));
            
            
            if(!empty($data[0]['Adminaddbook']['email_address'])){
               $found = " '".str_replace(",", "','", $data[0]['Adminaddbook']['system_email_address'])."' ";
            }else{
                $found = "''";
            }
            //echo $found;
            
            $user=$this->User->find('all',array('field'=>'id,email','conditions' =>'User.email NOT IN ('.$found.')'));
            //pr($user);
            $this->set('data',$data);
            $this->set('user',$user);
        }

}
/*_______________________________________________________________________________
* Method deleteadminadd
* Purpose: admin can delete record
* Parameter : None
*
* ______________________________________________________________________________
*/
function deleteadminadd($id=NULL){

	$this->Adminaddbook->delete($id);
 	$this->Session->setFlash('Your post deleted successfully.');
 	$this->redirect(array('action' => 'adminemailaddress'));
 }
 /*
* Method adminaddactivedeactive
* Purpose: admin can make active / deactive the tab
* Parameter : None
*
* _______________________________________________________________________________
*/

function adminaddactivedeactive($id=NULL){
    $this->checkadmin();
    $a=0;
$this->autoRender=false;
if($this->RequestHandler->isAjax()){
 Configure::write('debug', 0);
                    if(!empty($id)){
                        $results=$this->Adminaddbook->find('all',array('conditions'=>array('MD5(Adminaddbook.id)' =>$id)));

                        if($results[0]['Adminaddbook']['status']=='1'){
                                    $a = 'inactive_'.$id;
                                            $data=array(
                                            'status'=>0,
                                            'id' =>$results[0]['Adminaddbook']['id']);
                                    }else{
                                            $a = 'active_'.$id;
                                            $data=array(
                                            'status'=>1,
                                            'id' =>$results[0]['Adminaddbook']['id']);
                                    }
                                    $qq = "update admin_emailaddresses set status = '".$data['status']."' where admin_emailaddresses.id = ".$data['id'];
                                    $this->Adminaddbook->query($qq);
                                    echo $a;

                            }

}


}
/*
* Method testimonial
* Purpose: list content
* Parameter : None
*
* _______________________________________________________________________________
*/
function testimonial($id=NULL){
    $this->checkadmin();
     $this->layout = 'admin';
     // Check admin athontication //
     if(isset($id) && $id!=''){
         if(isset($this->data['Testimonial']['val'])){
          $this->Testimonial->updateall(array('testimonial'=>'"'.$this->data['Testimonial']['testimonial'].'"'),array('Testimonial.id'=>$id));
          $this->Session->setFlash('testimonial updated successfully.');
 	  $this->redirect(array('action' => 'testimonial'));
          } else{

         $this->Testimonial->id=$id;
         $this->data=$this->Testimonial->read();
         $this->set('id',$id);
         $this->set('editdata','editdata');
         }
     }else{
            $this->Testimonial->find('all');
            $this->paginate = array(
                     'limit' => 25,
                     'page'=>1,

                             );

         $data = $this->paginate('Testimonial');
        $this->set('data',$data);
     }

}
/*
* Method activedeactivetestimonial
* Purpose: list content
* Parameter : None
*
* _______________________________________________________________________________
*/
function activedeactivetestimonial($id=NULL){
    $this->checkadmin();
    $a=0;
    $this->autoRender=false;
if($this->RequestHandler->isAjax()){
 Configure::write('debug', 0);
                    if(!empty($id)){
                        $results=$this->Testimonial->find('all',array('conditions'=>array('MD5(Testimonial.id)' =>$id)));

                        if($results[0]['Testimonial']['status']=='1'){
                                    $a = 'inactive_'.$id;
                                            $data=array(
                                            'status'=>0,
                                            'id' =>$results[0]['Testimonial']['id']);
                                    }else{
                                            $a = 'active_'.$id;
                                            $data=array(
                                            'status'=>1,
                                            'id' =>$results[0]['Testimonial']['id']);
                                    }
                                    $qq = "update testimonial_masters set status = '".$data['status']."' where testimonial_masters.id = ".$data['id'];
                                    $this->Testimonial->query($qq);
                                    echo $a;

                            }

}


}
/*_______________________________________________________________________________
* Method deletetestimonial
* Purpose: admin can delete record
* Parameter : None
*
* ______________________________________________________________________________
*/
function deletetestimonial($id=NULL){

	$this->Testimonial->delete($id);
 	$this->Session->setFlash('Testimonial deleted successfully.');
 	$this->redirect(array('action' => 'testimonial'));
 }

/////////////////////////////
 /*_______________________________________________________________________________
	* Method charity
 	* Purpose: admin can make new charity
 	* Parameter : None
 	*
 	* ______________________________________________________________________________
 	*/
    function charity(){

    	$this->checkadmin();
    	           $this->Charity->find('all');
	               $this->paginate['Charity']=array('page'=>1,'limit'=>10);
	               $data = $this->paginate('Charity');

	               $this->set('data',$data);

                   $this->set('switch','');



    }
    /*_______________________________________________________________________________
	* Method newcharity
 	* Purpose: admin can make new charity
 	* Parameter : None
 	*
 	* ______________________________________________________________________________
 	*/
    function newcharity(){

       if(isset($this->data['Admin']['val']) && $this->data['Admin']['val']=='newCharitysave'){
     	    $this->data['Charity']['definition'] = @htmlspecialchars_decode($this->data['Charity']['definition']);
			$this->data['Charity']['detail'] = @htmlspecialchars_decode($this->data['Charity']['detail']);
		    $this->data['Charity']['publish']  = 1;
		    $this->data['Charity']['logo'] = $this->data['Charity']['logo']['name'];
    	    $this->data['Charity']['post_date']= date('Y-m-d H:i:s');

			$this->Charity->set($this->data);
    	    if($this->Charity->validates()) {
           	 $rval = $this->Usercomponent->imageGenerateKey();
           	 $iname=$rval.'_'.$_FILES['data']['name']['Charity']['logo'];
           	 if(move_uploaded_file($_FILES['data']['tmp_name']['Charity']['logo'] ,_ROOT_BASE_PATH."img/charity/".$iname)){
           	 	$this->data['Charity']['logo_randname'] = $iname;
           	 	$this->Charity->save($this->data);
           	 	$this->redirect('charity');
           	 }


           }


       }
    }

     /*_______________________________________________________________________________
	* Method editcharity
 	* Purpose: admin can edit charity
 	* Parameter : None
 	*
 	* ______________________________________________________________________________
 	*/
    function editcharity($id=NULL){

    	 if(isset($this->data['Admin']['val']) && $this->data['Admin']['val']=='editCharity'){

    	    $this->data['Charity']['definition'] = @htmlspecialchars_decode($this->data['Charity']['definition']);
			$this->data['Charity']['detail'] = @htmlspecialchars_decode($this->data['Charity']['detail']);
		   if($this->data['Charity']['definition']!=''){
			$this->Charity->updateAll(array('Charity.definition' =>' "' .$this->data['Charity']['definition'].' " '), array('Charity.id' => $this->data['Charity']['id']));
		   }
		   if($this->data['Charity']['definition']!=''){
			$this->Charity->updateAll(array('Charity.detail' =>' "' .$this->data['Charity']['detail'].' " ' ), array('Charity.id' => $this->data['Charity']['id']));
		   }
		   if($this->data['Charity']['title']!=''){
			$this->Charity->updateAll(array('Charity.title' =>' "' .$this->data['Charity']['title'].' " ' ), array('Charity.id' => $this->data['Charity']['id']));
		   }
			 $this->redirect('charity');

    	 }else{

	    		$val = $this->Charity->find('all',array('conditions'=>'id = '.$id));
	    		$this->set('data',$val[0]['Charity']);

	    }


    }
/*_______________________________________________________________________________
	*
	* Method editimage
 	* Purpose: admin can make edit image
 	* Parameter : None
 	*
 	* ______________________________________________________________________________
 	*/
    function editimage($id=null){
    	$this->checkadmin();
    	if($this->data['Charity']['logo']!=''){

    		$ext = explode('/', $_FILES['data']['type']['Charity']['logo']);
    		if($ext[1]=='jpeg' || $ext[1]=='jpeg' || $ext[1]=='JPEG' || $ext[1]=='png' || $ext[1]=='JPG' || $ext[1]=='jpg' || $ext[1]=='PNG' || $ext[1]=='GIF' || $ext[1]=='gif'){
    			$rval = $this->Usercomponent->imageGenerateKey();
           	    $iname=$rval.'_'.$_FILES['data']['name']['Charity']['logo'];
           	    if(move_uploaded_file($_FILES['data']['tmp_name']['Charity']['logo'] ,_ROOT_BASE_PATH."img/charity/" . $iname)){
					;

					@chmod(_ROOT_BASE_PATH."img/charity/", 0777);

					@chmod(_ROOT_BASE_PATH."img/charity/" .$imName, 0777);

					@unlink(_ROOT_BASE_PATH."img/charity/" .$imName);



                    $this->Charity->updateAll(array('Charity.logo' =>' "' .$_FILES['data']['name']['Charity']['logo'].' " ' , 'Charity.logo_randname' =>' "' .$iname.' " ' ), array('Charity.id' => $id));
           	    	$this->Session->setFlash('Logo updated successfully');
    			    $this->redirect('editcharity/'.$id);
           	    }

    		}else{
    			$this->Session->setFlash('Invalid file type');
    			$this->redirect('editcharity/'.$id);

    		}

    	}
    	else{

    	$this->layout = '';
    	$results=$this->Charity->find('all',array('conditions'=>array('id' =>$id)));

    	$this->set('result',$results[0]);
    	}

    }
/*______________________________________________________________________________
	*
	* Method charityactivedeactive
 	* Purpose: admin can make deactive the user
 	* Parameter : None
 	*
 	* _______________________________________________________________________________
 	*/
   function charityactivedeactive($id=NULL){
        	$this->checkadmin();

				if(!empty($id)){
					//$this->User->unbindAll();

				    $results=$this->Charity->find('all',array('conditions'=>array('MD5(id)' =>$id)));

				    //pr($results);


				    if($results[0]['Charity']['publish']=='1'){

				    	$this->Charity->updateAll(array('Charity.publish' =>0 ), array('MD5(id)' => $id));

					}else{
						$this->Charity->updateAll(array('Charity.publish' =>1 ), array('MD5(id)' => $id));

					}
					//$results=$this->Charity->find('all',array('conditions'=>array('MD5(id)' =>$id)));
					//pr($results);
					$this->redirect(array('controller' => 'admins','action' => 'charity'));

				}


   }

/*_______________________________________________________________________________
* Method staticloungeval
* Purpose: admin can make static key values
* Parameter : None
*
* ______________________________________________________________________________
*/
function staticloungeval(){

     $this->layout = 'admin';
     $this->checkadmin();
     if($this->data['check']['final']=='final'){
         for($i=0;$i<count($this->data['Admin']);$i++){
            $j=$i+1;
            $this->Staticloungevalue->updateAll(array('value' => '"'.$this->data["Admin"]["val_eng_".$j].'"', 'value_chn' => '"'.$this->data["Admin"]["val_chn_".$j].'"'),array('id' =>$j));
         }
         $this->Session->setFlash('Your post has been updated.');
        // $this->redirect("statickeyval");
     }
     $data = $this->Staticloungevalue->find('all');
     $this->set('data',$data);

}

}//////////// Class end
?>
