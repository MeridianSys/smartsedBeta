<?php
/* 
#################################################################################
#																				#
#   User Controller 															#
#   file name        	: referral_controller.php								#
#   Developed        	: Meridian Radiology 									#	
#   Intialised Variables: name , helpers ,components , uses , layout = (default)#
#																				#
#																				#
#################################################################################
*/
class ReferralsController extends AppController {

	
	 var $name = 'Referrals';
	 var $uses = array();
	 var $helpers = array('html', 'Form', 'javascript','Ajax','session','Thickbox');
	  var $components = array( 'Session','Email','RequestHandler');
	 var $layout = '';
	 var $conf = array();
	 
/*___________________________________________________________________________________________________
* 
* Method     : Emailtouser
* Purpose    : making for sending the email to users
* Parameters : None
* 
* ___________________________________________________________________________________________________
*/	 
function emailtouser(){
	
   	    // Check admin athontication // 
		$this->checkuserlogin();
		$id = $this->Session->read('User.id');
		$name = $this->UserFirstName($id);
		$email=explode(',' , $this->data['Referrals']['useremail']);
		
		for($i=0;$i<count($email);$i++){
			
			if($this->isValidEmail($email[$i])){
				
				$this->referuseremail($email[$i],$name,base64_encode($id));
				
					
				
			}
			
			
		}
		//pr($this->params);
		$this->redirect($this->referer());
 	
}
/*___________________________________________________________________________________________________
* 
* Method     : isValidEmail
* Purpose    : making for sending the email to users
* Parameters : None
* 
* ___________________________________________________________________________________________________
*/		
function isValidEmail($email){
 if (preg_match("/^[a-zA-Z0-9\.]+@[a-zA-Z0-9\-]+\.[a-zA-Z0-9\.]+$/i" , $email)){ 
  	return true;      
  }else {    
  	return false;
   } 
}    
 /*___________________________________________________________________________________________________
* 
* Method     : isValidEmail
* Purpose    : making for sending the email to users
* Parameters : None
* 
* ___________________________________________________________________________________________________
*/	   
function referuseremail($email,$fname,$id){
    $this->Email->to = $email;
    $this->Email->bcc = array('nikesh.kumar@meridiansys.com');
    $this->Email->subject = $this->communicationsubject(12);//'New Registration with Smartsed.com';
    $this->Email->from = 'Smartsed Web App <admin@smartsed.com>';
    $this->Email->template = 'referral'; // note no '.ctp'
    $this->set('message', $this->communicationmessage(12));
    $this->set('bottom', $this->communicationbottom(12));
    $this->set('name', $fname);
    $aid = md5('a');
    //$this->set('activation_url' , "<a href=\""._HTTP_PATH."users/register/".$id."\">"._HTTP_PATH."users/register/".$id."</a>");
    $this->set('activation_url' , "<a href=\""._HTTP_PATH."users/index/".$aid."/".$id."\">"._HTTP_PATH."users/index/".$aid."/".$id."</a>");

    $this->Email->sendAs = 'both';
    if($this->Email->send()){
        return true;
    }else{
        return false;
    }

}

    

}// End Class
?>
