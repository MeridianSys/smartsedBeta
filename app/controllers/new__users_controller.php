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


class UsersController extends AppController {

	var $name = 'Users';
        var $uses = array('Challengetemp','Featureuserattempt','User','Userprofile','Testimonial','Poll','Pollanswer','Staticcontent','Polluserattempt','Language','Country_master','State_master','City_master','Ediv_user_master','user_winning_points','language_averages','Quizuserattempt','Feature','Quizcat','Charity','Charityuser','Charityuserfriend','Edivcharityvalue','Cafeseriescontent','Cafeseriesquestion','Cafeseriesanswer','Staticloungevalue','Sponsern','Userpayment');
        var $components = array('Usercomponent', 'Session','Image','Email','RequestHandler','AuthorizeNet');
        var $helpers = array('Html', 'Form','Javascript','Ajax','Fck');


function index($code=NULL,$indexkey=NULL){
   $userID     =$this->Session->read('User.id');
   $userEmail   =$this->Session->read('User.email');

   if($indexkey!='') {
       $this->set('cite',$indexkey);
   } else {
        if($code!='') {
            if($code == 3) {
                $this->Session->setFlash('Invalid Email or Password.');
            }elseif($code == 2) {
                $this->Session->setFlash('Please activate your account, check email.');
            }elseif($code == 5) {
                $this->Session->setFlash('You have already login.');
            } elseif($code!='' && $code!=3 && $code!=2 && $code!=5) {
                $matchKey = $this->User->find(array('User.activationkey' => $code), array('User.id'));
                if(!empty($matchKey)) {
                    $this->set('key',$code);
                } else {
                    $this->redirect('/users/index');
                }
            } else {
                if(!empty($userID) && !empty($userEmail)){
                       $dashboardStatus = $this->get_user_info($this->Session->read('User.id'), 'Userprofile.edivactivity_reminder',false);
                      if($dashboardStatus['Userprofile']['edivactivity_reminder']=='P' || $dashboardStatus['Userprofile']['edivactivity_reminder']=='S' || $dashboardStatus['Userprofile']['edivactivity_reminder']=='N' || $dashboardStatus['Userprofile']['edivactivity_reminder']=='') {
                              $acordionstatus=4;
                        } elseif($dashboardStatus['Userprofile']['edivactivity_reminder']=='Performance') {
                           $acordionstatus=3;
                        } elseif($dashboardStatus['Userprofile']['edivactivity_reminder']=='Lounge') {
                            $acordionstatus=1;
                        }elseif($dashboardStatus['Userprofile']['edivactivity_reminder']=='eDiv') {
                            $acordionstatus=2;
                        } elseif($dashboardStatus['Userprofile']['edivactivity_reminder']=='My Courses') {
                            $acordionstatus=4;
                        }
                    $this->redirect('/users/dashboard/'.$acordionstatus);
                 }
            }
        }else{
           if(!empty($userID) && !empty($userEmail)){
               $dashboardStatus = $this->get_user_info($this->Session->read('User.id'), 'Userprofile.edivactivity_reminder',false);
               if($dashboardStatus['Userprofile']['edivactivity_reminder']=='P' || $dashboardStatus['Userprofile']['edivactivity_reminder']=='S' || $dashboardStatus['Userprofile']['edivactivity_reminder']=='N' || $dashboardStatus['Userprofile']['edivactivity_reminder']=='') {
                      $acordionstatus=4;
                } elseif($dashboardStatus['Userprofile']['edivactivity_reminder']=='Performance') {
                   $acordionstatus=3;
                } elseif($dashboardStatus['Userprofile']['edivactivity_reminder']=='Lounge') {
                    $acordionstatus=1;
                }elseif($dashboardStatus['Userprofile']['edivactivity_reminder']=='eDiv') {
                    $acordionstatus=2;
                } elseif($dashboardStatus['Userprofile']['edivactivity_reminder']=='My Courses') {
                    $acordionstatus=4;
                }
                $this->redirect('/users/dashboard/'.$acordionstatus);
           }
        }
   }
}



/*
*___________________________________________________________
*
* Method     : register
* Purpose    : making new user registration
* Parameters : None
*
*_____________________________________________________________
*/

function register(){

    if(!empty($this->data)) {
        if(isset($this->data['Userprofile']['final']) && $this->data['Userprofile']['final']=='final') {
             $this->data['User']['activationkey'] = $this->Usercomponent->generatekey();
           if($this->data['Userprofile']['state']=='') {
               $this->data['Userprofile']['state']=0;
           }
           if($this->data['Userprofile']['city']=='') {
               $this->data['Userprofile']['city']=0;
           }
           if($this->data['Userprofile']['adduce']!='') {
               $this->data['Userprofile']['referrals']=base64_decode($this->data['Userprofile']['adduce']);
               $this->set('referral',$this->data['Userprofile']['adduce']);
           }
          $this->data['User']['block'] = 1;
           $insertid = $this->autoincrementid('User');
          if($this->User->saveAll($this->data,array('validate'=>'first'))) {

              $this->data['Ediv_user_master']['uid']=$insertid;
              $this->data['Ediv_user_master']['ediv_type']=6;
              $this->data['Ediv_user_master']['points']=$this->pointforediv(6);
              $this->Ediv_user_master->save($this->data);
        /******************* REferral points update if user comes to register from refid - starts **************************************/
              if($this->data['Userprofile']['referrals']!='') {
                  $this->Ediv_user_master->create();
                  $this->data['Ediv_user_master']['uid']=$this->data['Userprofile']['referrals'];
                  $this->data['Ediv_user_master']['ediv_type']=1;
                  $this->data['Ediv_user_master']['points']=$this->pointforediv(1);
                  $this->data['Ediv_user_master']['datetime']= date('Y-m-d H:i:s');
                  $this->Ediv_user_master->save($this->data);
              }
       /******************* REferral points update if user comes to register from refid - ends **************************************/
              // if($this->newaccountemail($this->data['User']['email'], $this->data['User']['activationkey'], $this->data['Userprofile']['first_name'], $this->data['User']['password'])); {
					/*
                    $user = $this->User->find(array("User.id=".$insertid), array('User.id','User.email','User.block','User.password','User.activationkey','Userprofile.first_name','Userprofile.last_name,Userprofile.title','Userprofile.edivactivity_reminder', 'User.login_status'));

                    $this->Session->write('User.id' , $user['User']['id']);
                    $this->Session->write('User.name' , $user['Userprofile']['first_name'].' '.$user['Userprofile']['last_name']);
                    $this->Session->write('User.email' , $user['User']['email']);
                    $this->Session->write('User.userfname' , $user['Userprofile']['first_name']);
                    $this->Session->write('User.timeout',time());
                     $this->Session->write('User.title' , $user['Userprofile']['title']);
                    */
                    /* UPDATE USER LOGIN STATUS - STARTS */
                     
                    
                    $this->redirect(array('controller'=>'users','action'=>'processpayment/'.md5($insertid)));
                    exit;

               }
          } else {
              	$this->User->invalidFields();
	  }
        }
    //}
    if($this->data['Userprofile']['country']=='') {
        $this->data['Userprofile']['country'] = '40';
    }

$languages = $this->Language->find('list',array('conditions' => null,
                                                'order' => 'Language.name ASC',
                                                'limit' => null,
                                                'fields' => 'Language.name', 'Language.id'));
$this->set('languageList',$languages);
$countries = $this->Country_master->find('list',array('conditions' => array('Country_master.status =' => '1'),
                                                'order' => 'Country_master.country_name ASC',
                                                'limit' => null,
                                                'fields' => 'Country_master.country_name', 'Country_master.id'));
$this->set('countryList',$countries);

if($this->data['Userprofile']['country'] == '40') {
    $stateList = $this->State_master->find('list',array('conditions' => array('State_master.status =' => '1 AND State_master.country_id=40'),
                                                'order' => 'State_master.state_name ASC',
                                                'limit' => null,
                                                'fields' => 'State_master.state_name', 'State_master.id'));
    $this->set('stateList',$stateList);
}

if($this->data['Userprofile']['country'] == '40' && $this->data['Userprofile']['state'] != '') {
    $cityList = $this->City_master->find('list',array('conditions' => array('City_master.status =' => '1 AND City_master.state_id='.$this->data['Userprofile']['state']),
                                                'order' => 'City_master.city_name ASC',
                                                'limit' => null,
                                                'fields' => 'City_master.city_name', 'City_master.id'));
    $this->set('cityList',$cityList);
}


if($this->data['Userprofile']['accredit']!=''){
    $this->set('referral',$this->data['Userprofile']['accredit']);
  }

}

function stateupdate() {
    if($this->RequestHandler->isAjax()){
    Configure::write('debug', 0);
    $stateList = $this->State_master->find('list',array('conditions' => array('State_master.status' => '1', 'State_master.country_id'=>$_POST['id']),
                                                'order' => 'State_master.state_name ASC',
                                                'limit' => null,
                                                'fields' => 'State_master.state_name', 'State_master.id'));
    $this->set('stateList',$stateList);
    }
}

function stateupdatepro() {
    if($this->RequestHandler->isAjax()){
    Configure::write('debug', 0);
    $stateList = $this->State_master->find('list',array('conditions' => array('State_master.status' => '1', 'State_master.country_id'=>$_POST['id']),
                                                'order' => 'State_master.state_name ASC',
                                                'limit' => null,
                                                'fields' => 'State_master.state_name', 'State_master.id'));
    $this->set('stateList',$stateList);
    }
}

function cityupdate() {
    if($this->RequestHandler->isAjax()){
    Configure::write('debug', 0);
    $cityList = $this->City_master->find('list',array('conditions' => array('City_master.status' => '1',  'City_master.state_id'=>$_POST['id']),
                                                    'order' => 'City_master.city_name ASC',
                                                    'limit' => null,
                                                    'fields' => 'City_master.city_name', 'City_master.id'));
    $this->set('cityList',$cityList);
    }
}

function thanks(){
}

function activateuser($key) {
 $arrUser = array();
//check activation code
 $arrUser = $this->User->find(array('User.activationkey' => $key), array('User.id,User.email,User.block,Userprofile.first_name,Userprofile.last_name,Userprofile.title'));
    if($arrUser['User']['block']==0){
        $user_id = $arrUser['User']['id'];
        //update account to activate the account
         $this->data['User']['id'] = $user_id;
         $this->data['User']['block'] = 1;
            if($this->User->save($this->data)){
            } else {
               $this->Session->setFlash('Some error has been occured. Please try again later.');
             }
    }else{
        $this->redirect(array('controller'=>'users','action'=>'dashboard'));
    }
}

function userlogin() {
    $this->autoRender=false;
          if($this->RequestHandler->isAjax()){
             Configure::write('debug', 0);
                 if(!empty($this->data)){
                     $user = $this->User->find(array("(User.email='".trim($this->data['User']['logemail'])."' AND User.password = '".trim($this->data['User']['logpassword'])."')"), array('User.id','User.email','User.block','User.password','User.activationkey','Userprofile.first_name','Userprofile.last_name,Userprofile.title','Userprofile.edivactivity_reminder', 'User.login_status','Userprofile.payment_status'));
          		if(is_array($user) && count($user) > 0){
			      //check for account approval status
                            if(($user['User']['block'] != '1')) {
                                $Loginerror = "Your account is not yet activated.<br/>please activate you account by clicking on the verification link provided in your email.<br />";
                                $this->set('Loginerror',$Loginerror);
                                $ret = 2;
                                echo $ret;
                            } else {
                                if($user['User']['login_status']==0) {
                                	$chkpayment = $this->get_user_info($user['User']['id'], 'Userprofile.payment_status',false);
                                    if($chkpayment['Userprofile']['payment_status']=='1'){
                                    $this->Session->write('User.id' , $user['User']['id']);
                                    $this->Session->write('User.name' , $user['Userprofile']['first_name'].' '.$user['Userprofile']['last_name']);
                                    $this->Session->write('User.email' , $user['User']['email']);
                                    $this->Session->write('User.userfname' , $user['Userprofile']['first_name']);
                                    $this->Session->write('User.timeout',time());
                                     $this->Session->write('User.title' , $user['Userprofile']['title']);
                                    /* UPDATE USER LOGIN STATUS - STARTS */
                                       $curdatetime = date('Y-m-d H:i:s');
                                    $this->User->updateAll(array('User.login_status' => 1, 'User.lastvisit_date'=>"'$curdatetime'"),array('User.id'=>$user['User']['id']));
                                    /* UPDATE USER LOGIN STATUS - ENDS */

                                    $remember = $this->data['User']['remember'];
                                    if(intval($remember) == 1) {
                                            setcookie("cookemail", $user['User']['email'], time()+60*60*24*100, '/');
                                            setcookie("cookpass", $user['User']['password'], time()+60*60*24*100, '/');
                                            setcookie("cookrem", $remember, time()+60*60*24*100, '/');
                                    }
                                    else {
                                            setcookie("cookemail", $user['User']['email'], time()-60*60*24*100, '/');
                                            setcookie("cookpass", $user['User']['password'], time()-60*60*24*100, '/');
                                            setcookie("cookrem", $remember, time()-60*60*24*100, '/');
                                    }
                                    if($this->data['User']['page']!='') {
                                        $ret = 4;
                                        echo $ret;
                                    } else {
                                         if($user['Userprofile']['edivactivity_reminder']=='Performance') {
                                            $ret = '1-3';
                                            echo $ret;
                                        }else if($user['Userprofile']['edivactivity_reminder']=='Lounge') {
                                            $ret = '1-1';
                                            echo $ret;
                                        }else if($user['Userprofile']['edivactivity_reminder']=='eDiv') {
                                            $ret = '1-2';
                                            echo $ret;
                                        }else if($user['Userprofile']['edivactivity_reminder']=='My Courses') {
                                            $ret = '1-4';
                                            echo $ret;
                                        } else  {
                                            $ret = '1-4';
                                            echo $ret;
                                        }
                                    }	
                                    }else{
                                    	$this->Session->write('md5uid' , $user['User']['id']);
                                    	$ret = '7-'.md5($user['User']['id']);
                                        echo $ret;
                                    	
                                    }
                                    
                                } else {
                                     $ret = 5;
                                     echo $ret;
                                }
                            }
		} else {
                        $ret = 3;
                        echo $ret;
		}
            }
        }
}


/*
 * Dashboard page function
 */
function dashboard($acorid=NULL,$id2=NULL,$id3=NULL) {
    $this->checkuserlog();
    $this->layout='default';
    $pollDt = date('Y-m-d');
   $userID=$this->Session->read('User.id');
   $this->User->id=$userID;
 $userinfo = $this->User->read();
 $this->set('userinfo',$userinfo);

 /*
  * ********************************************************
  * Check the user is newer first time comming to the site .
  * And challenged by some on .Is redirected to challengedby
  * Page
  * *********************************************************
  * 
  */

 $email = $this->useremailchallenged($userID);
 $countchallenge = $this->Challengetemp->find('all',array('conditions'=>'to_email="'.$email.'"'));
 $staticLoungeArray = $this->Staticloungevalue->find('all');
 $staticLongeContent=array();
 for($i=0;$i<count($staticLoungeArray);$i++){
   if($this->Session->read('Config.language')=='en-gb'){
     $staticLongeContent[$staticLoungeArray[$i]['Staticloungevalue']['key']]=$staticLoungeArray[$i]['Staticloungevalue']['value'];
   }elseif($this->Session->read('Config.language')=='zh-cn'){
     $staticLongeContent[$staticLoungeArray[$i]['Staticloungevalue']['key']]=$staticLoungeArray[$i]['Staticloungevalue']['value_chn'];
   }
 }
 $this->set('staticLongeContent',$staticLongeContent);
 if(count($countchallenge)>0){
     $this->redirect(array('controller'=>'challenges','action'=>'challengedby'));
 }
/*/////////////////////////////////////////////////////////
  * ********************************************************
  * End of the redirect User  .
  * *********************************************************
  * /////////////////////////////////////////////////////////
  */

    if(!empty($this->data)){
         $val = explode('_',$this->data['User']['pollans']);
         $arInputPoll = array();
         $arInputPoll['id']='';
         $arInputPoll['uid']=$this->Session->read('User.id');
         $arInputPoll['poll_ans_id']=$val[0];
         $arInputPoll['poll_q_id']=$val[1];
         $arInputPoll['date_time']=date('Y-m-d H:i:s');
         $arInputPoll['points']=$this->pointforediv(3);

         if($this->Polluserattempt->save($arInputPoll)){
              $edivData['id']='';
              $edivData['uid']=$this->Session->read('User.id');
              $edivData['ediv_type']=3;
              $edivData['points']=$this->pointforediv(3);
              if($this->ediv_user_masters->save($edivData)){
                     $this->redirect('/users/dashboard');
              }
         }
    } else {
         $slott=0;

             if(strtotime('00:00:00') < time() && time() < strtotime('06:59:59')){
                $slott = 1;
             }
             if(strtotime('07:00:00') < time() && time() < strtotime('12:59:59')){
                 $slott = 2;
             }
             if(strtotime('13:00:00') < time() && time() < strtotime('18:59:59')){
                $slott = 3;
             }
             if(strtotime('19:00:00') < time() && time() < strtotime('23:59:59')){
                $slott = 4;
             }

          $checkPoll = $this->Polluserattempt->find('all',array("conditions"=>"DATE_FORMAT(date_time,'%Y-%m-%d') ='$pollDt' and day_slot = '".$slott."' and uid = ".$this->Session->read('User.id')));
          if(empty($checkPoll)){
              $data = $this->Poll->find('all',array('conditions'=>'date_of_poll = "'.$pollDt.'" and day_slot = "'.$slott.'"'));

               if(empty($data)){
                 $downCounter = $slott-1;
                 $pollEnotinslot = $this->Poll->find('all',array('conditions'=>'date_of_poll = "'.$pollDt.'"' ,'order' => array('day_slot')));

                if(!empty($pollEnotinslot)){
                 while($slott > 0){
                  if($downCounter > 0){
                    for($i=0;$i<count($pollEnotinslot);$i++){
                        // Search for the previous poll and disply //
                       if($downCounter == $pollEnotinslot[$i]['Poll']['day_slot'])
                       {
                            $this->set('data',$pollEnotinslot[$i]['Poll']['poll_questions']);
                            $this->set('day_slot',$pollEnotinslot[$i]['Poll']['day_slot']);
                            $ansdata = $this->Pollanswer->find('all',array('conditions'=>'poll_qid='.$pollEnotinslot[$i]['Poll']['id']));

                            $this->set('ans',$ansdata);
                            $response = $this->getPollResults2($pollEnotinslot[0]['Poll']['id']);
                            $this->set('response',$response);
                             $downCounter--;
                            break 2;
                           // exit();
                       }
                       $downCounter--;
                       //$slott--;
                     }
                   } else {
                       $this->set('nodata','nodata');
                       break;

                   }
                 }
                }else{
                    $this->set('nodata','nodata');
                }
             }else{
                $this->set('data',$data[0]['Poll']['poll_questions']);
                $this->set('day_slot',$data[0]['Poll']['day_slot']);
                $ansdata = $this->Pollanswer->find('all',array('conditions'=>'poll_qid='.$data[0]['Poll']['id']));
                $this->set('ans',$ansdata);
             }
           }else{
                $data = $this->Poll->find('all',array('conditions'=>'date_of_poll = "'.$pollDt.'" and day_slot = "'.$slott.'" '));
                if(!empty($data)) {
                   $this->set('data',$data[0]['Poll']['poll_questions']);
                   $this->set('day_slot',$data[0]['Poll']['day_slot']);
                   $ansdata = $this->Pollanswer->find('all',array('conditions'=>'poll_qid='.$data[0]['Poll']['id']));
                   $this->set('ans',$ansdata);
                   $response = $this->getPollResults2($checkPoll[0]['Polluserattempt']['poll_q_id']);
                   $this->set('response',$response);
                } else {
                   $this->set('nodata','nodata');
                }
           }
     }

 $userEdivStatus = $this->User->find(array('User.id' => $userID), array('User.edivstatus'));
 $this->set('edivstatus',$userEdivStatus['User']['edivstatus']);
/*********************************************************************************************************/
/********************** POINTS FOR REFERRAL, POLLS AND LANGUAGE - STARTS *********************************/
$edivRefQuery = $this->Ediv_user_master->query('SELECT MONTHNAME(datetime) AS MONTH , SUM(points) AS edivPoints FROM ediv_user_masters WHERE uid ='.$userID.' AND ediv_type=1 AND date_format(datetime, "%Y-%m")=date_format(now(), "%Y-%m") GROUP BY uid');
$this->set('edivRefValues',$edivRefQuery);

$edivPollQuery = $this->Ediv_user_master->query('SELECT MONTHNAME(datetime) AS MONTH , SUM(points) AS edivPoints FROM ediv_user_masters WHERE uid ='.$userID.' AND ediv_type=3 AND date_format(datetime, "%Y-%m")=date_format(now(), "%Y-%m") GROUP BY uid');
$this->set('edivPollValues',$edivPollQuery);

$edivLangQuery = $this->Ediv_user_master->query('SELECT MONTHNAME(datetime) AS MONTH , SUM(points) AS edivPoints FROM ediv_user_masters WHERE uid ='.$userID.' AND ediv_type=4 AND date_format(datetime, "%Y-%m")=date_format(now(), "%Y-%m") GROUP BY uid');
$this->set('edivLangValues',$edivLangQuery);

$edivQuizQuery = $this->Ediv_user_master->query('SELECT MONTHNAME(datetime) AS MONTH , SUM(points) AS edivPoints FROM ediv_user_masters WHERE uid ='.$userID.' AND ediv_type=8 AND date_format(datetime, "%Y-%m")=date_format(now(), "%Y-%m") GROUP BY uid');
$this->set('edivQuizValues',$edivQuizQuery);

$edivFeatQuery = $this->Ediv_user_master->query('SELECT MONTHNAME(datetime) AS MONTH , SUM(points) AS edivPoints FROM ediv_user_masters WHERE uid ='.$userID.' AND ediv_type=9 AND date_format(datetime, "%Y-%m")=date_format(now(), "%Y-%m") GROUP BY uid');
$this->set('edivFeatValues',$edivFeatQuery);

$edivStudentQuery = $this->Ediv_user_master->query('SELECT MONTHNAME(datetime) AS MONTH , SUM(points) AS edivPoints FROM ediv_user_masters WHERE uid ='.$userID.' AND (ediv_type=10 OR ediv_type=11) AND date_format(datetime, "%Y-%m")=date_format(now(), "%Y-%m") GROUP BY uid');
$this->set('edivStudentValues',$edivStudentQuery);

$edivRegQuery = $this->Ediv_user_master->query('SELECT MONTHNAME(datetime) AS MONTH , points AS edivPoints FROM ediv_user_masters WHERE uid ='.$userID.' AND ediv_type=6 AND date_format(datetime, "%Y-%m")=date_format(now(), "%Y-%m")');
$this->set('edivRegValues',$edivRegQuery);

$toeflQuery = $this->Quizuserattempt->find('all', array(
                                                        'fields' => array('sum(Quizuserattempt.points) AS Total, Quizuserattempt.catid, Quizuserattempt.uid'),
                                                        'conditions' => array('date_format(Quizuserattempt.played_date, "%Y")=date_format(now(), "%Y")','uid'=>$userID),
                                                        'order' =>  array('sum(Quizuserattempt.points) DESC'),
                                                        'group' => array('Quizuserattempt.catid'),
                                                        'limit' => 5));
$this->set('resToefl',$toeflQuery);

if($acorid!='') {
    $this->set('acord',$acorid);
}

/********************** POINTS FOR REFERRAL, POLLS AND LANGUAGE - ENDS *********************************/
/*********************************************************************************************************/

/*************************  MYPERFORMANCE and TOEFL - STARTS ************************************************************/
$perResults = $this->language_averages->query("select lavel,language_id, review_ave from language_averages where uid=".$userID);
$this->set('res',$perResults);

$perResults = $this->language_averages->query("select lavel,language_id, review_ave from language_averages where uid=".$userID);
$this->set('res',$perResults);
/*************************  MYPERFORMANCE and TOEFL - ENDS ************************************************************/
/* IMPORTANT QUESTIONS
*  TOEFL Questions User Play /
*/
$resultData = $this->Quizuserattempt->query("SELECT DATE_FORMAT(played_date, '%m/%d/%Y') AS DT, date_format(played_date, '%Y-%m-%d %H:%i') AS PD, GROUP_CONCAT(DISTINCT(catid) SEPARATOR ',') AS catid , SUM(points) AS point FROM `quiz_user_attempts` where uid =".$this->Session->read('User.id')." GROUP BY date_format(played_date, '%Y-%m-%d %H:%i') ORDER BY played_date DESC ");

	$learnData=array();
	for($i=0;$i<count($resultData);$i++){
            $learnData[$i]['DTT']=$resultData[$i][0]['PD'];
            $learnData[$i]['DT']=$resultData[$i][0]['DT'];
            $catnew = explode(',',$resultData[$i][0]['catid']);
            if(!empty($catnew[1])){
            	$learnData[$i]['catName'] = ucwords($this->catname($catnew[0])).' & '.ucwords($this->catname($catnew[1]));
            }else{
            	$learnData[$i]['catName'] = ucwords($this->catname($catnew[0]));
            }
            
            $learnData[$i]['point']=(strlen($resultData[$i][0]['point'])==1)? '0'.$resultData[$i][0]['point']:$resultData[$i][0]['point'];
	}
       
	$this->set('list',$learnData);
 // Replay Question //
        $recordPlay = $this->Quizuserattempt->find('all',array('fields'=>'Distinct(catid),uid','conditions'=>'uid ='.$this->Session->read('User.id'), 'limit'=>'1','order'=>'rand()'));
        $this->set('rePlay',$recordPlay[0]['Quizuserattempt']['catid']);
// End Replay
/* Featured QUESTIONS
*  Featured Questions User Play /
*/
        $resultFeatured = $this->Sponsern->find('all',array('conditions'=>'sponserd = 1 and status = 1','limit'=>1));
        $this->set('resultFeaturedd',$resultFeatured);
        if($id2!=''){
            $this->set('id2','featured');
        }
/* Featured QUESTIONS
*  Featured Questions User Play /
*/
        $resultFeatured = $this->Feature->find('all',array('conditions'=>'status = 1', 'order'=>'id DESC'));
        $this->set('resultFeatured',$resultFeatured);
        if($id2!=''){
            $this->set('id2','featured');
        }
        if($id3!=''){
            $this->set('id3','show');
        }
		/*Displaying cafe series content //
  * 
  */   
         $cafeseriescontent = $this->Cafeseriescontent->find('all',array('conditions'=>'Cafeseriescontent.st_dt <= NOW() AND NOW() <= Cafeseriescontent.end_dt and Cafeseriescontent.status="1"','order'=>array('Cafeseriescontent.id'=>'DESC'),'limit'=>1));    
         $this->set('cafeseriescontent',$cafeseriescontent); 
}
function userattemptfeatured(){
    $userID=$this->Session->Read('User.id');  // Reading the session user Id
    $this->autoRender=false;
    if($this->RequestHandler->isAjax()){
             Configure::write('debug', 0);
             $val = $this->Featureuserattempt->find('all',array('conditions'=>'featured_id ='.$_REQUEST['id'].'  and uid='.$this->Session->read('User.id')));

             if(count($val)>0){
                 echo '0';
             }else{
                 echo $_REQUEST['id'];
             }

       }
}

function poll_update() {
    $userID=$this->Session->Read('User.id');  // Reading the session user Id
    $this->autoRender=false;
          if($this->RequestHandler->isAjax()){
             Configure::write('debug', 0);
             $edivQuery = $this->Ediv_user_master->query('SELECT DATE_FORMAT(DATETIME,"%M")  AS MONTH , GROUP_CONCAT(ediv_type) AS edivType , GROUP_CONCAT(points) AS edivPoints FROM ediv_user_masters WHERE uid ='.$userID.' GROUP BY DATE_FORMAT(DATETIME,"%M")');
                if(strstr($edivQuery[0][0]['edivType'],',')) {
                    $eDivType = explode(',',$edivQuery[0][0]['edivType']);
                    array_pop($eDivType);
                }
                if(strstr($edivQuery[0][0]['edivPoints'],',')) {
                    $edivPoints = explode(',',$edivQuery[0][0]['edivPoints']);
                    array_pop($edivPoints);
                }
                echo intval($edivPoints[0]).'-'.intval($edivPoints[1]).'-'.intval($edivPoints[2]);
         }
}

function ediv_update() {
    $this->checkuserlogin(); // Stop the accesing the section //
    $userID=$this->Session->Read('User.id');  // Reading the session user Id
    $this->autoRender=false;
          if($this->RequestHandler->isAjax()){
             Configure::write('debug', 0);
             $this->User->updateAll(array('User.edivstatus' => 1),array('User.id'=>$userID));
          }
 }
 function myp_update() {
    $this->checkuserlogin(); // Stop the accesing the section //
    $userID=$this->Session->Read('User.id');  // Reading the session user Id
    $this->autoRender=false;
          if($this->RequestHandler->isAjax()){
             Configure::write('debug', 0);
             $this->User->updateAll(array('Userprofile.performance_flag' => 1),array('User.id'=>$userID));
          }
 }

 /********************************** POLL STARTS ***********************************************/
 function getPollResults2($pollID){
     $userID=$this->Session->read('User.id');
        $colorArray = array(1 => "#ffcc00", "#00ff00", "#cc0000", "#0066cc", "#ff0099", "#ffcc00", "#00ff00", "#cc0000", "#0066cc", "#ff0099");
	$colorCounter = 1;
        $pollresults="";
        $userAttemptPoll = $this->Polluserattempt->find('all',array('conditions'=>array('Polluserattempt.uid'=>$userID)));
        if(!empty($userAttemptPoll)) {
            $getPollPointsQuery = $this->Polluserattempt->find('all',array('conditions'=>array('Polluserattempt.poll_q_id'=>$pollID)));
               for($i=0; $i<count($getPollPointsQuery); $i++) {
               $getPollPointsCountQuery = $this->Polluserattempt->find('count',array('conditions'=>array('Polluserattempt.poll_q_id'=>$pollID,'Polluserattempt.poll_ans_id'=>$getPollPointsQuery[$i]['Polluserattempt']['poll_ans_id'])));
                    if ($pollResults == "") {
                        $pollResults = $getPollPointsQuery[$i]['Polluserattempt']['poll_ans_id'] . "|" . $getPollPointsCountQuery . "|" . $colorArray[$colorCounter];
                    } else {
                        $pollResults = $pollResults . "-" . $getPollPointsQuery[$i]['Polluserattempt']['poll_ans_id'] . "|" . $getPollPointsCountQuery . "|" . $colorArray[$colorCounter];
                    }
                    $colorCounter = $colorCounter + 1;
                }
         $getPollTotalPointsCountQuery = $this->Polluserattempt->find('count',array('conditions'=>array('Polluserattempt.poll_q_id'=>$pollID)));
         $pollResults = $pollResults . "-" . $getPollTotalPointsCountQuery;
       }
	return $pollResults;
}
function getPollID($pollQuesID,$userID){
     $slott=0;
             if(strtotime('00:00:00') < time() && time() < strtotime('06:59:59')){
                $slott = 1;
             }
             if(strtotime('07:00:00') < time() && time() < strtotime('12:59:59')){
                 $slott = 2;
             }
             if(strtotime('13:00:00') < time() && time() < strtotime('18:59:59')){
                $slott = 3;
             }
             if(strtotime('19:00:00') < time() && time() < strtotime('24:00:00')){
                $slott = 4;
             }
        $curdate = date('Y-m-d');
        $chkPollUserSubmitQuery = $this->Polluserattempt->find('count',array('conditions'=>array('Polluserattempt.poll_q_id'=>$pollQuesID,'Polluserattempt.uid'=>$userID,"DATE_FORMAT(Polluserattempt.date_time, '%Y-%m-%d') = '$curdate'"),'day_slot' => '"'.$slott.'"'));
       return $chkPollUserSubmitQuery;
}
function getPollResults($pollID){

	$colorArray = array(1 => "#ffcc00", "#00ff00", "#cc0000", "#0066cc", "#ff0099", "#ffcc00", "#00ff00", "#cc0000", "#0066cc", "#ff0099");
	$colorCounter = 1;
       $getPollPointsQuery = $this->Polluserattempt->find('all',array('conditions'=>array('Polluserattempt.poll_q_id'=>$pollID)));

        for($i=0; $i<count($getPollPointsQuery); $i++) {
            $getPollPointsCountQuery = $this->Polluserattempt->find('count',array('conditions'=>array('Polluserattempt.poll_q_id'=>$pollID,'Polluserattempt.poll_ans_id'=>$getPollPointsQuery[$i]['Polluserattempt']['poll_ans_id'])));
            if ($pollResults == "") {
                $pollResults = $getPollPointsQuery[$i]['Polluserattempt']['poll_ans_id'] . "|" . $getPollPointsCountQuery . "|" . $colorArray[$colorCounter];
            } else {
                $pollResults = $pollResults . "-" . $getPollPointsQuery[$i]['Polluserattempt']['poll_ans_id'] . "|" . $getPollPointsCountQuery . "|" . $colorArray[$colorCounter];
            }
            $colorCounter = $colorCounter + 1;
        }
         $getPollTotalPointsCountQuery = $this->Polluserattempt->find('count',array('conditions'=>array('Polluserattempt.poll_q_id'=>$pollID)));

	$pollResults = $pollResults . "-" . $getPollTotalPointsCountQuery;
	echo $pollResults;
}
function polldata($action=NULL) {
     $this->checkuserlogin(); // Stop the accesing the section //
    $this->layout='logdefault';
    $userID=$this->Session->Read('User.id');  // Reading the session user Id
    $this->autoRender=false;
          if($this->RequestHandler->isAjax()){
             Configure::write('debug', 0);
                if(!empty($this->data)) {

                    if ($this->data['Polluserattempt']['action'] == "vote"){
                        $val = explode('_',$this->data['Polluserattempt']['pollans']);
                        $pollAnsID = $val[0];
                        $pollQuesID = $val[1];
                        if($this->getPollID($pollQuesID,$userID)>0) {
                                echo "voted";
                        } else {
                            $this->data['Polluserattempt']['uid']=$userID;
                            $this->data['Polluserattempt']['poll_ans_id']=$pollAnsID;
                            $this->data['Polluserattempt']['poll_q_id']=$pollQuesID;
                            $this->data['Polluserattempt']['points']=$this->pointforediv(3);
                            $this->data['Polluserattempt']['date_time']=date('Y-m-d H:i:s');
                           $this->Polluserattempt->save($this->data);
                            $this->data['Ediv_user_master']['uid']=$userID;
                            $this->data['Ediv_user_master']['ediv_type']=3;
                            $this->data['Ediv_user_master']['points']=$this->pointforediv(3);
                            $this->Ediv_user_master->save($this->data);
                            $this->getPollResults($pollQuesID);
                        }
                    }
                }
          }
}

 /********************************** POLL ENDS **************************************************/

/*
* _______________________________________________________________________
* Method newaccountemail
* Purpose: making new user registration sending mail
* Parameters : None
* ________________________________________________________________________
*/
function newaccountemail($email,$activation,$fname,$password){
    $this->Email->to = $email;
    $this->Email->bcc = array('nikesh.kumar@meridiansys.com');
    $this->Email->subject = $this->communicationsubject(1);;
    $this->Email->from = 'Smartsed Web App <admin@smartsed.com>';
    $this->Email->template = 'account'; // note no '.ctp'
    $this->set('activationkey', $activation);
     $this->set('message', $this->communicationmessage(1));
     $this->set('bottom', $this->communicationbottom(1));
    $this->set('name', $fname);
    $this->set('password', $password);
    $this->set('email', $email);
    $this->set('activation_url' , "<a href=\""._HTTP_PATH."users/activateuser/".$activation."\">"._HTTP_PATH."users/activateuser/".$activation."</a>");

    $this->Email->sendAs = 'both';
    if($this->Email->send()){
        return true;
    }else{
        return false;
    }

}


/*
* __________________________________________________________________________
* Method logout
* Purpose: make logout and delete all the user related session
* Parameters : None
* ___________________________________________________________________________
*/
function logout(){
if($this->Session->check('User')) {
/* UPDATE LOGIN STATUS FOR LOGGED IN USER - STARTS */
$uid = $this->Session->read('User.id');
$this->User->updateAll(array('User.login_status' => 0),array('User.id'=>$uid));
/* UPDATE LOGIN STATUS FOR LOGGED IN USER - ENDS */

$this->Session->delete('User');
//$this->Session->setFlash('You have successfully logged out');
}
$this->redirect(array('action'=>'/index'));
}

/*
* ___________________________________________________________________________
* Method myaccount
* Purpose: make myaccount
* Parameters : None
* _____________________________________________________________________________
*/

function myaccount($val=NULL){
$this->checkuserlogin(); // Stop the accesing the section //
$userID     =$this->Session->read('User.id');  // Reading the session user Id
$this->User->id=$userID;
 $this->data = $this->User->read();
  $dob = explode("-",$this->data['Userprofile']['dob']);
    $year = $dob[0];
    $month = $dob[1];
    $day = $dob[2];
    $this->set('year',$year);
    $this->set('month',$month);
    $this->set('day',$day);
   $hobby = explode(",",$this->data['Userprofile']['hobby']);
   $this->set('hobby',$hobby);

 $checkReferral = array(
  0=>$this->data['Userprofile']['announce_reminder'],
  1=>$this->data['Userprofile']['referral_reminder'],
  2=>$this->data['Userprofile']['edivactivity_reminder'],
  3=>$this->data['Userprofile']['reminder_reminder']
  );

  $this->set('checkReferral',$checkReferral);
  $colectRef = $this->Userprofile->find('all',array('conditions'=>array('Userprofile.referrals'=>$userID)));
  $this->set('colectRef',$colectRef);
 // Ediv Tab //
  $edivUser = $this->Ediv_user_master->find('all',array('conditions'=>array('uid' => $userID,  'MONTH(datetime)' => date('m'))));
  $this->set('edivUser',$edivUser);
  //////////////////// For Reward History ///////////////////START////////////////////////
  $UserWinAmt = $this->user_winning_points->find('all',array('conditions'=>'uid ='.$userID));
  $this->set('UserWinAmt',$UserWinAmt);
  $UserReAmt = $this->Redeem->find('all',array('conditions'=>'uid ='.$userID));
  $this->set('UserReAmt',$UserReAmt);
  /////////////////////////////END REWARD END ///////////////////////////////////////////////////////////
// End Tab //
/// For charity //
  $usercharity = $this->Charityuser->find('all',array('conditions'=>'uid ='.$userID));
  if(!empty($usercharity)){
  $charity = $this->Charity->find('all',array('fields'=>'Charity.title,Charity.logo_randname,Charity.id','conditions'=>'Charity.id ='.$usercharity[0]['Charityuser']['charity_id']));
  $charitygiven=$this->Charityuserfriend->find('all',array('fields'=>'sum(amount) as total','conditions'=>'charity_id='.$usercharity[0]['Charityuser']['charity_id'].' and uid='.$userID,'group'=>'uid'));
  
  $this->set('charitygiven',$charitygiven[0][0]['total']);
  $this->set('usercharity',$usercharity);
  $this->set('charity',$charity);
$multiplier = $this->Edivcharityvalue->find('all',array('fields'=>'ediv_charity_amount','conditions'=>'Edivcharityvalue.status=1','limit'=>1));
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
  $userCollection = $this->Charityuser->find('all',array('fields'=>"(SUM(Quizuserattempt.points)/".$this->pointforediv(8).")*(".($multiplier[0]['Edivcharityvalue']['ediv_charity_amount']/100).") as Collect,Charityuser.target",'conditions' =>'Charityuser.uid='.$userID.' And Charityuser.charity_id = '.$usercharity[0]['Charityuser']['charity_id'],'group' => 'Charityuser.uid'));

  $communityUserCollection = $this->Charityuser->find('all',array('fields'=>"(SUM(Quizuserattempt.points)/".$this->pointforediv(8).")*(".($multiplier[0]['Edivcharityvalue']['ediv_charity_amount']/100).") as Collect,Charityuser.target",'conditions' =>'Charityuser.charity_id = '.$usercharity[0]['Charityuser']['charity_id'],'group' => 'Charityuser.uid'));
  $communityFriendsUserCollection = $this->Charityuserfriend->find('all',array('fields'=>"amount",'conditions' =>'Charityuserfriend.charity_id = '.$usercharity[0]['Charityuser']['charity_id'],'group' => 'Charityuserfriend.charity_id'));

  $this->Charityuser->unBindModel(array('belongsTo' => array('Quizuserattempt')));
  $sum=0;
  for($m=0;$m<count($communityUserCollection);$m++){
    $sum= $sum + $communityUserCollection[$m][0]['Collect'];
  }
  
  $totalcharity = $sum+$communityFriendsUserCollection[0]['Charityuserfriend']['amount'];
  $this->set('total',$totalcharity);
  
    if(!empty($userCollection)){
                 $this->set('userCollect',$userCollection[0][0]['Collect']);
         }else{
                 $this->set('userCollect',0);
         }
  
  }
  /// End Charity //
  



  $languages = $this->Language->find('list',array('conditions' => null,
                                                'order' => 'Language.name ASC',
                                                'limit' => null,
                                                'fields' => 'Language.name', 'Language.id'));
$this->set('languageList',$languages);
$countries = $this->Country_master->find('list',array('conditions' => array('Country_master.status' => '1'),
                                                'order' => 'Country_master.country_name ASC',
                                                'limit' => null,
                                                'fields' => 'Country_master.country_name', 'Country_master.id'));
$this->set('countryList',$countries);
if($this->data['Userprofile']['country'] == '40') {
    $stateList = $this->State_master->find('list',array('conditions' => array('State_master.status' => '1', 'State_master.country_id=40'),
                                                'order' => 'State_master.state_name ASC',
                                                'limit' => null,
                                                'fields' => 'State_master.state_name', 'State_master.id'));
    $this->set('stateList',$stateList);
}

if($this->data['Userprofile']['country'] == '40' && $this->data['Userprofile']['state'] != '') {
    $cityList = $this->City_master->find('list',array('conditions' => array('City_master.status' => '1', 'City_master.state_id='.$this->data['Userprofile']['state']),
                                                'order' => 'City_master.city_name ASC',
                                                'limit' => null,
                                                'fields' => 'City_master.city_name', 'City_master.id'));
    $this->set('cityList',$cityList);

}

$hobbiesArr = array('Photography','Business','Cooking','Fashion','Health & Fitness','Technology','Motor Sports','History','Music','Travel','Gaming','Art');
$this->set('hobbies',$hobbiesArr);

$proficiencyList = array('1'=>'Beginner','2'=>'Intermediate','3'=>'Expert');
$this->set('proficienyList',$proficiencyList);

foreach (range(2015, 1950) as $year) {
        $yearlist[$year] = $year;
    }
    $this->set('yearOptions',$yearlist);

for($d=01; $d<=31; $d++ ) {
        $d = sprintf("%02d", $d);
        $dayOptions[$d] = $d;
    }
    $this->set('dayOptions',$dayOptions);

$monthOptions = array('01'=>'Jan','02'=>'Feb','03'=>'Mar','04'=>'Apr','05'=>'May','06'=>'Jun','07'=>'Jul','08'=>'Aug','09'=>'Sept','10'=>'Oct','11'=>'Nov','12'=>'Dec');
    $this->set('monthOptions',$monthOptions);

if($val=='links') {
    $this->set('lin',$val);
}elseif($val=='charity') {
    $this->set('lin',$val);
}elseif($val=='dr'){
	$this->set('flg','Dr.');
}
elseif($val=='ms'){
	$this->set('flg','Ms.');
}
elseif($val=='mrs'){
	$this->set('flg','Mrs.');
}
elseif($val=='mr'){
	$this->set('flg','Mr.');
}
}

function edivDetails(){
    $this->checkuserlogin(); // Stop the accesing the section //
    $userID=$this->Session->Read('User.id');  // Reading the session user Id
    $this->autoRender=false;
          if($this->RequestHandler->isAjax()){
             Configure::write('debug', 0);
                 if(!empty($_POST)){
                   $sum=0;
                    $edivUser = $this->Ediv_user_master->query('SELECT * FROM ediv_user_masters WHERE uid ='.$userID.' AND  MONTH(datetime) ='. $_POST['month']);
                    $str ='<table cellspacing="3" class="coms edivs" style="width:100%;">';
                    $str .='<tr><td colspan="3"></td></tr>';
                      if(count($edivUser)>0) {
                         for($ii=0;$ii<count($edivUser);$ii++) {

                   $str.='<tr>
                     <td style="padding-left: 20px;" width="29%" align="left" valign="middle">';
                        $dt = explode(' ',$edivUser[$ii]['ediv_user_masters']['datetime']);

                        $str .= $dt[0];
                     $str.='</td>
                     <td width="48%" align="left" valign="middle" >';
                        if($edivUser[$ii]['ediv_user_masters']['ediv_type']==1){ $str.='Refer a Friend';}
                        if($edivUser[$ii]['ediv_user_masters']['ediv_type']==3){ $str.= 'Daily Poll';}
                        if($edivUser[$ii]['ediv_user_masters']['ediv_type']==4){ $str.= 'Language Lab';}
                        if($edivUser[$ii]['ediv_user_masters']['ediv_type']==6){ $str.= 'Register';}
                        if($edivUser[$ii]['ediv_user_masters']['ediv_type']==7){ $str.= 'Winner Amount';}
                        if($edivUser[$ii]['ediv_user_masters']['ediv_type']==8){ $str.= 'Quiz Point';}
                        if($edivUser[$ii]['ediv_user_masters']['ediv_type']==9){ $str.= 'Sponsored Point';}
                        if($edivUser[$ii]['ediv_user_masters']['ediv_type']==10){ $str.= 'Student Content Quiz';}
                        if($edivUser[$ii]['ediv_user_masters']['ediv_type']==11){ $str.= 'Student Content Poll';}

                    $str .='</td>
                     <td width="23%" align="center" valign="middle">';
                    $sum = $sum + $edivUser[$ii]['ediv_user_masters']['points'];
                    $str.=intval($edivUser[$ii]['ediv_user_masters']['points']);
                    $str .='</td>
                   </tr>';
                    } } else {
                   $str .='<tr>
                       <td colspan="3" align="center" style="color:#F08F16;"><strong>'.__d("account","no-activity",true).'</strong></td>
                   </tr>';
                   }
                   $str .='</table>';
                     echo $str.'_'.$sum;

                 }
          }
}

function refDetails(){
    $this->checkuserlogin(); // Stop the accesing the section //
    $userID=$this->Session->Read('User.id');  // Reading the session user Id
    $this->autoRender=false;
          if($this->RequestHandler->isAjax()){
             Configure::write('debug', 0);
                 if(!empty($this->data)){
                     if($this->data['Userprofile']['searchby']=='Name') {
                      $name = $this->data['Userprofile']['searchitem'];
                      $colectRef = $this->Userprofile->query('SELECT * FROM userprofiles LEFT JOIN users ON userprofiles.user_id = users.id WHERE userprofiles.referrals ='.$userID.' AND (userprofiles.first_name LIKE "%'.$name.'%" OR userprofiles.last_name LIKE "%'.$name.'%")');
                    }elseif($this->data['Userprofile']['searchby']=='Email') {
                      $email = $this->data['Userprofile']['searchitem'];
                      $colectRef = $this->Userprofile->query('SELECT * FROM userprofiles LEFT JOIN users ON userprofiles.user_id = users.id WHERE userprofiles.referrals ='.$userID.' AND users.email="'.$email.'"');
                    }

                  $this->set('colectRef',$colectRef);
                  $this->render('/users/refDetails', 'ajax');
                 }
          }
}

function update_email() {
    $this->checkuserlogin(); // Stop the accesing the section //
    $userID=$this->Session->Read('User.id');  // Reading the session user Id
    $this->autoRender=false;
          if($this->RequestHandler->isAjax()){
             Configure::write('debug', 0);
                 if(!empty($_POST)){
                    $this->User->updateAll(array('Userprofile.secondaryemail'=>'"'.$_POST['secemail'].'"'),array('User.id'=>$userID));
                 }
          }
}

function viewdetail($id=null){
   // $this->checkuserlogin();
    $this->layout = '';
    $results=$this->User->find('all',array('conditions'=>array('MD5(User.id)' =>$id)));
    $this->set('result',$results[0]);

    $languages = $this->Language->find('list',array('conditions' => null,
                                            'order' => 'Language.name ASC',
                                            'limit' => null,
                                            'fields' => 'Language.name', 'Language.id'));

$this->set('languageList',$languages);
$countries = $this->Country_master->find('list',array('conditions' => array('Country_master.status =' => '1'),
                                                'order' => 'Country_master.country_name ASC',
                                                'limit' => null,
                                                'fields' => 'Country_master.country_name', 'Country_master.id'));
$this->set('countryList',$countries);

if($this->data['Userprofile']['country'] == '40') {
    $stateList = $this->State_master->find('list',array('conditions' => array('State_master.status =' => '1 AND State_master.country_id=40'),
                                                'order' => 'State_master.state_name ASC',
                                                'limit' => null,
                                                'fields' => 'State_master.state_name', 'State_master.id'));
    $this->set('stateList',$stateList);
}

if($this->data['Userprofile']['country'] == '40' && $this->data['Userprofile']['state'] != '') {
    $cityList = $this->City_master->find('list',array('conditions' => array('City_master.status =' => '1 AND City_master.state_id='.$this->data['Userprofile']['state']),
                                                'order' => 'City_master.city_name ASC',
                                                'limit' => null,
                                                'fields' => 'City_master.city_name', 'City_master.id'));
    $this->set('cityList',$cityList);

}

$hobbiesArr = array('Photography','Business','Cooking','Fashion','Health & Fitness','Technology','Motor Sports','History','Music','Travel','Gaming','Art');
$this->set('hobbies',$hobbiesArr);

$proficiencyList = array('1'=>'Beginner','2'=>'Intermediate','3'=>'Expert');
$this->set('proficienyList',$proficiencyList);

}

/*_______________________________________________________________________________
/*
* Method updatereminder
* Purpose: update the reminder information
* Parameter : None
*
* ______________________________________________________________________________
*/

function updatereminder(){
$this->checkuserlogin(); // Stop the accesing the section //
    $userID=$this->Session->Read('User.id');  // Reading the session user Id
    $this->autoRender=false;
          if($this->RequestHandler->isAjax()){
             Configure::write('debug', 0);
                 if(!empty($this->data)){
                    $announce_reminder=$this->data['Userprofile']['g1'];
                    $referral_reminder=$this->data['Userprofile']['g2'];
                    $edivactivity_reminder=$this->data['Userprofile']['g3'];
                    $reminder_reminder=$this->data['Userprofile']['g4'];
                  if($announce_reminder!=''){
                  $this->Userprofile->updateAll(array('Userprofile.announce_reminder' => "'$announce_reminder'"),array('Userprofile.user_id'=>$userID));
                  }
                  if($announce_reminder!=''){
                  $this->Userprofile->updateAll(array('Userprofile.referral_reminder' => "'$referral_reminder'"),array('Userprofile.user_id'=>$userID));
                  }
                  if($edivactivity_reminder!=''){
                  $this->Userprofile->updateAll(array('Userprofile.edivactivity_reminder' => "'".$edivactivity_reminder."'"),array('Userprofile.user_id'=>$userID));
                  }
                  if($reminder_reminder!=''){
                  $this->Userprofile->updateAll(array('Userprofile.reminder_reminder' => "'$reminder_reminder'"),array('Userprofile.user_id'=>$userID));
                  }
                    echo '<font style="color:#ff0000;">Preferences Updated Successfully.</font>';
                 }
        }
}
/// Static functions made //
    function privacy(){
    	$this->layout='default';
    	$data = $this->Staticcontent->find('all',array('conditions'=>'id = "4" and status=1 '));

        $this->set('data',$data);
    }
    // Partner
     function partner(){
    	$this->layout='';

    }
    // term
     function terms(){
    	$this->layout='default';
    	$data = $this->Staticcontent->find('all',array('conditions'=>'id = "5" and status=1'));
        $this->set('data',$data);
    }

     function help(){
        $this->checkuserlog();
    	$this->layout='default';
    	$data = $this->Staticcontent->find('all',array('conditions'=>'id = "10" and status=1'));
        $this->set('data',$data);
    }
     function aboutus(){
    	$this->layout='default';
        $data = $this->Staticcontent->find('all',array('conditions'=>'tab_type = "The Company" and status=1'));
        //pr($data);
        $this->set('data',$data);
    }
    function contact(){
       $this->layout='default';
    }
    function advertise(){
       $this->layout='default';
    }
function con() {
    $this->autoRender=false;
      if($this->RequestHandler->isAjax()){
         Configure::write('debug', 0);
             if(!empty($this->data)){
                if(!empty($this->data) && $this->data['User']['final']=='final'){
                        $this->Email->smtpOptions = array(
                                                        'port'=>'465',
                                                        'timeout'=>'30',
                                                        'host' => 'ssl://smtp.gmail.com',
                                                        'username'=>'admin@smartsed.com',
                                                        'password'=>'Admin12345',
                                                        );
                       $this->Email->delivery = 'smtp';
                       $this->Email->to = 'admin@smartsed.com';
                       $this->Email->bcc = array('nikesh.kumar@meridiansys.com');
                       $this->Email->subject = $this->data['User']['subject'];
                      // $this->Email->from = $this->data['User']['email'];
                       $this->Email->template = 'contactus'; // note no '.ctp'

                       $this->set('name', $this->data['User']['firstname'].'  '.$this->data['User']['lastname']);
                       $this->set('useremail', $this->data['User']['email']);
                       $this->set('question', $this->data['User']['question']);

                       $this->Email->sendAs = 'both';

                       if($this->Email->send()){
                        echo "1";
                       }else{
                        echo "2";
                       }
                    }
             }
      }
}

function adv() {
    $this->autoRender=false;
      if($this->RequestHandler->isAjax()){
         Configure::write('debug', 0);
             if(!empty($this->data)){
                if(!empty($this->data) && $this->data['User']['final']=='final'){
                        $this->Email->smtpOptions = array(
                                                        'port'=>'465',
                                                        'timeout'=>'30',
                                                        'host' => 'ssl://smtp.gmail.com',
                                                        'username'=>'admin@smartsed.com',
                                                        'password'=>'Admin12345',
                                                        );
                       $this->Email->delivery = 'smtp';
//                       $this->Email->to = 'advertising@drsmarts.com';
                       $this->Email->to = 'pritam.gupta@meridiansys.com';
                       $this->Email->subject = $this->data['User']['subject'];
                      // $this->Email->from = $this->data['User']['email'];
                       $this->Email->template = 'contactus'; // note no '.ctp'

                       $this->set('name', $this->data['User']['firstname'].'  '.$this->data['User']['lastname']);
                       $this->set('useremail', $this->data['User']['email']);
                       $this->set('question', $this->data['User']['question']);

                       $this->Email->sendAs = 'both';

                       if($this->Email->send()){
                        echo "1";
                       }else{
                        echo "2";
                       }
                    }
             }
      }
}
/*********************************************************************/
/**************** PROFILE - STARTS ***********************************/
/*********************************************************************/


function cpassword(){
    $this->checkuserlogin(); // Stop the accesing the section //
    $userID=$this->Session->Read('User.id');  // Reading the session user Id
    $this->autoRender=false;
          if($this->RequestHandler->isAjax()){
             Configure::write('debug', 0);
                 if(!empty($this->data)){
                    $password = $this->data['User']['ppassword'];
                     $this->User->updateAll(array('User.password' => "'$password'"),array('User.id'=>$userID));
                     echo '<strong>Password Changed Successfully.</strong>';
                 }
          }
}

function profile_update() {
     $this->checkuserlogin(); // Stop the accesing the section //
    $this->layout='logdefault';
    $userID=$this->Session->Read('User.id');  // Reading the session user Id
    $this->autoRender=false;
          if($this->RequestHandler->isAjax()){
             Configure::write('debug', 0);
                if(!empty($this->data)) {
                  //echo '<pre>';  print_r($this->data); echo '</pre>';
                   $hobby = implode(",",$this->data['Userprofile']['hobby']);
                   $title = $this->data['Userprofile']['title'];
                    $month = $this->data['Userprofile']['month'];
                    $day = $this->data['Userprofile']['day'];
                    $year = $this->data['Userprofile']['year'];
                    $dob = $year.'-'.$month.'-'.$day;
                   if($this->data['Userprofile']['country'] == '40') {
                       $this->data['Userprofile']['otherstate']='';
                       $this->data['Userprofile']['othercity']='';
                   } else {
                      $this->data['Userprofile']['state']=0;
                      $this->data['Userprofile']['city']=0;
                   }
                   if($this->data['Userprofile']['title']=='Dr.'){
                   	$proftrack=$this->data['Userprofile']['professional_track'];
                   	$institute=$this->data['Userprofile']['institute'];
                   	$passingyear=$this->data['Userprofile']['passing_year'];
                   }else{
                   	$proftrack='';
                   	$institute='';
                   	$passingyear='';
                   }
                   $ret = $this->User->updateAll(
                                                array('Userprofile.title'=>"'$title'",
                                                      'Userprofile.first_name'=>'"'.$this->data['Userprofile']['first_name'].'"',
                                                      'Userprofile.last_name'=>'"'.$this->data['Userprofile']['last_name'].'"',
                                                    'Userprofile.nativelanguage'=>'"'. $this->data['Userprofile']['nativelanguage'],'"',
                                                    'Userprofile.fluentlanguage'=>'"'.$this->data['Userprofile']['fluentlanguage'].'"',
                                                    'Userprofile.fluentlanguage2'=>'"'.$this->data['Userprofile']['fluentlanguage2'].'"',
                                                    'Userprofile.fluentlanguage3'=>'"'.$this->data['Userprofile']['fluentlanguage3'].'"',
                                                    'Userprofile.fluentproficiency'=>'"'.$this->data['Userprofile']['fluentproficiency'].'"',
                                                    'Userprofile.fluentproficiency2'=>'"'.$this->data['Userprofile']['fluentproficiency2'].'"',
                                                    'Userprofile.fluentproficiency3'=>'"'.$this->data['Userprofile']['fluentproficiency3'].'"',
                                                    'Userprofile.learnlanguage'=>'"'.$this->data['Userprofile']['learnlanguage'].'"',
                                                    'Userprofile.learnlanguage2'=>'"'.$this->data['Userprofile']['learnlanguage2'].'"',
                                                    'Userprofile.learnlanguage3'=>'"'.$this->data['Userprofile']['learnlanguage3'].'"',
                                                    'Userprofile.learnproficiency'=>'"'.$this->data['Userprofile']['learnproficiency'].'"',
                                                    'Userprofile.learnproficiency2'=>'"'.$this->data['Userprofile']['learnproficiency2'].'"',
                                                    'Userprofile.learnproficiency3'=>'"'.$this->data['Userprofile']['learnproficiency3'].'"',
                                                    'Userprofile.country'=>$this->data['Userprofile']['country'],
                                                    'Userprofile.state'=>$this->data['Userprofile']['state'],
                                                    'Userprofile.city'=>$this->data['Userprofile']['city'],
                                                    'Userprofile.otherstate'=>'"'.$this->data['Userprofile']['otherstate'].'"',
                                                    'Userprofile.othercity'=>'"'.$this->data['Userprofile']['othercity'].'"',
                                                    'Userprofile.dob'=>"'$dob'",
                                                    'Userprofile.hobby'=>'"'.$hobby.'"',
                                                    'Userprofile.ispublic'=>$this->data['Userprofile']['ispublic'],
                                                	'Userprofile.professional_track'=>$this->data['Userprofile']['professional_track'],
                                                	'Userprofile.institute'=>$this->data['Userprofile']['institute'],
                                                	'Userprofile.passing_year'=>$this->data['Userprofile']['passing_year']
                                                    ),array('User.id'=>$userID)
                                               );

                   if($ret)
                           echo '<font style="color:#ff0000; font-weight:bold;">Profile Information Updated Successfully.</font>';
                   else
                       echo '<font style="color:#ff0000;font-weight:bold;">Some error has been occured. Please try again later.</font>';

                }
          }

}



 /*********************************************************************/
 /**************** PROFILE - ENDS *************************************/
 /*********************************************************************/


    function forgot_password(){
         $this->layout='';

    }
    function forgot_pass() {
        $this->autoRender=false;
          if($this->RequestHandler->isAjax()){
             Configure::write('debug', 0);
             $email = $this->data['User']['email'];
             $finder2 = array('User.email'=>"'$email'");
             $userAr = $this->User->find(array("(User.email='".trim($this->data['User']['email'])."')"), array('User.id','User.email','User.block','User.password','User.activationkey','Userprofile.first_name','Userprofile.last_name'));
             if(!empty($userAr)) {
                     $this->Email->to = $userAr['User']['email'];
                    $this->Email->bcc = array('nikesh.kumar@meridiansys.com');
                    $this->Email->subject = 'Smartsed forgot password';
                    $this->Email->from = 'admin@smartsed.com';
                    $this->Email->template = 'forgot_password';

                    $this->set('name', $userAr['Userprofile']['first_name']. ' ' . $userAr['Userprofile']['last_name']);
                    $this->set('password', $userAr['User']['password']);
                    $this->set('email', $userAr['User']['email']);

                    $this->Email->sendAs = 'both';
                    if($this->Email->send()){
                        echo __('Message_Success',true);
                       //echo 'Password has been sent successfully on registered email address.';
                    }else{
                         echo 'Some error has been occured. Please try again later.';
                    }

             } else {
                 echo __('Message_Fail',true);
                // echo 'Pleasse provide registerd email address.';
             }
          }
    }

/*
 * Testimonial ajax updation c4ca4238a0b923820dcc509a6f75849b
 */
function usertestimonial(){
    $this->autoRender=false;
          if($this->RequestHandler->isAjax()){
             Configure::write('debug', 0);
             if(!empty($this->data)){
             $testidata = array(
                 'id'=>'',
                 'uid'=>$this->data['Testimonial']['uid'],
                 'testimonial'=>$this->data['Testimonial']['testimonial']
             );
             $this->Testimonial->save($testidata);
             echo 'bbbb';
           }
     }
}

 function performance(){
      $userID=$this->Session->Read('User.id'); 
     $toeflQuery = $this->Quizuserattempt->find('all', array(
                                                        'fields' => array('sum(Quizuserattempt.points) AS Total, Quizuserattempt.catid, Quizuserattempt.uid'),
                                                        'conditions' => array('date_format(Quizuserattempt.played_date, "%Y")=date_format(now(), "%Y")','uid'=>$userID),
                                                        'order' =>  array('sum(Quizuserattempt.points) DESC'),
                                                        'group' => array('Quizuserattempt.catid')));
$this->set('resToefl',$toeflQuery);
 }
/*
*_______________________________________________________________________
* Method processpayment
* Purpose: Make Payment
* Parameters : None
* ________________________________________________________________________
*/
function processpayment($uid=NULL,$register=NULL){
           
	        $user = $this->User->find('all',array("conditions"=>"MD5(User.id)='".$uid."'"));
	        if(count($user)>0){
	        // check where user submit previous payment or Not //	
	        $payment = array();
			$this->set('uid',$uid);
			$this->set('user',$user[0]);
			if($this->checkpayment($user[0]['User']['id'])==1 && !empty($this->Session->Read('User.id'))){
			           $curdatetime = date('Y-m-d H:i:s');
			            $this->User->updateAll(array('User.login_status' => 1,'Userprofile.payment_status' =>'"1"', 'User.lastvisit_date'=>"'$curdatetime'"),array("MD5(User.id)"=>"$uid"));
						//$this->edivvalueupdationreminderemail($user[0]['User']['email']);
			            $this->Session->write('User.id' , $user[0]['User']['id']);
			            $this->Session->write('User.name' , $user[0]['Userprofile']['first_name'].' '.$user[0]['Userprofile']['last_name']);
			            $this->Session->write('User.email' , $user[0]['User']['email']);
			            $this->Session->write('User.userfname' , $user[0]['Userprofile']['first_name']);
			            $this->Session->write('User.timeout',time());
			            $this->Session->write('User.title' , $user[0]['Userprofile']['title']);
						$dashboardStatus = $this->get_user_info($user[0]['User']['id'], 'Userprofile.edivactivity_reminder',false);
			               if($dashboardStatus['Userprofile']['edivactivity_reminder']=='P' || $dashboardStatus['Userprofile']['edivactivity_reminder']=='S' || $dashboardStatus['Userprofile']['edivactivity_reminder']=='N' || $dashboardStatus['Userprofile']['edivactivity_reminder']=='') {
			                      $acordionstatus=4;
			                } elseif($dashboardStatus['Userprofile']['edivactivity_reminder']=='Performance') {
			                   $acordionstatus=3;
			                } elseif($dashboardStatus['Userprofile']['edivactivity_reminder']=='Lounge') {
			                    $acordionstatus=1;
			                }elseif($dashboardStatus['Userprofile']['edivactivity_reminder']=='eDiv') {
			                    $acordionstatus=2;
			                } elseif($dashboardStatus['Userprofile']['edivactivity_reminder']=='My Courses') {
			                    $acordionstatus=4;
			                }
			                 $this->redirect('/users/dashboard/'.$acordionstatus);
			}elseif($this->checkpayment($user[0]['User']['id'])==1 && empty($this->Session->Read('User.id'))){
				$this->redirect('/users/index');
			}
			elseif($this->checkeduemail($user[0]['User']['email'])==1){// for edu user
			            $curdatetime = date('Y-m-d H:i:s');
			            $this->User->updateAll(array('User.login_status' => 1,'Userprofile.payment_status' =>'"1"', 'User.lastvisit_date'=>"'$curdatetime'"),array("MD5(User.id)"=>"$uid"));
						//$this->edivvalueupdationreminderemail($user[0]['User']['email']);
			            $this->Session->write('User.id' , $user[0]['User']['id']);
			            $this->Session->write('User.name' , $user[0]['Userprofile']['first_name'].' '.$user[0]['Userprofile']['last_name']);
			            $this->Session->write('User.email' , $user[0]['User']['email']);
			            $this->Session->write('User.userfname' , $user[0]['Userprofile']['first_name']);
			            $this->Session->write('User.timeout',time());
			            $this->Session->write('User.title' , $user[0]['Userprofile']['title']);
						$dashboardStatus = $this->get_user_info($user[0]['User']['id'], 'Userprofile.edivactivity_reminder',false);
			               if($dashboardStatus['Userprofile']['edivactivity_reminder']=='P' || $dashboardStatus['Userprofile']['edivactivity_reminder']=='S' || $dashboardStatus['Userprofile']['edivactivity_reminder']=='N' || $dashboardStatus['Userprofile']['edivactivity_reminder']=='') {
			                      $acordionstatus=4;
			                } elseif($dashboardStatus['Userprofile']['edivactivity_reminder']=='Performance') {
			                   $acordionstatus=3;
			                } elseif($dashboardStatus['Userprofile']['edivactivity_reminder']=='Lounge') {
			                    $acordionstatus=1;
			                }elseif($dashboardStatus['Userprofile']['edivactivity_reminder']=='eDiv') {
			                    $acordionstatus=2;
			                } elseif($dashboardStatus['Userprofile']['edivactivity_reminder']=='My Courses') {
			                    $acordionstatus=4;
			                }
			                 $this->redirect('/users/dashboard/'.$acordionstatus);
			}else{
			if($this->checkpayment($user[0]['User']['id'])==1 && !empty($this->Session->Read('User.id'))){
				$this->set('failureresponse','paymentdone');
			}else{
				if(!empty($this->data)){
	        		$shippinginfo = array();
	    			$billinginfo = array("fname" =>$this->data['User']['fname'],
                            "lname" => $this->data['User']['lname'],
                            "address" => $this->data['User']['address'],
                            "city" => $this->data['User']['city'],
                            "state" => $this->data['User']['state'],
                            "country" =>$this->data['User']['country']);
	    			/*
	    			 * Main Process of payement //
	    			 */
	    			
	        		$response = $this->AuthorizeNet->chargeCard(AuthNetApiLogId, AuthNetTranKey , $this->data['User']['ccnum'], $this->data['User']['ccexpmonth'], $this->data['User']['ccexpyear'], $this->data['User']['ccver'], false, $this->data['User']['amount'], 0, 0,$this->data['User']['desc'] , $billinginfo, $this->data['User']['email'] , $this->data['User']['phone'] , $shippinginfo);
 				  
   					if($response[1]==1){// Success fully enter in the site
   					   $curdatetime = date('Y-m-d H:i:s');
			            $this->User->updateAll(array('User.login_status' => 1,'Userprofile.payment_status' =>'"1"', 'User.lastvisit_date'=>"'$curdatetime'"),array("MD5(User.id)"=>"$uid"));
						//$this->edivvalueupdationreminderemail($user[0]['User']['email']);
			            $this->Session->write('User.id' , $user[0]['User']['id']);
			            $this->Session->write('User.name' , $user[0]['Userprofile']['first_name'].' '.$user[0]['Userprofile']['last_name']);
			            $this->Session->write('User.email' , $user[0]['User']['email']);
			            $this->Session->write('User.userfname' , $user[0]['Userprofile']['first_name']);
			            $this->Session->write('User.timeout',time());
			            $this->Session->write('User.title' , $user[0]['Userprofile']['title']);
						$dashboardStatus = $this->get_user_info($user[0]['User']['id'], 'Userprofile.edivactivity_reminder',false);
			               if($dashboardStatus['Userprofile']['edivactivity_reminder']=='P' || $dashboardStatus['Userprofile']['edivactivity_reminder']=='S' || $dashboardStatus['Userprofile']['edivactivity_reminder']=='N' || $dashboardStatus['Userprofile']['edivactivity_reminder']=='') {
			                      $acordionstatus=4;
			                } elseif($dashboardStatus['Userprofile']['edivactivity_reminder']=='Performance') {
			                   $acordionstatus=3;
			                } elseif($dashboardStatus['Userprofile']['edivactivity_reminder']=='Lounge') {
			                    $acordionstatus=1;
			                }elseif($dashboardStatus['Userprofile']['edivactivity_reminder']=='eDiv') {
			                    $acordionstatus=2;
			                } elseif($dashboardStatus['Userprofile']['edivactivity_reminder']=='My Courses') {
			                    $acordionstatus=4;
			                }
			                $payment['id'] ='';
			                $payment['uid'] =$user[0]['User']['id'];
			                $payment['amount'] =$response[10];
			                $payment['paymentgateway'] ='Authorised.Net';
			                $this->Userpayment->save($payment);
			                $this->redirect('/users/dashboard/'.$acordionstatus);
			            	
   					}else{
   						$this->set('failureresponse',$response[4]);
   					}
	        	    		
	        	}
	        	if($register!=''){
	        		$this->set('failureresponse','Please make your payment');
	        	}
	        	
			  }
			}
	       }else{
	       	$this->set('failureresponse','You are not the existing user please make registration with us <a href="/smartsed/users/register">click here</a>');
	       }
	      
}
 

/*
*_______________________________________________________________________
* Method edivvalueupdationreminderemail
* Purpose: Sending winner mail
* Parameters : None
* ________________________________________________________________________
*/
function edivvalueupdationreminderemail($email){
    

    $this->Email->to = $email;
    $this->Email->bcc = array('nikesh.kumar@meridiansys.com');
    $this->Email->subject = $this->communicationsubject(8);
    $this->Email->from = 'Smartsed Web App <admin@smartsed.com>';
    $this->Email->template = 'reminders'; // note no '.ctp'
    $userInfo = $this->get_user_info($this->Session->Read('User.id'), 'Userprofile.first_name,Userprofile.last_name',false);
    $userName = $userInfo['Userprofile']['first_name'].' '.$userInfo['Userprofile']['last_name'];
    $topmesg = 'Dear&nbsp;'.$userName;
    $this->set('top', $topmesg);
    $this->set('message', $this->communicationmessage(8));
    $this->set('bottom', $this->communicationbottom(8));
    $this->Email->sendAs = 'both';
    if($this->Email->send()){
        return true;
    }else{
        return false;
    }
 }
function checkeduemail($email){
	 $ar = explode('.',$email);
     $chkedu = array_reverse($ar);
     if($chkedu[0]=='edu'){
     	return 1;
     }else{
     	return 0;
     }
}
function checkpayment($id){
	$userInfo = $this->get_user_info($id, 'Userprofile.payment_status',false);
    if($userInfo['Userprofile']['payment_status']=='1'){
    	return 1;
    }else{
    	return 2;
    }
}
 
// End Of the function for users_controller Class
}

?>
