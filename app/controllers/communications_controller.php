<?php
/* 
#################################################################################
#																				#
#   User Controller 															#
#   file name        	: communications_controller.php							#
#   Developed        	: Meridian Radiology 									#	
#   Intialised Variables: name , helpers ,components , uses , layout = (admin)  #
#																				#
#																				#
#################################################################################
*/
class CommunicationsController extends AppController {

	 var $name = 'Communications';
	 var $helpers = array('html', 'Form', 'javascript','Ajax','Session','Thickbox','Crypt','Fck');
	 var $layout = 'admin';
	 var $conf = array();
	 var $uses = array('Communication','User');
	 var $components = array('Email','Session');
	 
	 
	
/*___________________________________________________________________________________________________
* 
* Method     : index
* Purpose    : making list of the comminication
* Parameters : None
* 
* ___________________________________________________________________________________________________
*/	 
function index($id=NULL){
	 $this->layout = 'admin';
   	 // Check admin athontication // 
		$this->checkadmin();
		
	     $data = $this->Communication->find('all');
	     $this->set('data',$data);	
         if($id!=''){
         	$this->set('msg',$id);
         }
    	
    }
/*___________________________________________________________________________________________________
* 
* Method     : editmailcontent
* Purpose    : making list of the comminication
* Parameters : None
* 
* ___________________________________________________________________________________________________
*/	 
function editmailcontent($id=NULL){
	 $this->layout = 'admin';
   	 // Check admin athontication // 
		$this->checkadmin();

		if(!empty($this->data)){
                 $this->Communication->set($this->data);
		   if($this->Communication->validates($this->data))
			{
                         $this->Communication->save($this->data);
			 $this->Session->setFlash('Data successfully updated');
                         $this->redirect('index');
			}else{
                            $data = $this->Communication->find('all',array('conditions'=>'id = '.$id));
		            $this->set('data',$data);
                        }
			
							
		}else{
		$data = $this->Communication->find('all',array('conditions'=>'id = '.$id));
		$this->set('data',$data);
		}
	  
}
/*___________________________________________________________________________________________________
* 
* Method     : emailtorewarduser
* Purpose    : making list of the comminication
* Parameters : None
* 
* ___________________________________________________________________________________________________
*/	 
function emailannouncementreminder($email,$fname,$id){
    //$this->Email->to = 'shashibhushan0@gmail.com';
    $mailmessage = $this->Communication->find('all',array('conditions'=>'id = '.$id));
	$this->Email->to = $email;
    $this->Email->subject = $mailmessage[0]['Communication']['tag'];
	$this->Email->bcc = array('nikesh.kumar@meridiansys.com');
    $this->Email->from = 'Smartsed Web App <admin@smartsed.com>';
    $this->Email->template = 'announcement'; // note no '.ctp'
    $this->set('message' , $mailmessage[0]['Communication']['message']);
    $this->set('name', $fname);

    $this->Email->sendAs = 'both';
    if($this->Email->send()){
        
        return true;
    }else{
        return false;
        
    }

}



}// End Class
?>
