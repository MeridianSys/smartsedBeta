<?php
class PopupsController extends AppController {

	var $name = 'Popups';
        var $uses = array('User','Userprofile','Language','Country_master','State_master','City_master','Ediv_user_master');
        var $components = array('Usercomponent', 'Session','Image','Email','RequestHandler');
        var $helpers = array('Html', 'Form','Javascript','Ajax');


function viewdetails($id=Null){
    $this->checkuserlogin(); 
    $this->layout='logdefault';
    $userID=$this->Session->Read('User.id');  
    $this->autoRender=false;
          if($this->RequestHandler->isAjax()){
             Configure::write('debug', 0);
                if($_POST!='') {
                    $results=$this->User->find('all',array('conditions'=>array('MD5(User.id)'=>$_POST['v'])));
                    echo '1';
                }
                //print_r($results);
                
          }

}

}

?>
