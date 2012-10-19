<?php
/* 
#################################################################################
#																				#
#   Admin Charity Controller 													#
#   file name        	: charities_controller.php								#
#   Developed        	: Meridian Radiology 									#	
#   Intialised Variables: name , helpers ,components , uses , layout = (admin)  #
#																				#
#																				#
#################################################################################
*/
class CharitiesController extends AppController {

 	 var $name = 'Charities';
     var $helpers = array('html', 'Form', 'javascript','Ajax','session','Thickbox','Fck');
	 var $layout = 'admin';
	 var $uses = array('User','Userprofile','Admin','Charity','users_professional_tracks','Institution_list','Charityuser','Quizuserattempt','Charityuserfriend','Edivcharityvalue','Charityfeedback','Inbox');
	 var $components = array('Usercomponent','Session','Email','RequestHandler');
     var $paginate = array('limit' => 25 );
/*___________________________________________________________________________________________________
 * 
* Method     : charitydetail
* Purpose    : making list of the charity
* Parameters : None
* 
* ___________________________________________________________________________________________________
*/	 
function charitydetail(){
		
	    // Check admin athontication // 
		$this->checkadmin();
		$charityVal       = $this->Charityuser->query("SELECT  (SELECT title FROM charities WHERE id = charity_id) AS charityname , charity_id  AS charityId , SUM(target) AS Goal, COUNT(id) AS totalUser    FROM charity_users GROUP BY charity_id ");//charity_id, SUM(target) AS Goal, COUNT(id) AS totalUser FROM charity_users GROUP BY charity_id");
		$carityCollection = $this->Charityuser->query("SELECT charity_users.charity_id as cId, SUM(quiz_user_attempts.points) AS collection  FROM charity_users LEFT JOIN quiz_user_attempts ON charity_users.uid = quiz_user_attempts.uid GROUP BY charity_id");
		$multiplier = $this->Edivcharityvalue->find('all',array('conditions'=>'Edivcharityvalue.status=1','limit'=>1));
		$this->set('multicent',$multiplier[0]['Edivcharityvalue']['ediv_charity_amount']);
		$this->set('charityVal',$charityVal);
        $this->set('carityCollection',$carityCollection);   	    
   	    //$this->set('collection',$carityCollection);
		
}	 
	 
function charityuserdetail($id=NULL,$charityName=NULL){
	
	   $this->checkadmin();
	   $multiplier = $this->Edivcharityvalue->find('all',array('conditions'=>'Edivcharityvalue.status=1','limit'=>1));
	   $this->Charityuser->bindModel(
        array(
            'belongsTo' => 
            array(
                   'Quizuserattempt' =>
	                   array(
	                        'fields'                => "(SUM(Quizuserattempt.points)/".$this->pointforediv(8).")*(".($multiplier[0]['Edivcharityvalue']['ediv_charity_amount']/100).") as Collect",
	                        'type'                  => 'LEFT',
	                        'foreignKey'             => false,
	                        'conditions'             => 'Quizuserattempt.uid = Charityuser.uid'
	               ),
                )
                
            ),false
         ); 
          $this->Charityuser->recursive =1;  
        // $data = $this->Charityuser->find('all',array('conditions' =>'Charityuser.charity_id = '.$id,'group' => 'Charityuser.uid'));
         $this->paginate =
                     array(
                         'limit' => 25,
                          'conditions' =>'Charityuser.charity_id = '.$id,
                          'group' => 'Charityuser.uid'
    				 );
	     $data = $this->paginate('Charityuser');
         $this->set('charityName',$charityName);
	     $this->set('data',$data);
	     $this->set('id',$id);
	   
	
	
	
}	 

/// Function For Checking ///

function check123(){
	 $this->checkadmin();
	 $this->Charityuser->bindModel(
        array(
            'belongsTo' => 
            array(
                   'Quizuserattempt' =>
	                   array(
	                        
	                        'type'                  => 'LEFT',
	                        'foreignKey'             => false,
	                        'conditions'             => 'Quizuserattempt.uid = Charityuser.uid'
	               ),
                )
                
            ),false
         ); 
	    $this->Charityuser->recursive =1;  
        $this->paginate = array(
                         'fields'=> 'Charityuser.id,Charityuser.target,(SUM(Quizuserattempt.points)/5)*.025 as Collect',
                         'limit' => 30,
                         'page'=>1,
                          'conditions' =>'Charityuser.charity_id = 5',
                          'group' => array('Charityuser.uid')
    				 );
	     $data = $this->paginate('Charityuser');
         $this->set('data',$data);
    
}
/*
* ___________________________________________________________________
* Method friendfamilycharityamount
* Purpose: collect charity amount 
* Parameters : None
*
* _______________________________________________________________________
*/

function friendfamilycharityamount(){
	 $this->layout='default';
	 $this->Charityuserfriend->updateAll(array('Charityuserfriend.acknowledge' =>2,'Charityuserfriend.amount' =>'"'.$this->data['Charities']['amount'].'"','Charityuserfriend.friend_firstname' =>'"'.$this->data['Charities']['friend_firstname'].'"','Charityuserfriend.friend_lastname' =>'"'.$this->data['Charities']['friend_lastname'].'"','Charityuserfriend.transection_date' =>'"'.date('Y-m-d H:i:s').'"', 'Charityuserfriend.payment_method' =>"'Dummy Payment Method'"),array('Charityuserfriend.id'=>$this->data['Charities']['id'],'Charityuserfriend.uid'=>$this->data['Charities']['uid']));
	 $this->userfriendemailpaymentnotify($this->data['Charities']['id']);
	 $this->redirect('friendfamilycharity/'.base64_encode($this->data['Charities']['id']).'/thanks');	
}
/*
* ___________________________________________________________________
* Method friendfamilycharity
* Purpose: collect charity  
* Parameters : None
*
* _______________________________________________________________________
*/

function friendfamilycharity($id=NULL,$thanks=NULL){
	 $this->layout='default';
	 $familyId = base64_decode($id);
	 //echo $familyId;OA==
	 //echo base64_encode(9);
     // Second Parameter //
     if($thanks!=''){
     	$result =  $this->Charityuserfriend->find('all',array('conditions'=>'Charityuserfriend.id='.$familyId));
     	$resultCharity =  $this->Charity->find('all',array('conditions'=>'Charity.id='.$result[0]['Charityuserfriend']['charity_id']));//
		$this->set('id',$familyId);
	    $this->set('resultCharity',$resultCharity);
	    $this->set('data',$result[0]['Charityuserfriend']['uid']);
     	$this->set('thanks','firstdone');
     }else{
	 	 $result =  $this->Charityuserfriend->find('all',array('conditions'=>'Charityuserfriend.id='.$familyId));
		 // Only one time payment System
		 if($result[0]['Charityuserfriend']['amount']==''){
		 $resultCharity =  $this->Charity->find('all',array('conditions'=>'Charity.id='.$result[0]['Charityuserfriend']['charity_id']));//
		 $this->set('id',$familyId);
	     $this->set('resultCharity',$resultCharity);
	     $this->set('data',$result[0]['Charityuserfriend']['uid']);
	     }else{
		 $resultCharity =  $this->Charity->find('all',array('conditions'=>'Charity.id='.$result[0]['Charityuserfriend']['charity_id']));//
		 $this->set('id',$familyId);
	     $this->set('resultCharity',$resultCharity);
	     $this->set('data',$result[0]['Charityuserfriend']['uid']);
	     $this->set('thanks','previousgiven');
		 }
     }
}		 
/*
* ___________________________________________________________________
* Method selectcharity
* Purpose: Display charity 
* Parameters : None
*
* _______________________________________________________________________
*/

function selectcharity($idd=NULL){
     $this->checkuserlogin(); 
	 $this->layout='default';
	 $this->set('no',$idd);
	 $id = $this->Session->read('User.id');
     $records = $this->Charity->find('all',array('conditions'=>'publish = 1'));
     $this->set('data',$records);
     if($this->data['Charityuser']['final']=='final'){
    	 if($this->data['Charityuser']['charity_id'] != '' && $this->data['Charityuser']['target'] != ''){
    	 	$this->data['Charityuser']['uid']=$this->Session->read('User.id');
    	 	$this->data['Charityuser']['start_date'] = date('Y-m-d H:i:s');
    	 	$this->data['Charityuser']['status'] = 1;
    	 	
    	 	$this->Charityuser->save($this->data);
    	 	$friendemail = explode(',',$this->data['Charityuser']['userfriendfamily']);
    	 	$name = $this->UserFirstName($id);
    	 	// Sending Email process //base64_encode($id)
	    	 for($i=0;$i<count($friendemail);$i++){
				if($this->isValidEmail($friendemail[$i])){ // after validation send email to this email - id
					$friendarray= array();	
					$friendarray['id']='';
					$friendarray['uid']=$id;
					$friendarray['user_charity_id']=$this->Charityuser->id;
					$friendarray['friend_email']=$friendemail[$i];
					$friendarray['acknowledge']=1;
					$friendarray['charity_id'] = $this->data['Charityuser']['charity_id'];
					$friendarray['datetime']=date('Y-m-d H:i:s');
					$this->Charityuserfriend->save($friendarray);
					$this->userfriendemail($friendemail[$i],$name,base64_encode($this->Charityuserfriend->id));
				}
	    	 }
    	 	
    	 	$this->redirect('/users/myaccount/charity');
    	 }else{
    	 	$this->Session->setFlash('Select proper charity and goal ');
    	 }
      }
}
/*
* ___________________________________________________________________
* Method learnmore 
* Purpose: Display charity learnmore function 
* Parameters : $id
*
* _______________________________________________________________________
*/
function learnmore($id=NULL){
	 // Check user athontication //
	$this->checkuserlogin(); 
    
     if($id!=''){
     $records = $this->Charity->find('all',array('conditions'=>'id='.$id));
     $this->set('data',$records);
     	$this->set('id',$id);
     }else{
     	$recordChar = $this->Charityuser->find('all',array('conditions'=>'uid='.$this->Session->read('User.id').' and status = 1 ','order'=>'rand()','limit'=>1));
     	if(!empty($recordChar) && $recordChar[0]['Charityuser']['charity_id']!=''){
     		$records = $this->Charity->find('all',array('conditions'=>'id='.$recordChar[0]['Charityuser']['charity_id']));
     		$this->set('data',$records);
     	    $this->set('id',$recordChar[0]['Charityuser']['charity_id']);
     	}else{
     	   $this->set('id',$id);	
     	}
     	
     	
     }
     
}
/*___________________________________________________________________________________________________
* 
* Method     : userfriendemail
* Purpose    : making for sending the email to users
* Parameters : None
* 
* ___________________________________________________________________________________________________
*/	   
function userfriendemail($email,$name,$path){
    $this->Email->to = $email;
    $this->Email->bcc = array('nikesh.kumar@meridiansys.com');
    $this->Email->subject = $this->communicationsubject(16);//'New Registration with Smartsed.com';
    $this->Email->from = 'Smartsed Web App <admin@smartsed.com>';
    $this->Email->template = 'friendfamilycharity'; // note no '.ctp'
    $this->set('message', $this->communicationmessage(16));
    $this->set('bottom', $this->communicationbottom(16));
    $this->set('name', $name);
    
    $this->set('activation_url' , "<a href=\""._HTTP_PATH."charities/friendfamilycharity/".$path."\">Charity Invited</a>");

    $this->Email->sendAs = 'both';
    if($this->Email->send()){
        return true;
    }else{
        return false;
    }

}
/*___________________________________________________________________________________________________
* 
* Method     : userfriendemail
* Purpose    : making for sending the email to users
* Parameters : None
* 
* ___________________________________________________________________________________________________
*/	   
function userfriendemailpaymentnotify($id){
    $data = $this->Charityuserfriend->find('all',array('conditions'=>'Charityuserfriend.id='.$id));
    //pr($data);
    $datacharity = $this->Charity->find('all',array('fields'=>'Charity.title','conditions'=>'Charity.id='.$data[0]['Charityuserfriend']['charity_id']));
    //pr($datacharity);
    
	$this->Email->to = $data[0]['Charityuserfriend']['friend_email'];
    $this->Email->bcc = array('nikesh.kumar@meridiansys.com');
    $this->Email->subject = $this->communicationsubject(17);//'New Registration with Smartsed.com';
    $this->Email->from = 'Smartsed Web App <admin@smartsed.com>';
    $this->Email->template = 'userfriendemailpaymentnotify'; // note no '.ctp'
    $this->set('message', $this->communicationmessage(17));
    $this->set('behalfname', $this->UserFirstName($data[0]['Charityuserfriend']['uid']));
    $this->set('amount', $data[0]['Charityuserfriend']['amount']);
    $this->set('yourname', $data[0]['Charityuserfriend']['friend_firstname']);
    $this->set('transectiondt', $data[0]['Charityuserfriend']['transection_date']);
    $this->set('chrity', $datacharity[0]['Charity']['title']);
    
    $this->set('bottom', $this->communicationbottom(17));
    
    $this->Email->sendAs = 'both';
    if($this->Email->send()){
        return true;
    }else{
        return false;
    }
    

}
/*___________________________________________________________________________________________________
* 
* Method     : edivcharityval
* Purpose    : Set New Charity Value
* Parameters : None
* 
* ___________________________________________________________________________________________________
*/
function edivcharityvalue(){
	 	$this->checkadmin();
		$this->Edivcharityvalue->find('all');
		$this->paginate = array(
                         'limit' => 25,
                         'page'=>1,
     				 );
	     $data = $this->paginate('Edivcharityvalue');
         $this->set('data',$data);
}
/*___________________________________________________________________________________________________
* 
* Method     : newedivcharityvalue
* Purpose    : Set New Charity Value
* Parameters : None
* 
* ___________________________________________________________________________________________________
*/
function newedivcharityvalue(){
	$this->checkadmin();
    if(!empty($this->data['Edivcharityvalue']['val'])){
    $this->Edivcharityvalue->set($this->data);
    if($this->Edivcharityvalue->validates()){
    	
			$this->Edivcharityvalue->updateAll(array('Edivcharityvalue.status' => '0'),array('Edivcharityvalue.status'=>'1'));    	
    	    $this->Edivcharityvalue->save($this->data);
             $this->redirect('edivcharityvalue');
       
       }
    }
		
}
/*_____________________________________________________________________________
* Method edivcharityactivedeactive
* Purpose: admin can make deactive the user  
* Parameter : None
* 
* _______________________________________________________________________________
*/
function edivcharityactivedeactive($id=NULL){
        			$this->Edivcharityvalue->updateAll(array('Edivcharityvalue.status' => '0'));
        			$this->Edivcharityvalue->updateAll(array('Edivcharityvalue.status' => '1'),array('Edivcharityvalue.id'=>$id));
				    $this->redirect('edivcharityvalue'); 	
   }
/*___________________________________________________________________________________________________
 * 
* Method     : editcharityvalue
* Purpose    : edit ediv values
* Parameters : None
* 
* ___________________________________________________________________________________________________
*/	 
function editcharityvalue($id=NULL){
	$cronEdiv=array();
	    // Check admin athontication //
	   $this->checkadmin();
	   $this->Edivcharityvalue->id = $id;
       if (empty($this->data)) {
          $this->data = $this->Edivcharityvalue->read();
          $this->set('id',$id);
       }else{
        if ($this->Edivcharityvalue->save($this->data)) {
             
                $this->Session->setFlash('Your post has been updated.');
				$this->redirect(array('action' => 'edivcharityvalue'));
		 }
       	 $this->set('id',$id);
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
function isValidEmail($email){
 if (preg_match("/^[a-zA-Z0-9\.]+@[a-zA-Z0-9\-]+\.[a-zA-Z0-9\.]+$/i" , $email)){ 
  	return true;      
  }else {    
  	return false;
   } 
}   
function charitysuggestion(){
	$this->Charityfeedback->find('all');
	$this->paginate = array(
                         'limit' => 25,
                         'page'=>1,
     				 );
	     $data = $this->paginate('Charityfeedback');
	$this->set('data',$data);
}
//Charityfeedback
function adminreplycharity(){
	$this->layout='';
	 $this->autoRender=false;
	 
            if($this->RequestHandler->isAjax()){
             Configure::write('debug', 0);
             $temp = array();
             $temp['id']='';
             $temp['to_id']=$this->data['Messages']['uid'];
			 $temp['from_id']=0;	
             $temp['subject']=$this->data['Messages']['subject'];
             $temp['message']=$this->data['Messages']['feedback'];
             $temp['datetime']=date('Y-m-d H:i:s');
            
             $this->Inbox->save($temp);
             
             $this->Charityfeedback->updateAll(array('reply'=>'1'),array('id'=>$this->data['Messages']['safeid']));
             
            }
}
function viewdetailcharityemail($id=Null){
	$result = $this->Charityfeedback->find('all',array('conditions'=>'id='.$id));
	$this->set('data',$result);
}
}// END CLASS 

?>