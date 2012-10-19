<?php
/*
#################################################################################
#																				#
#   User Controller 															#
#   file name        	: users_controller.php									#
#   Developed        	: Meridian Radiology 									#
#   Intialised Variables: name , helpers ,components , uses , layout = (default)#
#																				#
#																				#
#################################################################################
*/


class ChallengesController extends AppController {

	var $name = 'Challenges';
        var $uses = array('User','Userprofile','Challengetemp','Challenge','Challengeuserattempt','Quizcat','Quizquestion','Quizanswer','Quizuserattempt','Challengesenderattempt');
        var $components = array('Usercomponent', 'Session','Image','Email','RequestHandler');
        var $helpers = array('Html', 'Form','Javascript','Ajax','Fck');
         var $layout = 'default';

/*___________________________________________________________________________________________________
*
* Method     : challengecollegue
* Purpose    : making for sending the email to users
* Parameters : None
*
* ___________________________________________________________________________________________________
*/
function challengecollegue(){
  $this->checkuserlogin();
  $id = $this->Session->read('User.id');
  $listCat = $this->Quizuserattempt->find('all',array('fields'=>'catid','conditions'=>'uid ='.$id,'group'=>'catid'));
  $this->set('listCat',$listCat);
  $numto = $this->Challenge->find('all',array('conditions'=>'from_id='.$this->Session->read('User.id')));
  $this->set('numto',count($numto));
  $numby = $this->Challenge->find('all',array('conditions'=>'to_id='.$this->Session->read('User.id')));
  $this->set('numby',count($numby));

}
/*___________________________________________________________________________________________________
*
* Method     : challengetocolegue
* Purpose    : making for sending the email to users
* Parameters : None
* ___________________________________________________________________________________________________
*/
function challengetocolegue(){
     $this->autoRender=false;
     if($this->RequestHandler->isAjax()){
        Configure::write('debug', 0);
        $chalengedata = array();
        $user = $this->User->find('all',array('conditions'=>"User.email='".$this->data['Challenge']['email']."'",'fields'=>"Userprofile.first_name,Userprofile.last_name,User.id,User.email"));
        if(!empty($user)){
            // checking for the buffer of the quistion //
            $yourquizbuffer = $this->Quizuserattempt->find('list',array('fields'=>'qid','conditions'=>'uid ='.$this->Session->read("User.id").' and catid ='.$this->data["Challenge"]["catid"].''));
            $challengerquizbuffer = $this->Challengeuserattempt->find('list',array('fields'=>'qid','conditions'=>'uid ='.$user[0]["User"]["id"].' and catid ='.$this->data["Challenge"]["catid"].''));
            $result = array_diff($yourquizbuffer, $challengerquizbuffer);
            if(count($result)<6){
                echo 'Have not sufficient question for this topic';
            }else{
            //SELECT * FROM `challenge_masters` WHERE points IS NULL AND from_id = 2
            // check not played 5 challenges //
            $checkfivechalenge = $this->Challenge->find('all',array('fields'=>'points','conditions'=>'points is NULL and from_id='.$this->Session->read('User.id')));
			if(count($checkfivechalenge)<5){
            $checkpreviouschallenge = $this->Challenge->find('all',array('fields'=>'points','conditions'=>'cat_id='.$this->data['Challenge']['catid'].' and points is NULL and to_id='.$user[0]['User']['id']));
            if(!empty($checkpreviouschallenge)){
                echo 'Givenchallenge';

                }else{
             // Previos number //
            $numto = $this->Challenge->find('all',array('conditions'=>'from_id='.$this->Session->read('User.id')));
            $newnum = count($numto)+1;
            $chalengedata['id']='';
            $chalengedata['from_id'] = $this->Session->read('User.id');
            $chalengedata['to_id']   = $user[0]['User']['id'];
            $chalengedata['cat_id']  = $this->data['Challenge']['catid'];
            $chalengedata['challenged_dt']= date('Y-m-d H:i:s');
            $chalengedata['played_dt'] = '0000-00-00 00:00:00';
            $this->challengeuseremail($user[0]['User']['email'],$this->UserFirstName($this->Session->read('User.id')),base64_encode($user[0]['Userprofile']['id']));
            $this->Challenge->save($chalengedata);
            echo 'Challengetouser+'.$chalengedata['cat_id'].'+'.$chalengedata['to_id'].'+'.$this->Challenge->id;
            }
			}else{
				echo 'You have exceeded the allotted number of challenges. To continue with this challenge, you must cancel  or delete one of your challenges';
			}
           }
          }
          else{

            $sendto = $this->Challengetemp->find('all',array('conditions'=>'to_email="'.$this->data['Challenge']['email'].'" and cat_id='.$this->data['Challenge']['catid'].' and from_id='.$this->Session->read('User.id')));
            if(empty($sendto)){
            $chalengedata['id']='';
            $chalengedata['from_id'] = $this->Session->read('User.id');
            $chalengedata['to_email']   = $this->data['Challenge']['email'];
            $chalengedata['cat_id']  = $this->data['Challenge']['catid'];
            $chalengedata['challenged_dt']= date('Y-m-d H:i:s');
            $chalengedata['played_dt'] = '0000-00-00 00:00:00';
            $this->nonexistchallengeuseremail($this->data['Challenge']['email'],$this->data['Challenge']['name'],base64_encode($user[0]['Userprofile']['id']));
            $this->Challengetemp->save($chalengedata);
              echo 'Nonexistinguser+'.$chalengedata['cat_id'].'+'.urlencode($chalengedata['to_email']).'+'.$this->Challengetemp->id;
            }else{
              echo 'Nonexistingusersendprevious';
            }
            
        }
     }

}
/*___________________________________________________________________________________________________
*
* Method     : checkemail
* Purpose    : making for sending the email to users
* Parameters : None
*
* ___________________________________________________________________________________________________
*/

function  checkemail(){
     $this->autoRender=false;
     if($this->RequestHandler->isAjax()){
        Configure::write('debug', 0);
        $user = $this->User->find('all',array('conditions'=>"User.email='".$_REQUEST['email']."'",'fields'=>"Userprofile.first_name,Userprofile.last_name,User.id"));
        if(!empty($user)){
            echo 'User is a member, you may proceed with your challenge';
        }else{
        	$scr= "'#referalpopup'";
            echo 'User not found on SmartsEd. Invite them to join <a href="javascript:void(0)" onclick=$("#referalpopup").showModal(); return false;>here</a>';
        }
     }
}


/*___________________________________________________________________________________________________
*
* Method     : Emailtouser
* Purpose    : making for sending the email to users
* Parameters : None
*
* ___________________________________________________________________________________________________
*/
function emailtouser(){

   	    $this->autoRender=false;
             if($this->RequestHandler->isAjax()){
                Configure::write('debug', 0);
                $this->checkuserlogin();
		$id = $this->Session->read('User.id');
		$name = $this->UserFirstName($id);
		$email=explode(',' , $_REQUEST['email']);
                    for($i=0;$i<count($email);$i++){
                    	if($this->isValidEmail($email[$i])){

				$this->referuseremail($email[$i],$name,base64_encode($id));
                	}
                  }
                  echo 'Email send to your Colleigue';
             }

}
/*___________________________________________________________________________________________________
*
* Method     : challengeuseremail
* Purpose    : making for sending the email to users
* Parameters : None
*
* ___________________________________________________________________________________________________
*/

function challengeuseremail($email,$fname,$id){
    $this->Email->to = $email;
    $this->Email->bcc = array('nikesh.kumar@meridiansys.com');
    $this->Email->subject = $this->communicationsubject(14);//'New Registration with Smartsed.com';
    $this->Email->from = 'Smartsed Web App <admin@smartsed.com>';
    $this->Email->template = 'challenge'; // note no '.ctp'
    $this->set('message', $this->communicationmessage(14));
    $this->set('bottom', $this->communicationbottom(14));
    $this->set('name', $fname);
    //$aid = md5('a');
    $this->set('activation_url' , "<a href=\""._HTTP_PATH."challenges/challengedby\">Click to view Challenge</a>");

    $this->Email->sendAs = 'both';
    if($this->Email->send()){
        return true;
    }else{
        return false;
    }

}
/*___________________________________________________________________________________________________
*
* Method     : nonexistchallengeuseremail
* Purpose    : making for sending the email to users
* Parameters : None
*
* ___________________________________________________________________________________________________
*/

function nonexistchallengeuseremail($email,$fname,$id){
    $this->Email->to = $email;
    $this->Email->bcc = array('nikesh.kumar@meridiansys.com');
    $this->Email->subject = $this->communicationsubject(15);//'New Registration with Smartsed.com';
    $this->Email->from = 'Smartsed Web App <admin@smartsed.com>';
    $this->Email->template = 'nonexistuserchallenge'; // note no '.ctp'
    $this->set('message', $this->communicationmessage(15));
    $this->set('bottom', $this->communicationbottom(15));
    $this->set('name', $fname);
    $this->set('activation_url' , "<a href=\""._HTTP_PATH."users/register\">Click to register with Smartsed </a>");

    $this->Email->sendAs = 'both';
    if($this->Email->send()){
        return true;
    }else{
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
function isValidEmail($email){
 if (preg_match("/^[a-zA-Z0-9\.]+@[a-zA-Z0-9\-]+\.[a-zA-Z0-9\.]+$/i" , $email)){
  	return true;
  }else {
  	return false;
   }
}
 /*___________________________________________________________________________________________________
*
* Method     : referuseremail
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
    $this->set('activation_url' , "<a href=\""._HTTP_PATH."users/index/".$aid."/".$id."\">"._HTTP_PATH."users/index/".$aid."/".$id."</a>");

    $this->Email->sendAs = 'both';
    if($this->Email->send()){
        return true;
    }else{
        return false;
    }

}

/*___________________________________________________________________________________________________
*
* Method     : challengedto
* Purpose    : making for sending the email to users
* Parameters : None
*
* ___________________________________________________________________________________________________
*/
function challengedto(){
    $this->checkuserlogin();
    //////////////// Chaeck //////////////// New User Challenge /////////////////
	    $email = $this->useremailchallenged($this->Session->read('User.id'));
	    $countchallenge = $this->Challengetemp->find('all',array('conditions'=>'to_email="'.$email.'"'));
	    $newChallengeData = array();
	    if(count($countchallenge)>0){
	        for($i=0;$i<count($countchallenge);$i++){
	            $newChallengeData['id']='';
	            $newChallengeData['from_id']=$countchallenge[$i]['Challengetemp']['from_id'];
	            $newChallengeData['to_id']=$this->Session->read('User.id');
	            $newChallengeData['cat_id']=$countchallenge[$i]['Challengetemp']['cat_id'];;
	            $newChallengeData['challenged_dt']=$countchallenge[$i]['Challengetemp']['challenged_dt'];
	            $newChallengeData['played_dt']='0000-00-00';
	            $newChallengeData['setofquiznum']=$countchallenge[$i]['Challengetemp']['setofquiznum'];
	            $this->Challenge->save($newChallengeData);
	            $this->Challengetemp->delete($countchallenge[$i]['Challengetemp']['id']);// delete the entry 
	
	        }
	     }
    
    /////////////////////////END CHECKING //////////////////////////////////////
    $this->Challenge->find('all',array('conditions'=>'from_id='.$this->Session->read('User.id')));
    $this->paginate = array(
                     'limit' => 10,
                     'page'=>1,
                     'conditions'=>'setofquiznum IS NOT NULL and from_id='.$this->Session->read('User.id')
                     );
    $list = $this->paginate('Challenge');
    $this->set('list',$list);
    // Challenge to nonregister user
   $challengeNonRegisterUser = $this->Challengetemp->find('all',array('conditions'=>'from_id ='.$this->Session->read('User.id')));
   $this->set('challengecount',$challengeNonRegisterUser);
   ///////////// CHallenge By ///////////////////////////
    $email = $this->useremailchallenged($this->Session->read('User.id'));
    $countchallenge = $this->Challengetemp->find('all',array('conditions'=>'to_email="'.$email.'"'));
    $newChallengeData = array();
    if(count($countchallenge)>0){
        for($i=0;$i<count($countchallenge);$i++){
            $newChallengeData['id']='';
            $newChallengeData['from_id']=$countchallenge[$i]['Challengetemp']['from_id'];
            $newChallengeData['to_id']=$this->Session->read('User.id');
            $newChallengeData['cat_id']=$countchallenge[$i]['Challengetemp']['cat_id'];;
            $newChallengeData['challenged_dt']=$countchallenge[$i]['Challengetemp']['challenged_dt'];;
            $newChallengeData['played_dt']='0000-00-00';
            $this->Challenge->save($newChallengeData);
            $this->Challengetemp->delete($countchallenge[$i]['Challengetemp']['id']);// delete the entry 

        }
     }
     
     
    $this->Challenge->find('all',array('conditions'=>'to_id='.$this->Session->read('User.id')));
    $this->paginate = array(
                     'limit' => 10,
                     'page'=>1,
                     'conditions'=>'setofquiznum IS NOT NULL and to_id='.$this->Session->read('User.id')
                     );
    $listto = $this->paginate('Challenge');
    $this->set('listto',$listto);
     

}
function deletethischallenge($id=NULL){
	$result = $this->Challenge->find('all',array('conditions'=>'id='.$id));
	$delset = explode(',',$result[0]['Challenge']['setofquiznum']);
	for($i=0;$i<count($delset);$i++){
		$this->Challengesenderattempt->delete($delset[$i]);
	}
	$this->Challenge->delete($id);
	$this->redirect('challengedto');
}
/*___________________________________________________________________________________________________
*
* Method     : nonregisterchallengedto
* Purpose    : making for sending the email to users
* Parameters : None
*
* ___________________________________________________________________________________________________
*/
function nonregisterchallengedto(){
    $this->checkuserlogin();
    $this->Challengetemp->find('all',array('conditions'=>'from_id='.$this->Session->read('User.id')));
    $this->paginate = array(
                     'limit' => 10,
                     'page'=>1,
                     'conditions'=>'from_id='.$this->Session->read('User.id')
                     );
    $list = $this->paginate('Challengetemp');
    $this->set('list',$list);
   
}
/*___________________________________________________________________________________________________
*
* Method     : challengedto
* Purpose    : making for sending the email to users
* Parameters : None
*
* ___________________________________________________________________________________________________
*/
function challengedby(){
    $this->checkuserlogin();
    
    $email = $this->useremailchallenged($this->Session->read('User.id'));
    $countchallenge = $this->Challengetemp->find('all',array('conditions'=>'to_email="'.$email.'"'));
    $newChallengeData = array();
    if(count($countchallenge)>0){
        for($i=0;$i<count($countchallenge);$i++){
            $newChallengeData['id']='';
            $newChallengeData['from_id']=$countchallenge[$i]['Challengetemp']['from_id'];
            $newChallengeData['to_id']=$this->Session->read('User.id');
            $newChallengeData['cat_id']=$countchallenge[$i]['Challengetemp']['cat_id'];;
            $newChallengeData['challenged_dt']=$countchallenge[$i]['Challengetemp']['challenged_dt'];
            $newChallengeData['played_dt']='0000-00-00';
            $newChallengeData['setofquiznum']=$countchallenge[$i]['Challengetemp']['setofquiznum'];
            $this->Challenge->save($newChallengeData);
            $this->Challengetemp->delete($countchallenge[$i]['Challengetemp']['id']);// delete the entry 

        }
     }



    $this->Challenge->find('all',array('conditions'=>'to_id='.$this->Session->read('User.id')));
    $this->paginate = array(
                     'limit' => 10,
                     'page'=>1,
                     'conditions'=>'to_id='.$this->Session->read('User.id')
                     );
    $list = $this->paginate('Challenge');
    $this->set('list',$list);


}
/*
* ___________________________________________________________________
* Method quizstart
* Purpose: Display Quiz question
* Parameters : None
*
* _______________________________________________________________________
    */
function quizstart($id=NULL,$id2=NULl,$challengerid=NULL,$responcenotice=NULL){

	// Check user athontication //
	$this->checkUserLogin();
	// Set Layout

	// Set category
	$cond = array();

	
	$this->set('data',$records);
	$this->set('url',$id.'/'.$id2);
    $this->set('catSetId',$id);
    $this->set('challengerid',$challengerid);
    $this->set('responcenotice',$responcenotice);
    $resultQuiz = $this->Challenge->find('all',array('fields'=>'setofquiznum','conditions'=>'id = '.$responcenotice));
    $newQuizQuestion = explode(',',$resultQuiz[0]['Challenge']['setofquiznum']);
   
    if(isset($this->data['Challenges']['qnum']) && !empty($this->data['Challenges']['qnum'])){
		$qnum=$this->data['Challenges']['qnum'];
		
	}else{
		$qnum=1;
	
	}
	
	if($qnum==1){
		$QuizToPlay = $this->Challengesenderattempt->find('all',array('fields'=>'qid','conditions'=>'id = '.$newQuizQuestion[0]));
    	$qrecord = $this->Quizquestion->find('all',array('order'=>'rand()','limit'=>1,'fields'=>'question_id,image_name,question_text,cat_id','conditions'=>'question_id ='.$QuizToPlay[0]['Challengesenderattempt']['qid']));
		
		$arecord = $this->Quizanswer->find('all',array('fields'=>'id,answer_text,answer_discription','conditions'=>array('qid'=>$qrecord[0]['Quizquestion']['question_id'])));
		$this->set('qdata',$qrecord[0]['Quizquestion']);
		$this->set('adata',$arecord);
		$this->set('num',$qnum);
		$this->set('qset',$qrecord[0]['Quizquestion']['question_id']);

	}
	elseif($qnum==2){
		
		 $this->Session->write('replay.1.qid' , $this->data['Challenges']['qid']);
		 $this->Session->write('replay.1.cat' , $this->data['Challenges']['cat']);
		 $this->Session->write('replay.1.selectAns' , $this->data['Challenges']['answer']);
		 $QuizToPlay = $this->Challengesenderattempt->find('all',array('fields'=>'qid','conditions'=>'id = '.$newQuizQuestion[1]));
    	$qrecord = $this->Quizquestion->find('all',array('order'=>'rand()','limit'=>1,'fields'=>'question_id,image_name,question_text,cat_id','conditions'=>'question_id ='.$QuizToPlay[0]['Challengesenderattempt']['qid']));
		
		 
		$arecord = $this->Quizanswer->find('all',array('fields'=>'id,answer_text,answer_discription','conditions'=>array('qid'=>$qrecord[0]['Quizquestion']['question_id'])));
		$this->set('qdata',$qrecord[0]['Quizquestion']);
		$this->set('adata',$arecord);
		$this->set('num',$qnum);
		$this->set('qset',$this->data['Challenges']['qid'].','.$qrecord[0]['Quizquestion']['question_id']);

	}
    elseif($qnum==3){
    	 $Qid=explode(",",$this->data['Challenges']['qid']);
    	 $this->Session->write('replay.2.qid' ,$Qid[1]);
		 $this->Session->write('replay.2.cat' , $this->data['Challenges']['cat']);
		 $this->Session->write('replay.2.selectAns' , $this->data['Challenges']['answer']);
		$QuizToPlay = $this->Challengesenderattempt->find('all',array('fields'=>'qid','conditions'=>'id = '.$newQuizQuestion[2]));
    	$qrecord = $this->Quizquestion->find('all',array('order'=>'rand()','limit'=>1,'fields'=>'question_id,image_name,question_text,cat_id','conditions'=>'question_id ='.$QuizToPlay[0]['Challengesenderattempt']['qid']));
		
		 
		$arecord = $this->Quizanswer->find('all',array('fields'=>'id,answer_text,answer_discription','conditions'=>array('qid'=>$qrecord[0]['Quizquestion']['question_id'])));
		$this->set('qdata',$qrecord[0]['Quizquestion']);
		$this->set('adata',$arecord);
		$this->set('num',$qnum);
		$this->set('qset',$this->data['Challenges']['qid'].','.$qrecord[0]['Quizquestion']['question_id']);

	}
    elseif($qnum==4){
    	$Qid=explode(",",$this->data['Challenges']['qid']);
    	$this->Session->write('replay.3.qid' , $Qid[2]);
		 $this->Session->write('replay.3.cat' , $this->data['Challenges']['cat']);
		 $this->Session->write('replay.3.selectAns' , $this->data['Challenges']['answer']);
		$QuizToPlay = $this->Challengesenderattempt->find('all',array('fields'=>'qid','conditions'=>'id = '.$newQuizQuestion[3]));
    	$qrecord = $this->Quizquestion->find('all',array('order'=>'rand()','limit'=>1,'fields'=>'question_id,image_name,question_text,cat_id','conditions'=>'question_id ='.$QuizToPlay[0]['Challengesenderattempt']['qid']));
		
		 
		$arecord = $this->Quizanswer->find('all',array('fields'=>'id,answer_text,answer_discription','conditions'=>array('qid'=>$qrecord[0]['Quizquestion']['question_id'])));
		$this->set('qdata',$qrecord[0]['Quizquestion']);
		$this->set('adata',$arecord);
		$this->set('num',$qnum);
		$this->set('qset',$this->data['Challenges']['qid'].','.$qrecord[0]['Quizquestion']['question_id']);

	}
  elseif($qnum==5){
  	    $Qid=explode(",",$this->data['Challenges']['qid']);
  	    $this->Session->write('replay.4.qid' , $Qid[3]);
		 $this->Session->write('replay.4.cat' , $this->data['Challenges']['cat']);
		 $this->Session->write('replay.4.selectAns' , $this->data['Challenges']['answer']);
		$QuizToPlay = $this->Challengesenderattempt->find('all',array('fields'=>'qid','conditions'=>'id = '.$newQuizQuestion[4]));
    	$qrecord = $this->Quizquestion->find('all',array('order'=>'rand()','limit'=>1,'fields'=>'question_id,image_name,question_text,cat_id','conditions'=>'question_id ='.$QuizToPlay[0]['Challengesenderattempt']['qid']));
		
		 
		 
		$arecord = $this->Quizanswer->find('all',array('fields'=>'id,answer_text,answer_discription','conditions'=>array('qid'=>$qrecord[0]['Quizquestion']['question_id'])));
		$this->set('qdata',$qrecord[0]['Quizquestion']);
		$this->set('adata',$arecord);
		$this->set('num',$qnum);
		$this->set('qset',$this->data['Challenges']['qid'].','.$qrecord[0]['Quizquestion']['question_id']);

	}
elseif($qnum==6){
	    if(isset($this->data['Challenges']['final']) && !empty($this->data['Challenges']['final'])){
	    	$Qid=explode(",",$this->data['Challenges']['qid']);
	    	$this->Session->write('replay.6.qid' , $Qid[5]);
		 	$this->Session->write('replay.6.cat' , $this->data['Challenges']['cat']);
		 	$this->Session->write('replay.6.selectAns' , $this->data['Challenges']['answer']);
		 	$this->set('dataVal',$this->Session->read('replay'));
			$this->set('finalCheck','finished');

	    }else{
	    	$Qid=explode(",",$this->data['Challenges']['qid']);
	    	$this->Session->write('replay.5.qid' , $Qid[4]);
		 	$this->Session->write('replay.5.cat' , $this->data['Challenges']['cat']);
		 	$this->Session->write('replay.5.selectAns' , $this->data['Challenges']['answer']);
		 	$QuizToPlay = $this->Challengesenderattempt->find('all',array('fields'=>'qid','conditions'=>'id = '.$newQuizQuestion[5]));
    	    $qrecord = $this->Quizquestion->find('all',array('order'=>'rand()','limit'=>1,'fields'=>'question_id,image_name,question_text,cat_id','conditions'=>'question_id ='.$QuizToPlay[0]['Challengesenderattempt']['qid']));
		
		 	
		    $arecord = $this->Quizanswer->find('all',array('fields'=>'id,answer_text,answer_discription','conditions'=>array('qid'=>$qrecord[0]['Quizquestion']['question_id'])));
		    $this->set('qdata',$qrecord[0]['Quizquestion']);
		    $this->set('adata',$arecord);
		    $this->set('num',$qnum);
		    $this->set('qset',$this->data['Challenges']['qid'].','.$qrecord[0]['Quizquestion']['question_id']);


	    }



	}


}
/*
* ___________________________________________________________________
* Method reviewquestion
* Purpose: Display Quiz Review Section
* Parameters : None
*
* _______________________________________________________________________
*/
function reviewquestion($id=NULL,$id2=NULL,$challengerid=NULL,$responcenotice=NULL){
	// Check user athontication //
	$this->checkuserlogin();
	// Reading Session Array //
	if($this->Session->read('replay')!=''){
	$qar = $this->Session->read('replay');

	$this->set('dataVal',$qar);
	 $this->set('catSetId',$id);
	// Setting Numbers //
	$qnum=1;
	if(isset($this->data['Challenges']['qnum']) && !empty($this->data['Challenges']['qnum'])){
		$qnum=$this->data['Challenges']['qnum'];
	}else{
		$num=1;

	}
    // cat name //
    $records = $this->Quizcat->find('all',array('fields'=>'category_name,parent_id','conditions'=>array('id IN ('.$id.','.$id2.')')));
	$this->set('data',$records);
	$this->set('rootCat',$this->catname($records[0]['Quizcat']['parent_id']));
	$this->set('url',$id.'/'.$id2);
       $this->set('challengerid',$challengerid);
       $this->set('responcenotice',$responcenotice);


	if($qnum==1){

		$cond=array('cat_id='.$qar['1']['cat'].' and question_id = '.$qar['1']['qid']);
		$qrecord = $this->Quizquestion->find('all',array('limit'=>1,'fields'=>'question_id,image_name,question_text,cat_id','conditions'=>$cond));
		$arecord = $this->Quizanswer->find('all',array('fields'=>'id,answer_text,answer_discription','conditions'=>array('qid'=>$qrecord[0]['Quizquestion']['question_id'])));
		$this->set('qdata',$qrecord[0]['Quizquestion']);
		$this->set('adata',$arecord);
		$this->set('num',$qnum);
		$this->set('qset',$qrecord[0]['Quizquestion']['question_id']);

	}
	if($qnum==2){
		 $this->Session->write('replay.1.qid' , $this->data['Challenges']['qid']);
		 $this->Session->write('replay.1.cat' , $this->data['Challenges']['cat']);
		 $this->Session->write('replay.1.selectAns' , $this->data['Challenges']['answer']);

		$cond=array('cat_id='.$qar['2']['cat'].' and question_id = '.$qar['2']['qid']);
		$qrecord = $this->Quizquestion->find('all',array('limit'=>1,'fields'=>'question_id,image_name,question_text,cat_id','conditions'=>$cond));
		$arecord = $this->Quizanswer->find('all',array('fields'=>'id,answer_text,answer_discription','conditions'=>array('qid'=>$qrecord[0]['Quizquestion']['question_id'])));
		$this->set('qdata',$qrecord[0]['Quizquestion']);
		$this->set('adata',$arecord);
		$this->set('num',$qnum);
		$this->set('qset',$qrecord[0]['Quizquestion']['question_id']);

	}
	if($qnum==3){
		 $this->Session->write('replay.2.qid' , $this->data['Challenges']['qid']);
		 $this->Session->write('replay.2.cat' , $this->data['Challenges']['cat']);
		 $this->Session->write('replay.2.selectAns' , $this->data['Challenges']['answer']);

		 $cond=array('cat_id='.$qar['3']['cat'].' and question_id = '.$qar['3']['qid']);
		$qrecord = $this->Quizquestion->find('all',array('limit'=>1,'fields'=>'question_id,image_name,question_text,cat_id','conditions'=>$cond));
		$arecord = $this->Quizanswer->find('all',array('fields'=>'id,answer_text,answer_discription','conditions'=>array('qid'=>$qrecord[0]['Quizquestion']['question_id'])));
		$this->set('qdata',$qrecord[0]['Quizquestion']);
		$this->set('adata',$arecord);
		$this->set('num',$qnum);
		$this->set('qset',$qrecord[0]['Quizquestion']['question_id']);

	}
	if($qnum==4){
		 $this->Session->write('replay.3.qid' , $this->data['Challenges']['qid']);
		 $this->Session->write('replay.3.cat' , $this->data['Challenges']['cat']);
		 $this->Session->write('replay.3.selectAns' , $this->data['Challenges']['answer']);

		$cond=array('cat_id='.$qar['4']['cat'].' and question_id = '.$qar['4']['qid']);
		$qrecord = $this->Quizquestion->find('all',array('limit'=>1,'fields'=>'question_id,image_name,question_text,cat_id','conditions'=>$cond));
		$arecord = $this->Quizanswer->find('all',array('fields'=>'id,answer_text,answer_discription','conditions'=>array('qid'=>$qrecord[0]['Quizquestion']['question_id'])));
		$this->set('qdata',$qrecord[0]['Quizquestion']);
		$this->set('adata',$arecord);
		$this->set('num',$qnum);
		$this->set('qset',$qrecord[0]['Quizquestion']['question_id']);

	}
	if($qnum==5){
		 $this->Session->write('replay.4.qid' , $this->data['Challenges']['qid']);
		 $this->Session->write('replay.4.cat' , $this->data['Challenges']['cat']);
		 $this->Session->write('replay.4.selectAns' , $this->data['Challenges']['answer']);

		 $cond=array('cat_id='.$qar['5']['cat'].' and question_id = '.$qar['5']['qid']);
		$qrecord = $this->Quizquestion->find('all',array('limit'=>1,'fields'=>'question_id,image_name,question_text,cat_id','conditions'=>$cond));
		$arecord = $this->Quizanswer->find('all',array('fields'=>'id,answer_text,answer_discription','conditions'=>array('qid'=>$qrecord[0]['Quizquestion']['question_id'])));
		$this->set('qdata',$qrecord[0]['Quizquestion']);
		$this->set('adata',$arecord);
		$this->set('num',$qnum);
		$this->set('qset',$qrecord[0]['Quizquestion']['question_id']);

	}
      if($qnum==6){

		if(isset($this->data['Challenges']['final']) && !empty($this->data['Challenges']['final'])){
	    	$this->Session->write('replay.6.qid' , $this->data['Challenges']['qid']);
		 	$this->Session->write('replay.6.cat' , $this->data['Challenges']['cat']);
		 	$this->Session->write('replay.6.selectAns' , $this->data['Challenges']['answer']);
		 	$this->set('dataVal',$this->Session->read('play'));
			$this->set('finalCheck','finished');

		}else{
	     $this->Session->write('replay.5.qid' , $this->data['Challenges']['qid']);
		 $this->Session->write('replay.5.cat' , $this->data['Challenges']['cat']);
		 $this->Session->write('replay.5.selectAns' , $this->data['Challenges']['answer']);

		 $cond=array('cat_id='.$qar['6']['cat'].' and question_id = '.$qar['6']['qid']);
		$qrecord = $this->Quizquestion->find('all',array('limit'=>1,'fields'=>'question_id,image_name,question_text,cat_id','conditions'=>$cond));
		$arecord = $this->Quizanswer->find('all',array('fields'=>'id,answer_text,answer_discription','conditions'=>array('qid'=>$qrecord[0]['Quizquestion']['question_id'])));
		$this->set('qdata',$qrecord[0]['Quizquestion']);
		$this->set('adata',$arecord);
		$this->set('num',$qnum);
		$this->set('qset',$qrecord[0]['Quizquestion']['question_id']);
		}
	}
		$this->set('noval',1);
	}else{
		$this->set('noval',0);
	}

}
/*
* ___________________________________________________________________
* Method quizfinalise
* Purpose: Display Quiz Review Section
* Parameters : None
* Note : This function insert information-quiz in the server and display the result status
*
* _______________________________________________________________________
*/
function quizfinalise($id=NULL,$id2=NULL,$challengerid=NULL,$responcenotice=NULL){
     // Check user athontication //
     $this->checkuserlogin();
     $this->Session->write('challengerid',$challengerid);
     $this->Session->write('responcenotice',$responcenotice);
     $ansDetail= array();
     $url=$id.'-'.$id2 ;
     $userId =$this->Session->read('User.id');
     $insertAr= array();

     if($this->Session->read('replay')!=''){

     $sumpoints =0;
     for($i=1;$i<=count($this->Session->read('replay'));$i++){

     	  $ansDetail= $this->answerdetail($this->Session->read('replay.'.$i.'.selectAns'));
		  $insertAr['id'] = '';
          $insertAr['uid'] = $userId;
          $insertAr['qid'] = $this->Session->read('replay.'.$i.'.qid');
          $insertAr['aid'] = $this->Session->read('replay.'.$i.'.selectAns');
          $insertAr['final_result'] = $ansDetail['answer_result'];
		  $insertAr['points'] = $ansDetail['point'];
		  $sumpoints = $sumpoints + $ansDetail['point'];
                  $insertAr['serial_num'] = $i;
		  $insertAr['played_date']=date('Y-m-d H:i:s');
		  $insertAr['catid']=$this->Session->read('replay.'.$i.'.cat');
		  $insertAr['name']=$this->catname($this->Session->read('replay.'.$i.'.cat'));
                  $insertAr['challenged_by']=$challengerid;
                  $insertAr['challenged_id']=$responcenotice;
                  $this->Challengeuserattempt->save($insertAr);
                  // Tunneling to second session //
                  $this->Session->write('refinalQuiz.'.$i.'.qid' , $this->Session->read('replay.'.$i.'.qid'));
                  $this->Session->write('refinalQuiz.'.$i.'.aid' , $this->Session->read('replay.'.$i.'.selectAns'));
                  $this->Session->write('refinalQuiz.'.$i.'.points' , $ansDetail['point']);
		  $this->Session->write('refinalQuiz.'.$i.'.final_result' , $ansDetail['answer_result']);
		  $this->Session->write('refinalQuiz.'.$i.'.serial_num' , $i);
		  $this->Session->write('refinalQuiz.'.$i.'.name' , $insertAr['name']);

     }

     $this->Challenge->updateAll(array('played_dt' =>'"'.date('Y-m-d H:i:s').'"' , 'points'=>$sumpoints),array('id'=>$responcenotice));
     }

     $this->set('data',$this->Session->read('refinalQuiz'));
     $this->set('url',$url);
     $this->Session->delete('replay');

}
/*
* ___________________________________________________________________
* Method answerdetail
* Purpose: providing the answer detail
* Parameters : None
* Note : function retreive the information
*
* _______________________________________________________________________
*/
function answerdetail($ansId){

	 // Check user athontication //
     $this->checkuserlogin();

      $arDetail = array();

    $data = $this->Quizanswer->find('all',array('fields'=>'answer_text,answer_discription,result','conditions'=>array('id'=>$ansId)));

    if($data[0]['Quizanswer']['result']!= 0){
        $arDetail['answer_discription'] = $data[0]['Quizanswer']['answer_discription'];
        $arDetail['answer_result'] = $data[0]['Quizanswer']['result'];
	    $arDetail['point'] = $this->get_ediv(8);
    }else{
        $arDetail['answer_result'] = $data[0]['Quizanswer']['result'];
        $arDetail['answer_discription'] = '';
        $arDetail['point'] = 0;
    }
    return $arDetail;


}
/*
* ___________________________________________________________________
* Method quizexplaination
* Purpose: providing the answer detail of wrong question
* Parameters : $qid,$aid,$serialnum
* Note : function retreive the information
*
* _______________________________________________________________________
*/
function quizexplaination($qid=NULL,$cid=NULL,$serialnum=NULL,$chk=NULL){
     $this->checkuserlogin();
     $inVal=explode('-',$cid);
     $records = $this->Quizcat->find('all',array('fields'=>'category_name','conditions'=>array('id IN ('.$inVal[0].','.$inVal[1].')')));
    // Retreve the Questions //
    $qrecord = $this->Quizquestion->find('all',array('order'=>'rand()','limit'=>1,'fields'=>'question_id,image_name,question_text,cat_id','conditions'=>'question_id ='.$qid));
    $arecord = $this->Quizanswer->find('all',array('fields'=>'id,answer_text,answer_discription,result','conditions'=>array('qid'=>$qid)));
    $this->set('qdata',$qrecord[0]['Quizquestion']);
    $this->set('adata',$arecord);
    $this->set('num',$serialnum);
    $this->set('data',$records);
    $this->set('invalBack',explode('-',$cid));
    $this->set('bet',$cid);
    $this->set('sesAns',$this->Session->read('refinalQuiz.'.$serialnum.'.aid'));
    if($chk!=''){
    	$this->set('chk',$chk);
    }
}
  /*
* ___________________________________________________________________
* Method viewplayedquiz
* Purpose: Display Quiz Result Section
* Parameters : None
* Note : This function insert information-quiz in the server and display the result status
*
* _______________________________________________________________________
*/
function viewplayedquiz($pd=NULL){
     // Check user athontication //

     $this->checkuserlogin();
     $data = $this->Challengeuserattempt->find('all',array('conditions'=>'challenged_id="'.$pd.'"'));
     $this->set('data',$data);
     
     $this->set('pd',$pd);


}
  /*
* ___________________________________________________________________
* Method viewplayedquizsender
* Purpose: Display Quiz Result Section
* Parameters : None
* Note : This function insert information-quiz in the server and display the result status
*
* _______________________________________________________________________
*/
function viewplayedquizsender($pd=NULL){
     // Check user athontication //

     $this->checkuserlogin();
     $data = $this->Challengesenderattempt->find('all',array('conditions'=>'challenged_id="'.$pd.'"'));
     $this->set('data',$data);
     
     $this->set('pd',$pd);


}
/*
* ___________________________________________________________________
* Method viewplayedquizexplaination
* Purpose: Display Quiz Result Section
* Parameters : None
* Note : This function insert information-quiz in the server and display the result status
*
* _______________________________________________________________________
*/
function viewplayedquizexplaination($qid=NULL,$cid=NULL,$serialnum=NULL,$useraid=NULL,$attemptid=NULL,$pd=NULL,$fresult=NULL){
     // Check user athontication //
     $this->checkuserlogin();
     //$inVal=explode('-',Configure::read('contoller')->_decrypt($cid));
     //$records = $this->Quizcat->find('all',array('fields'=>'category_name','conditions'=>array('id IN ('.$inVal[0].','.$inVal[1].')')));
    $records = $this->Quizcat->find('all',array('fields'=>'category_name','conditions'=>array('id'=>Configure::read('contoller')->_decrypt($cid))));
     // Retreve the Questions //
	    $qrecord = $this->Quizquestion->find('all',array('order'=>'rand()','limit'=>1,'fields'=>'question_id,image_name,question_text,cat_id','conditions'=>'question_id ='.Configure::read('contoller')->_decrypt($qid)));
		$arecord = $this->Quizanswer->find('all',array('fields'=>'id,answer_text,answer_discription,result','conditions'=>array('qid'=>Configure::read('contoller')->_decrypt($qid))));
		$this->set('qdata',$qrecord[0]['Quizquestion']);
		$this->set('adata',$arecord);
               $this->set('num',Configure::read('contoller')->_decrypt($serialnum));
             $this->set('data',$records);
	     $this->set('invalBack',explode('-',Configure::read('contoller')->_decrypt($cid)));
	     $this->set('sesAns',Configure::read('contoller')->_decrypt($useraid));
             $this->set('pd',$pd);
             $this->set('fres',$fresult);
             
 $incorrectData = $this->Challengeuserattempt->find('all',array('conditions'=>array("challenged_id"=>$pd, 'uid'=>$this->Session->read('User.id'), 'id !='=> Configure::read('contoller')->_decrypt($attemptid), 'id >'=> Configure::read('contoller')->_decrypt($attemptid)),'fields'=>array('id','uid','qid','aid','final_result', 'points','challenged_id','catid','serial_num')));
 $this->set('nextIncorrectQ', $incorrectData);

}

/*
* ___________________________________________________________________
* Method viewplayedquizexplaination
* Purpose: Display Quiz Result Section
* Parameters : None
* Note : This function insert information-quiz in the server and display the result status
*
* _______________________________________________________________________
*/
function viewplayedquizexplainationsender($qid=NULL,$cid=NULL,$serialnum=NULL,$useraid=NULL,$attemptid=NULL,$pd=NULL,$fresult=NULL){
     // Check user athontication //
     $this->checkuserlogin();
     //$inVal=explode('-',Configure::read('contoller')->_decrypt($cid));
     //$records = $this->Quizcat->find('all',array('fields'=>'category_name','conditions'=>array('id IN ('.$inVal[0].','.$inVal[1].')')));
    $records = $this->Quizcat->find('all',array('fields'=>'category_name','conditions'=>array('id'=>Configure::read('contoller')->_decrypt($cid))));
     // Retreve the Questions //
	    $qrecord = $this->Quizquestion->find('all',array('order'=>'rand()','limit'=>1,'fields'=>'question_id,image_name,question_text,cat_id','conditions'=>'question_id ='.Configure::read('contoller')->_decrypt($qid)));
		$arecord = $this->Quizanswer->find('all',array('fields'=>'id,answer_text,answer_discription,result','conditions'=>array('qid'=>Configure::read('contoller')->_decrypt($qid))));
		$this->set('qdata',$qrecord[0]['Quizquestion']);
		$this->set('adata',$arecord);
               $this->set('num',Configure::read('contoller')->_decrypt($serialnum));
             $this->set('data',$records);
	     $this->set('invalBack',explode('-',Configure::read('contoller')->_decrypt($cid)));
	     $this->set('sesAns',Configure::read('contoller')->_decrypt($useraid));
             $this->set('pd',$pd);
             $this->set('fres',$fresult);
             
 $incorrectData = $this->Challengesenderattempt->find('all',array('conditions'=>array("challenged_id"=>$pd, 'challeng_sender_id'=>$this->Session->read('User.id'), 'id !='=> Configure::read('contoller')->_decrypt($attemptid), 'id >'=> Configure::read('contoller')->_decrypt($attemptid)),'fields'=>array('id','challeng_sender_id','qid','aid','final_result', 'points','challenged_id','catid','serial_num')));
 pr($incorrectData);
 $this->set('nextIncorrectQ', $incorrectData);

}



function senderquizstart($id=null,$emailid=NULL,$challengedId=NULL){
	$this->checkuserlogin();
	
	
	$cond = array();
	$records = $this->Quizcat->find('all',array('fields'=>'category_name,parent_id','conditions'=>array('id IN ('.$id.')')));
	$this->set('rootCat',$this->catname($records[0]['Quizcat']['parent_id']));
	$this->set('data',$records);
	$this->set('url',$id);

	//$inarr = $this->Challengesenderattempt->find('all',array('fields'=>'qid','conditions'=>array('challeng_sender_id ='.$this->Session->read('User.id').' ')));
	$str='';
	/*
	if(count($inarr)>0){
	
		for($j=0;$j<count($inarr);$j++){

			$str = $str .','.$inarr[$j]['Challengesenderattempt']['qid'];

		}
	}
	*/
	// Checking Count //
   if($str!=''){
   $condCount = array('cat_id IN ('.$id.') and question_id  NOT IN ('.substr($str,1).')');
   }else{
   	$condCount = array('cat_id IN ('.$id.')');
   }
    if(isset($this->data['Challenges']['qnum']) && !empty($this->data['Challenges']['qnum'])){
		$qnum=$this->data['Challenges']['qnum'];
		$cond=array('cat_id IN ('.$id.') and question_id  NOT IN  ('.$this->data['Challenges']['qid'].$str.')');

	}else{
		$qnum=1;
		$this->Session->write('emailorid',urldecode($emailid));
	    $this->Session->write('challengedId',$challengedId);
		if($str!=''){
		$cond=array('cat_id IN ('.$id.') and question_id  NOT IN ('.substr($str,1).')');
		}else{
		$cond=array('cat_id IN ('.$id.')');
		}
	}
   ///////// Load first question //	
	
	
   if($qnum==1){
        $this->Session->write('timestart' , date('Y-m-d H:i:s'));
		$qrecord = $this->Quizquestion->find('all',array('order'=>'rand()','limit'=>1,'fields'=>'question_id,image_name,question_text,cat_id','conditions'=>$cond));
		$arecord = $this->Quizanswer->find('all',array('fields'=>'id,answer_text,answer_discription','conditions'=>array('qid'=>$qrecord[0]['Quizquestion']['question_id'])));
		$this->set('qdata',$qrecord[0]['Quizquestion']);
		$this->set('adata',$arecord);
		$this->set('num',$qnum);
		$this->set('qset',$qrecord[0]['Quizquestion']['question_id']);

	}
elseif($qnum==2){
           
		 $this->Session->write('play.1.qid' , $this->data['Challenges']['qid']);
		 $this->Session->write('play.1.cat' , $this->data['Challenges']['cat']);
		 $this->Session->write('play.1.selectAns' , $this->data['Challenges']['answer']);
		$qrecord = $this->Quizquestion->find('all',array('order'=>'rand()','limit'=>1,'fields'=>'question_id,image_name,question_text,cat_id','conditions'=>$cond));
		$arecord = $this->Quizanswer->find('all',array('fields'=>'id,answer_text,answer_discription','conditions'=>array('qid'=>$qrecord[0]['Quizquestion']['question_id'])));
		$this->set('qdata',$qrecord[0]['Quizquestion']);
		$this->set('adata',$arecord);
		$this->set('num',$qnum);
		$this->set('qset',$this->data['Challenges']['qid'].','.$qrecord[0]['Quizquestion']['question_id']);

	}
    elseif($qnum==3){
    	 $Qid=explode(",",$this->data['Challenges']['qid']);
    	 $this->Session->write('play.2.qid' ,$Qid[1]);
		 $this->Session->write('play.2.cat' , $this->data['Challenges']['cat']);
		 $this->Session->write('play.2.selectAns' , $this->data['Challenges']['answer']);
		$qrecord = $this->Quizquestion->find('all',array('order'=>'rand()','limit'=>1,'fields'=>'question_id,image_name,question_text,cat_id','conditions'=>$cond));
		$arecord = $this->Quizanswer->find('all',array('fields'=>'id,answer_text,answer_discription','conditions'=>array('qid'=>$qrecord[0]['Quizquestion']['question_id'])));
		$this->set('qdata',$qrecord[0]['Quizquestion']);
		$this->set('adata',$arecord);
		$this->set('num',$qnum);
		$this->set('qset',$this->data['Challenges']['qid'].','.$qrecord[0]['Quizquestion']['question_id']);

	}
    elseif($qnum==4){
    	$Qid=explode(",",$this->data['Challenges']['qid']);
    	$this->Session->write('play.3.qid' , $Qid[2]);
		 $this->Session->write('play.3.cat' , $this->data['Challenges']['cat']);
		 $this->Session->write('play.3.selectAns' , $this->data['Challenges']['answer']);
		$qrecord = $this->Quizquestion->find('all',array('order'=>'rand()','limit'=>1,'fields'=>'question_id,image_name,question_text,cat_id','conditions'=>$cond));
		$arecord = $this->Quizanswer->find('all',array('fields'=>'id,answer_text,answer_discription','conditions'=>array('qid'=>$qrecord[0]['Quizquestion']['question_id'])));
		$this->set('qdata',$qrecord[0]['Quizquestion']);
		$this->set('adata',$arecord);
		$this->set('num',$qnum);
		$this->set('qset',$this->data['Challenges']['qid'].','.$qrecord[0]['Quizquestion']['question_id']);

	}
  elseif($qnum==5){
  	    $Qid=explode(",",$this->data['Challenges']['qid']);
  	    $this->Session->write('play.4.qid' , $Qid[3]);
		 $this->Session->write('play.4.cat' , $this->data['Challenges']['cat']);
		 $this->Session->write('play.4.selectAns' , $this->data['Challenges']['answer']);
		$qrecord = $this->Quizquestion->find('all',array('order'=>'rand()','limit'=>1,'fields'=>'question_id,image_name,question_text,cat_id','conditions'=>$cond));
		$arecord = $this->Quizanswer->find('all',array('fields'=>'id,answer_text,answer_discription','conditions'=>array('qid'=>$qrecord[0]['Quizquestion']['question_id'])));
		$this->set('qdata',$qrecord[0]['Quizquestion']);
		$this->set('adata',$arecord);
		$this->set('num',$qnum);
		$this->set('qset',$this->data['Challenges']['qid'].','.$qrecord[0]['Quizquestion']['question_id']);

	}
elseif($qnum==6){
	    if(isset($this->data['Challenges']['final']) && !empty($this->data['Challenges']['final'])){
                $this->Session->write('timeend' , date('Y-m-d H:i:s'));
	    	$Qid=explode(",",$this->data['Challenges']['qid']);
	    	$this->Session->write('play.6.qid' , $Qid[5]);
		 	$this->Session->write('play.6.cat' , $this->data['Challenges']['cat']);
		 	$this->Session->write('play.6.selectAns' , $this->data['Challenges']['answer']);
		 	$this->set('dataVal',$this->Session->read('play'));
			$this->set('finalCheck','finished');

	    }else{
	    	$Qid=explode(",",$this->data['Challenges']['qid']);
	    	 $this->Session->write('play.5.qid' , $Qid[4]);
		 $this->Session->write('play.5.cat' , $this->data['Challenges']['cat']);
		 $this->Session->write('play.5.selectAns' , $this->data['Challenges']['answer']);
		 $qrecord = $this->Quizquestion->find('all',array('order'=>'rand()','limit'=>1,'fields'=>'question_id,image_name,question_text,cat_id','conditions'=>$cond));
		    $arecord = $this->Quizanswer->find('all',array('fields'=>'id,answer_text,answer_discription','conditions'=>array('qid'=>$qrecord[0]['Quizquestion']['question_id'])));
		    $this->set('qdata',$qrecord[0]['Quizquestion']);
		    $this->set('adata',$arecord);
		    $this->set('num',$qnum);
		    $this->set('qset',$this->data['Challenges']['qid'].','.$qrecord[0]['Quizquestion']['question_id']);

	    }
	}
	
	
}
/*
* ___________________________________________________________________
* Method reviewquestion
* Purpose: Display Quiz Review Section
* Parameters : None
*
* _______________________________________________________________________
*/
function reviewquestionsender($id=NULL){
	// Check user athontication //
	$this->checkuserlogin();
	// Reading Session Array //
	if($this->Session->read('play')!=''){
	$qar = $this->Session->read('play');

	$this->set('dataVal',$qar);
	 $this->set('catSetId',$id);
	// Setting Numbers //
	$qnum=1;
	if(isset($this->data['Challenges']['qnum']) && !empty($this->data['Challenges']['qnum'])){
		$qnum=$this->data['Challenges']['qnum'];
	}else{
		$qnum=1;

	}
    // cat name //
    $records = $this->Quizcat->find('all',array('fields'=>'category_name,parent_id','conditions'=>array('id IN ('.$id.')')));
	$this->set('data',$records);
	$this->set('rootCat',$this->catname($records[0]['Quizcat']['parent_id']));
	$this->set('url',$id);
       

	if($qnum==1){

		$cond=array('cat_id='.$qar['1']['cat'].' and question_id = '.$qar['1']['qid']);
		$qrecord = $this->Quizquestion->find('all',array('limit'=>1,'fields'=>'question_id,image_name,question_text,cat_id','conditions'=>$cond));
		$arecord = $this->Quizanswer->find('all',array('fields'=>'id,answer_text,answer_discription','conditions'=>array('qid'=>$qrecord[0]['Quizquestion']['question_id'])));
		$this->set('qdata',$qrecord[0]['Quizquestion']);
		$this->set('adata',$arecord);
		$this->set('num',$qnum);
		$this->set('qset',$qrecord[0]['Quizquestion']['question_id']);

	}
	if($qnum==2){
		 $this->Session->write('play.1.qid' , $this->data['Challenges']['qid']);
		 $this->Session->write('play.1.cat' , $this->data['Challenges']['cat']);
		 $this->Session->write('play.1.selectAns' , $this->data['Challenges']['answer']);

		$cond=array('cat_id='.$qar['2']['cat'].' and question_id = '.$qar['2']['qid']);
		$qrecord = $this->Quizquestion->find('all',array('limit'=>1,'fields'=>'question_id,image_name,question_text,cat_id','conditions'=>$cond));
		$arecord = $this->Quizanswer->find('all',array('fields'=>'id,answer_text,answer_discription','conditions'=>array('qid'=>$qrecord[0]['Quizquestion']['question_id'])));
		$this->set('qdata',$qrecord[0]['Quizquestion']);
		$this->set('adata',$arecord);
		$this->set('num',$qnum);
		$this->set('qset',$qrecord[0]['Quizquestion']['question_id']);

	}
	if($qnum==3){
		 $this->Session->write('play.2.qid' , $this->data['Challenges']['qid']);
		 $this->Session->write('play.2.cat' , $this->data['Challenges']['cat']);
		 $this->Session->write('play.2.selectAns' , $this->data['Challenges']['answer']);

		 $cond=array('cat_id='.$qar['3']['cat'].' and question_id = '.$qar['3']['qid']);
		$qrecord = $this->Quizquestion->find('all',array('limit'=>1,'fields'=>'question_id,image_name,question_text,cat_id','conditions'=>$cond));
		$arecord = $this->Quizanswer->find('all',array('fields'=>'id,answer_text,answer_discription','conditions'=>array('qid'=>$qrecord[0]['Quizquestion']['question_id'])));
		$this->set('qdata',$qrecord[0]['Quizquestion']);
		$this->set('adata',$arecord);
		$this->set('num',$qnum);
		$this->set('qset',$qrecord[0]['Quizquestion']['question_id']);

	}
	if($qnum==4){
		 $this->Session->write('play.3.qid' , $this->data['Challenges']['qid']);
		 $this->Session->write('play.3.cat' , $this->data['Challenges']['cat']);
		 $this->Session->write('play.3.selectAns' , $this->data['Challenges']['answer']);

		$cond=array('cat_id='.$qar['4']['cat'].' and question_id = '.$qar['4']['qid']);
		$qrecord = $this->Quizquestion->find('all',array('limit'=>1,'fields'=>'question_id,image_name,question_text,cat_id','conditions'=>$cond));
		$arecord = $this->Quizanswer->find('all',array('fields'=>'id,answer_text,answer_discription','conditions'=>array('qid'=>$qrecord[0]['Quizquestion']['question_id'])));
		$this->set('qdata',$qrecord[0]['Quizquestion']);
		$this->set('adata',$arecord);
		$this->set('num',$qnum);
		$this->set('qset',$qrecord[0]['Quizquestion']['question_id']);

	}
	if($qnum==5){
		 $this->Session->write('play.4.qid' , $this->data['Challenges']['qid']);
		 $this->Session->write('play.4.cat' , $this->data['Challenges']['cat']);
		 $this->Session->write('play.4.selectAns' , $this->data['Challenges']['answer']);

		 $cond=array('cat_id='.$qar['5']['cat'].' and question_id = '.$qar['5']['qid']);
		$qrecord = $this->Quizquestion->find('all',array('limit'=>1,'fields'=>'question_id,image_name,question_text,cat_id','conditions'=>$cond));
		$arecord = $this->Quizanswer->find('all',array('fields'=>'id,answer_text,answer_discription','conditions'=>array('qid'=>$qrecord[0]['Quizquestion']['question_id'])));
		$this->set('qdata',$qrecord[0]['Quizquestion']);
		$this->set('adata',$arecord);
		$this->set('num',$qnum);
		$this->set('qset',$qrecord[0]['Quizquestion']['question_id']);

	}
      if($qnum==6){

		if(isset($this->data['Challenges']['final']) && !empty($this->data['Challenges']['final'])){
	    	$this->Session->write('play.6.qid' , $this->data['Challenges']['qid']);
		 	$this->Session->write('play.6.cat' , $this->data['Challenges']['cat']);
		 	$this->Session->write('play.6.selectAns' , $this->data['Challenges']['answer']);
		 	$this->set('dataVal',$this->Session->read('play'));
			$this->set('finalCheck','finished');

		}else{
	     $this->Session->write('play.5.qid' , $this->data['Challenges']['qid']);
		 $this->Session->write('play.5.cat' , $this->data['Challenges']['cat']);
		 $this->Session->write('play.5.selectAns' , $this->data['Challenges']['answer']);

		 $cond=array('cat_id='.$qar['6']['cat'].' and question_id = '.$qar['6']['qid']);
		$qrecord = $this->Quizquestion->find('all',array('limit'=>1,'fields'=>'question_id,image_name,question_text,cat_id','conditions'=>$cond));
		$arecord = $this->Quizanswer->find('all',array('fields'=>'id,answer_text,answer_discription','conditions'=>array('qid'=>$qrecord[0]['Quizquestion']['question_id'])));
		$this->set('qdata',$qrecord[0]['Quizquestion']);
		$this->set('adata',$arecord);
		$this->set('num',$qnum);
		$this->set('qset',$qrecord[0]['Quizquestion']['question_id']);
		}
	}
	//echo noval;
		$this->set('noval',1);
	}else{
		$this->set('noval',0);
	}

}
/*
* ___________________________________________________________________
* Method quizfinalisesender
* Purpose: Display Quiz Review Section
* Parameters : None
* Note : This function insert information-quiz in the server and display the result status
*
* _______________________________________________________________________
*/
function quizfinalisesender($id=NULL){
     // Check user athontication //
     $this->checkuserlogin();
     
     $ansDetail= array();
     $url=$id.'-'.$id ;
     $userId =$this->Session->read('User.id');
     $insertAr= array();

     if($this->Session->read('play')!=''){

     $sumpoints =0;
     $tempStr='';
     for($i=1;$i<=count($this->Session->read('play'));$i++){

     	  $ansDetail= $this->answerdetail($this->Session->read('play.'.$i.'.selectAns'));
		  $insertAr['id'] = '';
          $insertAr['challeng_sender_id'] = $userId;
          $insertAr['qid'] = $this->Session->read('play.'.$i.'.qid');
          $insertAr['aid'] = $this->Session->read('play.'.$i.'.selectAns');
          $insertAr['final_result'] = $ansDetail['answer_result'];
		  $insertAr['points'] = $ansDetail['point'];
		  $sumpoints = $sumpoints + $ansDetail['point'];
                  $insertAr['serial_num'] = $i;
		  $insertAr['played_date']=date('Y-m-d H:i:s');
		  $insertAr['catid']=$this->Session->read('play.'.$i.'.cat');
		  $insertAr['name']=$this->catname($this->Session->read('play.'.$i.'.cat'));
                  
                  $insertAr['challenged_id']=$this->Session->read('challengedId');
                  $insertAr['challeng_taker_email']=$this->Session->read('emailorid');
                  $this->Challengesenderattempt->save($insertAr);
                  $tempStr= $tempStr.','.$this->Challengesenderattempt->id;
                  // Tunneling to second session //
                  $this->Session->write('refinalQuizsender.'.$i.'.qid' , $this->Session->read('play.'.$i.'.qid'));
                  $this->Session->write('refinalQuizsender.'.$i.'.aid' , $this->Session->read('play.'.$i.'.selectAns'));
                  $this->Session->write('refinalQuizsender.'.$i.'.points' , $ansDetail['point']);
		  $this->Session->write('refinalQuizsender.'.$i.'.final_result' , $ansDetail['answer_result']);
		  $this->Session->write('refinalQuizsender.'.$i.'.serial_num' , $i);
		  $this->Session->write('refinalQuizsender.'.$i.'.name' , $insertAr['name']);

     }  
       
        if(is_numeric($this->Session->read('emailorid'))){
        	$this->Challenge->updateAll(array('setofquiznum' =>'"'.substr($tempStr,1).'"'),array('id'=>$this->Session->read('challengedId')));
        }else{
        	 $this->Challengetemp->updateAll(array('setofquiznum' =>'"'.substr($tempStr,1).'"'),array('id'=>$this->Session->read('challengedId')));
        }
        
		$this->Session->delete('emailorid');
        $this->Session->delete('challengedId');
       
     }

     $this->set('data',$this->Session->read('refinalQuizsender'));
     $this->set('url',$url);
     $this->Session->delete('play');

}
/*
* ___________________________________________________________________
* Method quizexplaination
* Purpose: providing the answer detail of wrong question
* Parameters : $qid,$aid,$serialnum
* Note : function retreive the information
*
* _______________________________________________________________________
*/
function quizexplainationsender($qid=NULL,$cid=NULL,$serialnum=NULL,$chk=NULL){
     $this->checkuserlogin();
     $inVal=explode('-',$cid);
     $records = $this->Quizcat->find('all',array('fields'=>'category_name','conditions'=>array('id IN ('.$inVal[0].','.$inVal[1].')')));
    // Retreve the Questions //
    $qrecord = $this->Quizquestion->find('all',array('order'=>'rand()','limit'=>1,'fields'=>'question_id,image_name,question_text,cat_id','conditions'=>'question_id ='.$qid));
    $arecord = $this->Quizanswer->find('all',array('fields'=>'id,answer_text,answer_discription,result','conditions'=>array('qid'=>$qid)));
    $this->set('qdata',$qrecord[0]['Quizquestion']);
    $this->set('adata',$arecord);
    $this->set('num',$serialnum);
    $this->set('data',$records);
    $this->set('invalBack',explode('-',$cid));
    $this->set('bet',$cid);
    $this->set('sesAns',$this->Session->read('refinalQuizsender.'.$serialnum.'.aid'));
    if($chk!=''){
    	$this->set('chk',$chk);
    }
}



}
?>
