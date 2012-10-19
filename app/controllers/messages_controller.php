<?php

class MessagesController extends AppController {

	/**
	* The name of this controller. Controller names are plural, named after the model they manipulate.
	*
	* @var string
	* @access public
	*/
	var $name = 'Messages';

	/**
	* these are the model names used in the controller
	* @var array
	* @access public
	**/
	var $uses = array('Message', 'Inbox', 'Sent', 'Trash','User','Group','UserGroup','Charityuser');

	/**
	* these are the component names used in the controller
	* @var array
	* @access public
	**/
	var $helpers = array('Ajax','Session','Fck');
         var $components = array('Session');
        var $layout = 'default';
	//var $helpers = array('Fck');


	function inbox() {
               
		$this->checkUserLogin();
		$user_id = $this->Session->read('User.id');

		$conditions[] = array("Inbox.to_id = '$user_id'");
		$this->paginate = array('limit' => '25', 'order' => array('Inbox.datetime' => 'DESC'));
		$rows = $this->paginate('Inbox', $conditions);
                $this->set('rows', $rows);
               
            // pr($this->data);
		if(!empty($this->data)) {
                         $status = $this->data['Inbox']['status'];
			$ids = $this->data['Inbox']['id'];
                        $count = count($ids);

			switch(trim($status)){
				case "delete":
					for($i=0;$i<$count;$i++){

						$data = $this->Inbox->findById($ids[$i]);

						$arr_trash['Trash']['type'] 			= 'Inbox';
						$arr_trash['Trash']['from_id'] 			= $data['Inbox']['from_id'];
						$arr_trash['Trash']['to_id'] 			= $data['Inbox']['to_id'];
						$arr_trash['Trash']['subject'] 			= $data['Inbox']['subject'];
						$arr_trash['Trash']['message'] 			= $data['Inbox']['message'];
						$arr_trash['Trash']['status'] 		= $data['Inbox']['status'];
						$arr_trash['Trash']['datetime'] 	= $data['Inbox']['datetime'];
						
						$this->Trash->create();
						$this->Trash->save($arr_trash);

						$this->Inbox->delete($ids[$i]);
					}
					$this->Session->setFlash(__d('message','deletemsg',true));
					break;
			}
			$this->redirect('/messages/inbox/');
		}

		
		
	}




	function sent() {

		$this->checkUserLogin();
		$user_id = $this->Session->read('User.id');

		$conditions[] = array("Sent.from_id = '$user_id'");
		$this->paginate = array('limit' => '25', 'order' => array('Sent.datetime' => 'DESC'));
		$rows = $this->paginate('Sent', $conditions);
		$this->set('rows', $rows);

		if(!empty($this->data)) {
                        $status = $this->data['Sent']['status'];
			$ids = $this->data['Sent']['id'];
			$count = count($ids);
                        echo $status;
			switch(trim($status)){
				
				case "delete":
					for($i=0;$i<$count;$i++){

						$data = $this->Sent->findById($ids[$i]);

						$arr_trash['Trash']['type'] 			= 'Sent';
						$arr_trash['Trash']['from_id'] 			= $data['Sent']['from_id'];
						$arr_trash['Trash']['to_id'] 			= $data['Sent']['to_id'];
						$arr_trash['Trash']['subject'] 			= $data['Sent']['subject'];
						$arr_trash['Trash']['message'] 			= $data['Sent']['message'];
						$arr_trash['Trash']['status'] 		= $data['Sent']['status'];
						$arr_trash['Trash']['datetime'] 	= $data['Sent']['datetime'];
						
						$this->Trash->create();
						$this->Trash->save($arr_trash);

						$this->Sent->delete($ids[$i]);
					}
					$this->Session->setFlash(__d('message','deletemsg',true));
					break;
			}
			$this->redirect('/messages/sent/');
		}

		
	}



	function trash() {

		$this->checkUserLogin();
		$user_id = $this->Session->read('User.id');

		$conditions[] = array("Trash.from_id = '$user_id' OR Trash.to_id = '$user_id'");
		$this->paginate = array('limit' => '25', 'order' => array('Trash.datetime' => 'DESC'));
		$rows = $this->paginate('Trash', $conditions);
                /*echo '<pre>';
                print_r($rows);
                echo '</pre>';*/
		$this->set('rows', $rows);

		if(!empty($this->data)) {

			 $status = $this->data['Trash']['status'];
			$ids = $this->data['Trash']['id'];
			$count = count($ids);

			switch(trim($status)) {
				case "delete":
					for($i=0;$i<$count;$i++){
						$this->Trash->delete($ids[$i]);
					}
//					$this->Session->setFlash(__d('message','perdeletemsg',true));
					break;
			}
			$this->redirect('/messages/trash/');
		}
		
	}




	function view($message_id = '', $type='inbox') {

		$row = '';
		$this->checkUserLogin();
		$user_id = $this->Session->read('User.id');
		$type = strtolower($type);

		if(!empty($message_id)) {
			switch($type) {

				case 'inbox':
					$row = $this->Inbox->findById($message_id);
					if((isset($row)) && is_array($row) && (count($row) > 0)) {
						if($row['Inbox']['to_id'] == $user_id) {

							$this->Inbox->id= $message_id;
							$this->Inbox->saveField("status", '1');

							$this->set('row', $row);
							$this->set('second_member_id', $row['Inbox']['from_id']);
							$this->set('model', 'Inbox');
						}
					}
				break;

				case 'sent':
					$row = $this->Sent->findById($message_id);
					if((isset($row)) && is_array($row) && (count($row) > 0)) {
						if($row['Sent']['from_id'] == $user_id) {

							$this->Sent->id= $message_id;
							$this->Sent->saveField("status", '1');

							$this->set('row', $row);
							$this->set('second_member_id', $row['Sent']['to_id']);
							$this->set('model', 'Sent');
						}
					}
				break;

				case 'trash':
					$row = $this->Trash->findById($message_id);
					if((isset($row)) && is_array($row) && (count($row) > 0)) {
						if( ($row['Trash']['type'] == 'Inbox') && ($row['Trash']['to_id'] == $user_id) ) {

							$this->Trash->id= $message_id;
							$this->Trash->saveField("status", '1');

							$this->set('row', $row);
							$this->set('second_member_id', $row['Trash']['from_id']);
						}
						if( ($row['Trash']['type'] == 'Sent') && ($row['Trash']['from_id'] == $user_id) ) {

							$this->Trash->id= $message_id;
							$this->Trash->saveField("status", '1');

							$this->set('row', $row);
							$this->set('second_member_id', $row['Trash']['to_id']);
						}
						$this->set('model', 'Trash');
					}
				break;

				default:
				break;
			}
		}

		$this->set('message_id', $message_id);
		$this->set('type', $type);
	}

    function dataset($q) {
        
        $this->autoRender=false;
            if($this->RequestHandler->isAjax()){
             Configure::write('debug', 0);
                $userInfo = $this->User->find('all', array(
                                            'conditions' => array('Userprofile.first_name LIKE' => "%$q%"),
                                            'fields' => array('Userprofile.first_name','Userprofile.last_name', 'User.email','User.id'),
                                            'order' => 'Userprofile.first_name DESC',
                                        ));
           
            foreach($userInfo as $user) {
                $json['value']=$user['User']['id'];
                $json['name']=$user['Userprofile']['first_name'].' '.$user['Userprofile']['last_name'];
               // $json['email']=$user['User']['email'];
                $data[]=$json;
            }

            header("Content-type: application/json");
            echo json_encode($data);
          }
    }

	function compose() {
                $this->checkUserLogin();
		$user_id = $this->Session->read('User.id');
		//$user_info = $this->get_user_info($user_id, '', true);

                $GroupList = $this->Group->find('all',array('conditions'=>array('uid'=>$user_id)));
                $this->set('groupList',$GroupList);
		if(!empty($this->data)) {

			if($this->Message->create($this->data) && $this->Message->validates()) {

				$to 			= explode(',', substr($this->data['Message']['to'],0,-1));
				$subject 		= $this->data['Message']['subject'];
				$message 		= $this->data['Message']['message'];

                            if($this->data['Message']['rad']=='group') {
                                for($gr=0; $gr<count($this->data['Message']['groupid']); $gr++) {
                                    if($this->data['Message']['groupid'][$gr]=='cha'){
                                     //FINDING CHARITY PARTNERS - STARTS
                                     $charityID = $this->Charityuser->find('all',array('conditions'=>'uid ='.$user_id));
                                     if(!empty($charityID)) {
                                     $charityGroupUser = $this->Charityuser->find('all',array('conditions'=>"uid !=".$user_id . " AND charity_id =".$charityID[0]['Charityuser']['charity_id']));
                                     for($cu=0; $cu<=count($charityGroupUser); $cu++) {
                                        if($charityGroupUser[$cu]['Charityuser']['uid']!=''){
                                            $sent['Sent']['from_id'] = $user_id;
                                            $sent['Sent']['to_id'] 	= $charityGroupUser[$cu]['Charityuser']['uid'];
                                            $sent['Sent']['subject'] = $subject;
                                            $sent['Sent']['message'] = $message;
                                            $sent['Sent']['status'] = 0;
                                            $sent['Sent']['datetime'] = date('Y-m-d H:i:s');
                                            $this->Sent->create();
                                            $this->Sent->save($sent);

                                            $inbox['Inbox']['from_id'] = $user_id;
                                            $inbox['Inbox']['to_id'] = $charityGroupUser[$cu]['Charityuser']['uid'];
                                            $inbox['Inbox']['subject']= $subject;
                                            $inbox['Inbox']['message']= $message;
                                            $inbox['Inbox']['status']= 0;
                                            $inbox['Inbox']['datetime']= date('Y-m-d H:i:s');
                                            $this->Inbox->create();
                                            $this->Inbox->save($inbox);
                                        }
                                     }
                                    }

                                    }
                                    if($this->data['Message']['groupid'][$gr]=='ref'){
                                    $referalGroupUser = $this->User->find('all',array('conditions'=>array('Userprofile.referrals ='=>$user_id),'fields'=>array('Userprofile.user_id') ));
                                    for($ru=0; $ru<=count($referalGroupUser); $ru++) {
                                        if($referalGroupUser[$ru]['Userprofile']['user_id']!=''){
                                            $sent['Sent']['from_id'] = $user_id;
                                            $sent['Sent']['to_id']  = $referalGroupUser[$ru]['Userprofile']['user_id'];
                                            $sent['Sent']['subject'] = $subject;
                                            $sent['Sent']['message'] = $message;
                                            $sent['Sent']['status'] = 0;
                                            $sent['Sent']['datetime'] = date('Y-m-d H:i:s');
                                            $this->Sent->create();
                                            $this->Sent->save($sent);

                                            $inbox['Inbox']['from_id'] = $user_id;
                                            $inbox['Inbox']['to_id'] = $referalGroupUser[$ru]['Userprofile']['user_id'];
                                            $inbox['Inbox']['subject']= $subject;
                                            $inbox['Inbox']['message']= $message;
                                            $inbox['Inbox']['status']= 0;
                                            $inbox['Inbox']['datetime']= date('Y-m-d H:i:s');
                                            $this->Inbox->create();
                                            $this->Inbox->save($inbox);
                                        }
                                     }

                                    }
//echo "one<br>";
//echo "<pre>";print_r($this->data['Message']['groupid'][$gr]);
if($this->data['Message']['groupid'][$gr]!='ref' && $this->data['Message']['groupid'][$gr]!='cha'){
$userGroup=$this->data['Message']['groupid'][$gr];
                                    $groupUser[] = $this->UserGroup->find('all',
                                            array('conditions'=>array('UserGroup.group_id'=>$userGroup),
                                                'fields'=>array('UserGroup.user_gid'),
                                            ));
//  echo "two<br>";
                                for($u=0; $u<=count($groupUser); $u++) {//echo "three<br>";
                                    //echo "===".$groupUser[0][$u]['UserGroup']['user_gid'];
                                    if($groupUser[0][$u]['UserGroup']['user_gid']!=''){//echo "four<br>";
                                        $sent['Sent']['from_id'] = $user_id;
					$sent['Sent']['to_id'] 	= $groupUser[0][$u]['UserGroup']['user_gid'];
					$sent['Sent']['subject'] = $subject;
					$sent['Sent']['message'] = $message;
					$sent['Sent']['status'] = 0;
                                        $sent['Sent']['datetime'] = date('Y-m-d H:i:s');
					$this->Sent->create();
					$this->Sent->save($sent);

                                        $inbox['Inbox']['from_id'] = $user_id;
					$inbox['Inbox']['to_id'] = $groupUser[0][$u]['UserGroup']['user_gid'];
					$inbox['Inbox']['subject']= $subject;
					$inbox['Inbox']['message']= $message;
					$inbox['Inbox']['status']= 0;
                                        $inbox['Inbox']['datetime']= date('Y-m-d H:i:s');
					$this->Inbox->create();
					$this->Inbox->save($inbox);
                                    }
                                
                                }
}
                                }
                                 $this->Session->setFlash(__d('message','composemsg',true));
				$this->redirect('/messages/compose/');

                            } elseif($this->data['Message']['rad']=='user') {
                                foreach($to as $recepients) {
                                 	$recepient_info = $this->User->findById(trim($recepients));
	
					$sent['Sent']['from_id'] = $user_id;
					$sent['Sent']['to_id'] 	= $recepient_info['User']['id'];
					$sent['Sent']['subject'] = $subject;
					$sent['Sent']['message'] = $message;
					$sent['Sent']['status'] = 0;
                                        $sent['Sent']['datetime'] = date('Y-m-d H:i:s');
					$this->Sent->create();
					$this->Sent->save($sent);

					$inbox['Inbox']['from_id'] = $user_id;
					$inbox['Inbox']['to_id'] = $recepient_info['User']['id'];
					$inbox['Inbox']['subject']= $subject;
					$inbox['Inbox']['message']= $message;
					$inbox['Inbox']['status']= 0;
                                        $inbox['Inbox']['datetime']= date('Y-m-d H:i:s');
					$this->Inbox->create();
					$this->Inbox->save($inbox);
				}
                                $this->Session->setFlash(__d('message','composemsg',true));
				$this->redirect('/messages/compose/');
                            }

				
			}
			else {
				$this->Message->invalidFields();
			}
		}
	}




	function reply($message_id, $type="inbox") {

		$row = '';
		$from_user_id = '';
		$this->checkUserLogin();
		$user_id = $this->Session->read('User.id');
		$user_info = $this->get_user_info($user_id, 'Userprofile.first_name,Userprofile.last_name,User.id', false);
		$type = strtolower($type);

		if(!empty($message_id)) {

			switch($type) {

				case 'inbox':
					$row = $this->Inbox->findById($message_id);
					if((isset($row)) && is_array($row) && (count($row) > 0)) {
                                            if($row['Inbox']['to_id'] == $user_id) {

                                                $this->set('row', $row);
                                                $from_user_id = $row['Inbox']['from_id'];
                                                $from_info = $this->get_user_info($from_user_id, 'Userprofile.first_name,Userprofile.last_name,User.id', false);
                                               // pr($from_info);
                                                $from_user_name = $from_info['Userprofile']['first_name'].' '.$from_info['Userprofile']['last_name'];
                                                $this->set('from_user_name', $from_user_name);
                                                $this->set('message', $row['Inbox']['message']);
                                        }
                                    }
				break;

				default:
				break;
			}

			if( (!empty($this->data)) && (!empty($from_user_id)) ) {

				if($this->Message->create($this->data) && $this->Message->validates()) {

					$subject = $this->data['Message']['subject'];
					$message = $this->data['Message']['message'];

					{
						
						$sent['Sent']['from_id'] = $user_id;
						$sent['Sent']['to_id']= $from_info['User']['id'];
						$sent['Sent']['subject']= $subject;
						$sent['Sent']['message'] = $message;
						$sent['Sent']['status'] = 0;
                                                $sent['Sent']['datetime']= date('Y-m-d H:i:s');
						$this->Sent->create();
						$this->Sent->save($sent);


						$inbox['Inbox']['from_id'] = $user_id;
						$inbox['Inbox']['to_id'] 	= $from_info['User']['id'];
						$inbox['Inbox']['subject'] = $subject;
						$inbox['Inbox']['message'] = $message;
						$inbox['Inbox']['status'] = 0;
                                                $inbox['Inbox']['datetime']= date('Y-m-d H:i:s');
						$this->Inbox->create();
						$this->Inbox->save($inbox);
					}

					$this->Session->setFlash(__d('message','replymsg',true).' '.$from_user_name);
					$this->redirect('/messages/sent/');
				}
				else {
					$this->Message->invalidFields();
				}
			}
			else {
				$this->data['Message']['subject'] = "RE: ".$row['Inbox']['subject'];
                                $message_reply = "";
                                $message_reply .= "";
				$message_reply .= "<br/><br/>----- Original Message -----";
				$message_reply .= "<br/>From: ".$from_info['Userprofile']['first_name'].' '.$from_info['Userprofile']['last_name'];
				$message_reply .= "<br/>Sent: ".date('l dS M Y, h:i:s A');
				$message_reply .= "<br/>".nl2br($row['Inbox']['message']);
				$this->data['Message']['message'] = $message_reply;
                                //$this->set('msg',$message_reply);
			}
		}

		$this->set('message_id', $message_id);
		$this->set('type', $type);
	}




	function report_spam($message_id, $type = 'inbox') {

		$row = '';
		$this->checkMemberLogin();
		$member_id = $this->Session->read('Member.id');
		$type = strtolower($type);

		if(!empty($message_id)) {
			switch($type) {

				case 'inbox':
					$row = $this->Inbox->findById($message_id);
					if((isset($row)) && is_array($row) && (count($row) > 0)) {
						if($row['Inbox']['to'] == $member_id) {
							$this->set('row', $row);
							$this->set('second_member_id', $row['Inbox']['from']);
							$this->set('model', 'Inbox');
						}
					}
				break;

				default:
				break;
			}
		}

		if(!empty($this->data)) {

			// pr($this->data);
			if($this->Message->create($this->data) && $this->Message->validates()) {

					$this->Inbox->id = $message_id;
					$this->Inbox->saveField("spam", '1');
					$this->Inbox->saveField("spam_reason", $this->data['Message']['spam_reason']);

					$this->Session->setFlash(__d('message','requestmsg',true));

                                        $this->redirect('/messages/inbox/');
			}
		}

		$this->set('message_id', $message_id);
		$this->set('type', $type);
	}







	function backend_index() {

		$conditions[] = array("Inbox.spam = '1'");
		$this->paginate = array('limit' => '25', 'order' => array('Inbox.created' => 'DESC'));
		$rows = $this->paginate('Inbox', $conditions);

		$this->set('rows', $rows);

		$this->set('limit', $this->params['paging']['Inbox']['options']['limit']);
		$this->set('page', $this->params['paging']['Inbox']['options']['page']);
		// pr($rows);
	}



	function backend_spam_details($message_id) {

		if(!empty($message_id)) {

			$message_info = $this->Inbox->findById($message_id);
			$this->set('message_info', $message_info);
		}
	}




	function backend_delete_spam($message_id = '') {

		if(!empty($message_id)) {

			$row = $this->Inbox->find(array('Inbox.id' => $message_id), array('Inbox.id'));

			if((isset($row)) && is_array($row) && (count($row) > 0)) {

				$this->Inbox->delete($message_id);

				$this->Session->setFlash(__d('message','deletemsg',true));
				$this->redirect('/backend/messages/index/');
			}
		}
	}



} // Class ends