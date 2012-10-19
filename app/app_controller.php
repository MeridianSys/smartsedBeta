<?php
App::import('Lib','Crypt/CryptClass',array('file'=>'CryptClass.php')); 
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2010, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2010, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       cake
 * @subpackage    cake.cake.libs.controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * This is a placeholder class.
 * Create the same file in app/app_controller.php
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package       cake
 * @subpackage    cake.cake.libs.controller
 * @link http://book.cakephp.org/view/957/The-App-Controller
 */
class AppController extends Controller {
    /**
     * crypt
     */
    private $Crypt = null;
    
    var $helpers = array('Html', 'Form', 'Javascript','Session','Text','Crypt');
    var $components = array('Session', 'RequestHandler','Usercomponent');
    var $uses = array('Feature','Quizcat','Quizquestion','Quizanswer','Quizuserattempt','User','Userprofile','Edivuser','poll_user_attempts','Redeem', 'languages','country_masters','state_masters','city_masters','Rewardproduct','Statickeyvalue','user_winning_points','Communication','Inbox', 'Sent', 'Trash','Pausetempuser','users_professional_tracks','institution_lists','Studentcontentcat');
     
function  beforeFilter() {
        Configure::write('contoller', $this);
  }

    /**
     * _encrypt()
     *
     * @param mixed $data
     * @return string
     */
    function _encrypt($data) {
        if(!$this->Crypt) { $this->__makeCrypt(); }
        return $this->Crypt->encrypt($data);
    }

    /**
     * _decrypt()
     *
     * @param mixed $data
     * @return mixed
     */
    function _decrypt($data) {
        if(!$this->Crypt) { $this->__makeCrypt(); }
        return $this->Crypt->decrypt($data);
    }

    /**
     * __makeCrypt()
     */
    function __makeCrypt() {
            $this->Crypt = new CryptClass(
            Configure::read('Cryptable.cipher'),
            Configure::read('Cryptable.key'),
            Configure::read('Cryptable.mode'),
            Configure::read('Cryptable.iv')
        );
    }

    
function  beforeRender(){
    $inactive = 1440;
    $userTimeOut =$this->Session->read('User.timeout');
if(isset($userTimeOut) && !empty($userTimeOut)) {
	$session_life = time() - $userTimeOut;
        
	if($session_life > $inactive)
        {
            $this->redirect('/users/logout');
        } else {
            $uid = $this->Session->read('User.id');
             $curdatetime = date('Y-m-d H:i:s');
             $this->User->updateAll(array('User.lastvisit_date' => "'$curdatetime'"),array('User.id'=>$uid));
        }
}
$this->Session->write('User.timeout',time());
 Configure::write('contoller', $this);
        $data = $this->Statickeyvalue->find('all');
        if($this->Session->read('Config.language')=='en-gb') {
           $welcome_note   = $data[0]['Statickeyvalue']['value'];
           $dashboard_tab1 = $data[1]['Statickeyvalue']['value'];
           $dashboard_tab2 = $data[2]['Statickeyvalue']['value'];
           $dashboard_tab3 = $data[3]['Statickeyvalue']['value'];
           $dashboard_tab4 = $data[4]['Statickeyvalue']['value'];
           $my_ac_tab1     = $data[5]['Statickeyvalue']['value'];
           $my_ac_tab2     = $data[6]['Statickeyvalue']['value'];
           $my_ac_tab3     = $data[7]['Statickeyvalue']['value'];
           $my_ac_tab4     = $data[8]['Statickeyvalue']['value'];
        } elseif($this->Session->read('Config.language')=='zh-cn') {
           $welcome_note   = $data[0]['Statickeyvalue']['value_chn'];
           $dashboard_tab1 = $data[1]['Statickeyvalue']['value_chn'];
           $dashboard_tab2 = $data[2]['Statickeyvalue']['value_chn'];
           $dashboard_tab3 = $data[3]['Statickeyvalue']['value_chn'];
           $dashboard_tab4 = $data[4]['Statickeyvalue']['value_chn'];
           $my_ac_tab1     = $data[5]['Statickeyvalue']['value_chn'];
           $my_ac_tab2     = $data[6]['Statickeyvalue']['value_chn'];
           $my_ac_tab3     = $data[7]['Statickeyvalue']['value_chn'];
           $my_ac_tab4     = $data[8]['Statickeyvalue']['value_chn'];
        }
           $this->set(compact('welcome_note','dashboard_tab1','dashboard_tab2','dashboard_tab3','dashboard_tab4','my_ac_tab1','my_ac_tab2','my_ac_tab3','my_ac_tab4'));
}


        function checkuseremail($email){
            App::import('Model', 'User');
            $user = new User();
            $checkname = $this->User->findbyemail($email);
            
             if($checkname['User']['email']!=''){
                 return false;

             }else{
                    return true;                 
             }
        }
        
        function checkuserlog() {
             $userId =$this->Session->read('User.id');
            $userName =$this->Session->read('User.email');
            if(empty($userId) && empty($userName)){
                return $this->redirect('/users/index/');
            }
        }

        function checkuserlogin($id=NULL){
            $userId =$this->Session->read('User.id');
            $userName =$this->Session->read('User.email');
            if(empty($userId) && empty($userName)){
                if($id!='' && $id!=2 && $id!=3) {
                    return $this->redirect('/users/index/'.$id);
                } else {
                    return $this->redirect('/users/index/');
                }
            }
        }

       /* GET USER BALANCE OF REDEEMPTION CART - STARTS */
        function get_user_balance($userID = '') {
            $redeemTransection = $this->Redeem->query('SELECT  SUM(redeem_points) AS redeemPoints FROM  redeems WHERE uid='.$userID.' GROUP BY uid');
            $redeemAmount = $redeemTransection[0][0]['redeemPoints'];
            $winningAmount = $this->user_winning_points->query('SELECT  SUM(amount) AS amount FROM  user_winning_points WHERE uid='.$userID.' GROUP BY uid');
            $winAmount = $winningAmount[0][0]['amount'];
            $remainingAmount = intval($winAmount)-intval($redeemAmount);
            $countryUser = $this->User->find('all',array('fields'=>'Userprofile.country','conditions'=>'User.id = '.$userID.' '));
            if($countryUser[0]['Userprofile']['country']==40){
                $disValue= CurMul;
                $remAmount=intval($remainingAmount*$disValue);
                
            }else{
                $disValue = 1;
                $remAmount=intval($remainingAmount);
            }
           
            return $remAmount;

        }
        /* GET USER BALANCE OF REDEEMPTION CART - ENDS */

    /* GET ANY USER INFO REGARDING PARTICULAR FIELD */
     function get_user_info($userId = '', $field_name='', $unbind = false) {
            $rowset = array();
            if(!empty($userId)) {

                    if($unbind) {
                            $this->User->unbindAll();
                    }
                    $rowset = $this->User->find(array('User.id' => $userId), array($field_name));
                    if( (isset($rowset)) && (is_array($rowset)) && (count($rowset) > 0) ) {
                       return $rowset;
                    }
            }
            return '';
    }
       

/* FUNCTION TO CHECK USER LOGIN TIME AND REMOVE FROM CHAT SECTION BY LOGOUT FROM THE SYSTEM AFTER GOOD 24 MIN - STARTS */
function chkuserlog() {
    $uid = $this->Session->read('User.id');
    $inactive = 1440;
    if(isset($_SESSION['timeout'])) {
        $session_life = time() - $_SESSION['timeout'];
        if($session_life > $inactive)
        {
            if($this->Session->check('User')) {
                $this->User->updateAll(array('User.login_status' => 0),array('User.id'=>$uid));
                $this->Session->delete('User');
            }
            $this->redirect(array('controller'=>'users', 'action'=>'index'));
        }
        else {
            $curdatetime = date('Y-m-d H:i:s');
            $this->User->updateAll(array('User.lastvisit_date' => "'$curdatetime'"),array('User.id'=>$uid));
            /* CHECK TO SEE IF USER IS IDLE FOR MORE THAN 1440sec (OR 24min) AFETR LAST LOGIN TIME then removed them from chat window */
            $chatUserList = $this->User->find('all', array(
                        'conditions' => array('TIMESTAMPDIFF(SECOND,User.lastvisit_date, "'.date('Y-m-d H:i:s').'") >' => 1440, 'User.login_status' => 1),
                        'fields' => 'User.id',
                        'order' => 'User.id ASC',
                    ));
            for($i=0; $i<count($chatUserList); $i++) {
                 $this->User->updateAll(array('User.login_status' => 0),array('User.id'=>$chatUserList[$i]['User']['id']));
            }

        }
    }
     $this->Session->write('timeout',time());
}
/* FUNCTION TO CHECK USER LOGIN TIME AND REMOVE FROM CHAT SECTION BY LOGOUT FROM THE SYSTEM AFTER GOOD 24 MIN - ENDS */

 /** GET ANY USER MESSAGE INFO AND DISPLAY IT ON FOOTER SECTION FOR LOGGED IN USER **/
function get_user_message() {

    $userID = $this->Session->read('User.id');

    $conditions[] = array("Inbox.to_id = '$userID'");
    $this->paginate = array('limit' => '10', 'order' => array('Inbox.datetime' => 'DESC'));
    $rowspop = $this->paginate('Inbox', $conditions);


    if(!empty($rowspop))
        return $rowspop;
    else
        return '0';
}

  /** GET ANY USER UNREAD MESSAGE COUNT INFO AND DISPLAY IT ON FOOTER SECTION FOR LOGGED IN USER **/

        function get_user_message_count() {
            $userID = $this->Session->read('User.id');
            $countUnread = $this->Inbox->find('count',array('conditions' => array('Inbox.status' => '0', 'Inbox.to_id' => $userID)));
           
             if($countUnread>0)
                return $countUnread;
            else
                return '0';
        }

        function get_user_message_count_total() {
            $userID = $this->Session->read('User.id');
            $counttot= $this->Inbox->find('count',array('conditions' => array('Inbox.to_id' => $userID)));

             if($counttot>0)
                return $counttot;
            else
                return '0';
        }
        

        function autoincrementid($model){

        	App::import('Model', $model);
        	
        	 $table=strtolower($model).'s';

        	 $query="SHOW TABLE STATUS LIKE '".$table."'";
        	 
        	 $tableStatus = $this->$model->query($query);
        	
        	 return $tableStatus[0]['TABLES']['Auto_increment'];
        	 
        }
        

        
        function checkadmin(){
        	
            $adminid      =$this->Session->read('adminid');
            $username =$this->Session->read('username');
            $isadmin =$this->Session->read('isadmin');
            if(empty($adminid) && empty($username) && empty($isadmin)){
                 
            	return $this->redirect('/admins/');// redirect to index page
               
                 
            }
            
        
        }
      ///// calculating diffrence between charity month ///
 
      function monthDifrence($charityDt){
      	$data = $this->User->query("SELECT TIMESTAMPDIFF(MONTH,'".$charityDt."','".date('Y-m-d H:i:s')."') AS m ");
      	return $data[0][0]['m'];
      }

        
        function polldatecheck($date){
           $check= $this->Poll->find('all',array('fields'=>'Poll.id',  'conditions'=>'date_of_poll="'.$date.'" '));
           if(count($check)>0){
           return false;
           }else{
           return true;
           }
            
        
        }

        function polldatecheckedit($date,$id){
        	if($date!='0000-00-00'){
                    $check= $this->Poll->find('all',array('conditions'=>'date_of_poll="'.$date.'" and id <> '.$id));
		 if(count($check)<1){
                   return true;
                 }else{
                   return false;
                   }
        	}else{
                    return true;
        	}
            
        
        }
    /*
* The function use to provide not esixting date
* method name :polldatesessioncheck
* Parameters  : $date=NULL,$daysess=NULL,$id=NULL
*
*/
function polldatesessioncheck($date=NULL,$daysess=NULL,$id=NULL){
    if(!isset($id) && $id != ''){
    $check= $this->Poll->find('all',array('conditions'=>'date_of_poll="'.$date.'" '));
    if(count($check)<4){
        $check1= $this->Poll->find('all',array('conditions'=>'date_of_poll="'.$date.'"  and day_slot="'.$daysess.'"  '));
        if(count($check1)<1){
            return true;
        }else{
            $this->Session->setFlash('Please change the session');
            return false;
        }
    }else{
        $this->Session->setFlash('Please change the date');
        return false;
    }
  }
 else{

        $check= $this->Poll->find('all',array('conditions'=>'id="'.$id.'" '));
        if($check[0]['Poll']['date_of_poll'] == $date &&  $check[0]['Poll']['day_slot'] == $daysess){
            return true;

            }else{
                if(count($check)<4){
                $check1= $this->Poll->find('all',array('conditions'=>'date_of_poll="'.$date.'"  and day_slot="'.$daysess.'"  '));
                if(count($check1)<1){
                    return true;
                }else{
                    $this->Session->setFlash('Please change the session');
                    return false;
                   }
                }else{
                        $this->Session->setFlash('Please change the date');
                        return false;
               }
       }
 }
}
  
       /** EDIV INFO - STARTS */
        function get_ediv($id=null) {
            $rowset = $this->Edivuser->find(array('Edivuser.id' => $id), array('Edivuser.ediv_point'));
             if( (isset($rowset)) && (is_array($rowset)) && (count($rowset) > 0) ) {
               return $rowset['Edivuser']['ediv_point'];
            }
        }
     /** EDIV INFO - ENDS */
/** EDIV name - STARTS */
        function get_ediv_name($id=null) {
            $rowset = $this->Edivuser->find(array('Edivuser.id' => $id), array('Edivuser.ediv_tag'));
             if( (isset($rowset)) && (is_array($rowset)) && (count($rowset) > 0) ) {
               return $rowset['Edivuser']['ediv_tag'];
            }
        }
     /** EDIV name - ENDS */
/** USER FIRST NAME INFO - STARTS */
        function UserFirstName($id=null) {
            $rowset = $this->User->find(array('User.id' => $id), array('Userprofile.first_name'));
             if( (isset($rowset)) && (is_array($rowset)) && (count($rowset) > 0) ) {
               return $rowset['Userprofile']['first_name'];
            }
        }
/** USER FIRST NAME INFO - ENDS */
         
/*
* The function provide count accourding to the answer Id 
* method name :pollcount
* Parameters  : id
*
*/
function pollcount($id){

	$data = $this->poll_user_attempts->query("SELECT COUNT(poll_ans_id) as count FROM poll_user_attempts where poll_ans_id = ".$id." GROUP BY poll_ans_id");
	if(!empty($data)){
		return $data[0][0]['count'];
	}else{
	
		 return 0;
	}
	
	
}   
/*
       * The function use to provide the next comming auto increment Id for tables which contain _ between there name
       * method name :autoincrementid_underscore 
       * Parameters  : model name
       *
       */
        
function autoincrementid_underscore($model){
   $tableStatus = $this->$model->query("SHOW TABLE STATUS LIKE '".$model."'");
    return $tableStatus[0]['TABLES']['Auto_increment'];
        	 
}
  
/*
* The function provide name of language
* method name :languagename
* Parameters  : id
*
*/
function languagename($id=NULL){

	    $data = $this->languages->query("SELECT name from languages where id = ".$id);
	 	return $data[0]['languages']['name'];

}
/*
* The function provide country name 
* method name :countryname
* Parameters  : id
*
*/
function countryname($id=NULL){
    $data = $this->country_masters->query("SELECT country_name from country_masters where id = ".$id);
    return $data[0]['country_masters']['country_name'];
}
/*
* The function provide state name 
* method name :statename
* Parameters  : id
*
*/
function statename($id=NULL){
   $data = $this->state_masters->query("SELECT state_name from state_masters where id = ".$id);
   return $data[0]['state_masters']['state_name'];
}
/*
* The function provide city name 
* method name :cityname
* Parameters  : id
*
*/
function cityname($id=NULL){
    $data = $this->city_masters->query("SELECT city_name from city_masters where id = ".$id);
    return $data[0]['city_masters']['city_name'];
}

/*
* The function points 
* method name :pointforediv
* Parameters  : id
*
*/
function pointforediv($id=NULL){
      $data = $this->Edivuser->query("SELECT ediv_point from ediv_masters where id = ".$id);
      return $data[0]['ediv_masters']['ediv_point'];
}

function productPrice($pid=NULL) {
     $data = $this->Rewardproduct->query("SELECT point from reward_products where id = ".$pid);
     return $data[0]['reward_products']['point'];
}

function productname($pid=NULL) {
     $data = $this->Rewardproduct->query("SELECT product_name from reward_products where id = ".$pid);
     return $data[0]['reward_products']['product_name'];
}
///////////////////// For admin setted mail ////////////////////
function communicationsubject($id=NULL) {
     $data = $this->Communication->query("SELECT subject from communications where id = ".$id);
     return $data[0]['communications']['subject'];
}
function communicationmessage($id=NULL) {
     $data = $this->Communication->query("SELECT message from communications where id = ".$id);
     return $data[0]['communications']['message'];
}
function communicationbottom($id=NULL) {
     $data = $this->Communication->query("SELECT bottom from communications where id = ".$id);
     return $data[0]['communications']['bottom'];
}
function catname($id=NULL) {
     $data = $this->Quizcat->query("SELECT category_name from quiz_category_masters where id = ".$id);
     return $data[0]['quiz_category_masters']['category_name'];
}
function fcompname($id){
	
	 if(strpos($_SERVER['REQUEST_URI'], 'sponserns')){
	 	
	 $data = $this->Sponsern->find('all',array('conditions'=>'id='.$id));
	if(!empty($data)){
		return $data[0]['Sponsern']['company_name'];
	}else{

		 return 0;
	}
	 }else{
	 	
	$data = $this->Feature->find('all',array('conditions'=>'id='.$id));
	if(!empty($data)){
		return $data[0]['Feature']['company_name'];
	}else{

		 return 0;
	}
	 }
}
function useremailchallenged($id){
	$data = $this->User->find('all',array('fields'=>'User.email','conditions'=>'User.id='.$id));
	if(!empty($data)){
		return $data[0]['User']['email'];
	}else{

		 return 0;
	}
}
function todalusercharity($charId=NULL){
	$sum=0;
	$result = $this->Charityuserfriend->find('all',array('fields'=>'Charityuserfriend.charity_id,sum(Charityuserfriend.amount) as sum','conditions'=>'Charityuserfriend.charity_id='.$charId,'group'=>'Charityuserfriend.charity_id'));
	if(!empty($result[0][0]['sum'])){
		$sum=$result[0][0]['sum'];
		return $sum;
	
	  }else{
	  	return $sum;
	  }
}
function todalusercharitycollection($userid=NULL,$charid=NULL){
	$sum=0;
	$result = $this->Charityuserfriend->find('all',array('fields'=>'sum(Charityuserfriend.amount) as sum','conditions'=>'Charityuserfriend.uid='.$userid.' and Charityuserfriend.charity_id='.$charid,'group'=>'Charityuserfriend.uid'));
	if(!empty($result[0][0]['sum'])){
		$sum=$result[0][0]['sum'];
		return $sum;
	
	  }else{
	  	return $sum;
	  }
}
 function pauseradio($u=NULL,$q=NULL,$a=NULL,$type=NULL){
	$check =0;
	$result = $this->Pausetempuser->find('all',array('conditions'=>'uid='.$u.' and qid='.$q.' and aid='.$a.' and type="'.$type.'"'));
	
	if(!empty($result)){
		$check = 1;
		return $check;
	}else{
	   return $check;
	}
}        
function givenuseranswercheck($u=NULL,$a=NULL){
	
	$userattempt = $this->Studentuserattempt->find('all',array('conditions'=>array('uid'=>$u,'aid'=>$a)));
	if(!empty($userattempt)){
		$result = $this->Studentanswer->find('all',array('fields'=>'result','conditions'=>array('Studentanswer.id'=>$a)));
		if($result[0]['Studentanswer']['result']==1){
			 $resultOk = 1; return $resultOk;
		}else{
			$resultOk = 0; return $resultOk;
		}
	}else{
	  $resultOk=2; return $resultOk; 
	}
	
}
function checkedvalidation($a=NULL){
	$result = $this->Studentanswer->find('all',array('fields'=>'result','conditions'=>array('Studentanswer.id'=>$a)));
	if($result[0]['Studentanswer']['result']==1){return $result[0]['Studentanswer']['result'];}else{ return $result[0]['Studentanswer']['result'];}
}
function pollcheckecomit($a=NULL){
	$result = $this->Studentuserattempt->find('all',array('fields'=>'final_result','conditions'=>array('Studentuserattempt.aid'=>$a)));
	if(!empty($result)){
		return 1;
	}else{
		return 0;
	}	
}
function pollgraphstudent($q=NULL,$a=NULL){
	$result = $this->Studentuserattempt->find('all',array('fields'=>'id,count(Studentuserattempt.id) as cnt','conditions'=>array('qid'=>$q),'group'=>'qid'));
	$base= round(100/$result[0][0]['cnt']);
	
	$multiplier = $this->Studentuserattempt->find('all',array('fields'=>'count(Studentuserattempt.id) as cnt','conditions'=>array('qid'=>$q,'aid'=>$a),'group'=>'aid'));
	if(!empty($multiplier)){
	$display = round($base*$multiplier[0][0]['cnt']);
	}else{
	$display=0;
	}
	return $display;
}
function pollgraphcafe($q=NULL,$a=NULL){
   	$result = $this->Cafeseriesuserattempt->find('all',array('fields'=>'id,count(Cafeseriesuserattempt.id) as cnt','conditions'=>array('qid'=>$q),'group'=>'qid'));
	$base= round(100/$result[0][0]['cnt']);

	$multiplier = $this->Cafeseriesuserattempt->find('all',array('fields'=>'count(Cafeseriesuserattempt.id) as cnt','conditions'=>array('qid'=>$q,'aid'=>$a),'group'=>'aid'));
	if(!empty($multiplier)){
	$display = round($base*$multiplier[0][0]['cnt']);
	}else{
	$display=0;
	}
	return $display;
}
function pollgraphsponsor($q=NULL,$a=NULL){
   	$result = $this->Sponsernuserattempt->find('all',array('fields'=>'id,count(Sponsernuserattempt.id) as cnt','conditions'=>array('qid'=>$q),'group'=>'qid'));
	$base= round(100/$result[0][0]['cnt']);

	$multiplier = $this->Sponsernuserattempt->find('all',array('fields'=>'count(Sponsernuserattempt.id) as cnt','conditions'=>array('qid'=>$q,'aid'=>$a),'group'=>'aid'));
	if(!empty($multiplier)){
	$display = round($base*$multiplier[0][0]['cnt']);
	}else{
	$display=0;
	}
	return $display;
}
function checkedvalidationvideo($a=NULL){
	$result = $this->Cafeseriesanswer->find('all',array('fields'=>'result','conditions'=>array('Cafeseriesanswer.id'=>$a)));
	if($result[0]['Cafeseriesanswer']['result']==1){return $result[0]['Cafeseriesanswer']['result'];}else{ return $result[0]['Cafeseriesanswer']['result'];}
}
function givenuseranswercheckvideo($u=NULL,$a=NULL){
	
	$userattempt = $this->Cafeseriesuserattempt->find('all',array('conditions'=>array('uid'=>$u,'aid'=>$a)));
	if(!empty($userattempt)){
		$result = $this->Cafeseriesanswer->find('all',array('fields'=>'result','conditions'=>array('Cafeseriesanswer.id'=>$a)));
		if($result[0]['Cafeseriesanswer']['result']==1){
			 $resultOk = 1; return $resultOk;
		}else{
			$resultOk = 0; return $resultOk;
		}
	}else{
	  $resultOk=2; return $resultOk; 
	}
	
} 
function checkedvalidationsponsern($a=NULL){
	$result = $this->Sponsernanswer->find('all',array('fields'=>'result','conditions'=>array('Sponsernanswer.id'=>$a)));
	if($result[0]['Sponsernanswer']['result']==1){return $result[0]['Sponsernanswer']['result'];}else{ return $result[0]['Sponsernanswer']['result'];}
}
function givenuseranswercheckspon($u=NULL,$a=NULL){
	
	$userattempt = $this->Sponsernuserattempt->find('all',array('conditions'=>array('uid'=>$u,'aid'=>$a)));
	if(!empty($userattempt)){
		$result = $this->Sponsernanswer->find('all',array('fields'=>'result','conditions'=>array('Sponsernanswer.id'=>$a)));
		if($result[0]['Sponsernanswer']['result']==1){
			 $resultOk = 1; return $resultOk;
		}else{
			$resultOk = 0; return $resultOk;
		}
	}else{
	  $resultOk=2; return $resultOk; 
	}
	
}
function studentcontentplayed($id=NULL){

	$result = $this->Studentuserattempt->find('all',array('fields'=>'id','conditions'=>'Studentuserattempt.content_id='.$id));
	if(!empty($result)){
	 return 1;
	}else{
	 return 2;
	}

}
function cafeseriescontentplayed($id=NULL){

	$result = $this->Cafeseriesuserattempt->find('all',array('fields'=>'id','conditions'=>'Cafeseriesuserattempt.cafeseries_id='.$id));
	if(!empty($result)){
	 return 1;
	}else{
	 return 2;
	}

}
function sponserncontentplayed($id=NULL){
	$result = $this->Sponsernuserattempt->find('all',array('fields'=>'id','conditions'=>'Sponsernuserattempt.sponsern_content_id='.$id));
	if(!empty($result)){
	 return 1;
	}else{
	 return 2;
	}
    }
function dailyquizcontentplayed($id=NULL){
	$result = $this->Quizuserattempt->find('all',array('fields'=>'id','conditions'=>'Quizuserattempt.qid='.$id));
        if(!empty($result)){
	 return 1;
	}else{
	 return 2;
	}
    }

function perquestionans($qid=NULL){
	$valAr = array();
        $result = $this->Quizuserattempt->find('all',array('fields'=>'count(id) as cnt','conditions'=>'qid='.$qid,'group'=>'qid'));
        $trueAns = $this->Quizuserattempt->find('all',array('fields'=>'count(id) as cnt','conditions'=>'points = 5 and qid='.$qid,'group'=>'qid'));
        if(!empty ($trueAns)){
            $ansPer = round(($trueAns[0][0]['cnt']/$result[0][0]['cnt'])*100);
            return $ansPer;
        }else{
            return 0;
        }
    }
function groupname($id=NULL){

	$name = $this->Group->find('all',array('conditions'=>'id='.$id));
	return $name[0]['Group']['groupname'];

}
function professionaltrk(){
    $result = $this->users_professional_tracks->query("select * from users_professional_tracks");
	for($i=0;$i<count($result);$i++){
    	$temp[$result[$i]['users_professional_tracks']['id']]=$result[$i]['users_professional_tracks']['professional_track'];
    }
    return  $temp;
}
function institution(){
    $result = $this->institution_lists->query("select * from institution_lists");
	for($i=0;$i<count($result);$i++){
    	$temp[$result[$i]['institution_lists']['id']]=$result[$i]['institution_lists']['name'];
    }
    return  $temp;
}
function youplayedachallenge($id=NULL){

	 $result = $this->Challengeuserattempt->find('all',array('fields'=>'sum(points) as sum','conditions'=>'challenged_id='.$id,'group'=>'challenged_id'));
	 if(!empty($result)){
		 $first = $result[0][0]['sum'];
		 $result2 = $this->Challenge->find('all',array('fields'=>'setofquiznum','conditions'=>'id='.$id));
		 $secondFlag = $result2[0]['Challenge']['setofquiznum'];
		 $result3=$this->Challengesenderattempt->find('all',array('fields'=>'sum(points) as sum','conditions'=>'id IN ('.$secondFlag.')'));
		 $second = $result3[0][0]['sum'];
		 if(($first-$second)>0){
		 	echo 'Win By<br>'.($first-$second)/$this->get_ediv(8).' Question(s)';
		 }elseif(($first-$second)==0){
		 echo 'Draw';
		 }elseif(($first-$second)<0){
		 echo 'Loose By<br>'.($second-$first)/$this->get_ediv(8).' Question(s)';
		 }else{
		 return 0;
		 }
	 }
	
}
function loginplayedachallenge($id=NULL){

	 $result = $this->Challengeuserattempt->find('all',array('fields'=>'sum(points) as sum','conditions'=>'challenged_id='.$id,'group'=>'challenged_id'));
	 if(!empty($result)){
		 $first = $result[0][0]['sum'];
		 $result2 = $this->Challenge->find('all',array('fields'=>'setofquiznum','conditions'=>'id='.$id));
		 $secondFlag = $result2[0]['Challenge']['setofquiznum'];
		 $result3=$this->Challengesenderattempt->find('all',array('fields'=>'sum(points) as sum','conditions'=>'id IN ('.$secondFlag.')'));
		 $second = $result3[0][0]['sum'];
		 if(($first-$second)>0){
		 	echo 'Loose By<br>'.($first-$second)/$this->get_ediv(8).' Question(s)';
		 }elseif(($first-$second)==0){
		 echo 'Draw';
		 }elseif(($first-$second)<0){
		 echo 'Win By<br>'.($second-$first)/$this->get_ediv(8).' Question(s)';
		 }else{
		 return 0;
		 }
	 }
	
}
function charityname($id=NULL){
 $result = $this->Charity->find('all',array('conditions'=>'id='.$id));
 return $result[0]['Charity']['title'];
}

function get_student_category($id=null) {
            $rowset = $this->Studentcontentcat->find(array('id' => $id), array('name'));
             if( (isset($rowset)) && (is_array($rowset)) && (count($rowset) > 0) ) {
               return $rowset['Studentcontentcat']['name'];
            }
        }
function getdrnondrcatid($id=NULL) {

	 if($id==2){
	 	
	 	$result = $this->Quizcat->find('all',array('conditions'=>'access_type in (2,0)','fields'=>'id','limit'=>2));
	 	return $result[0]['Quizcat']['id'].','.$result[1]['Quizcat']['id'];
	 }else{
	 
	 	$result = $this->Quizcat->find('all',array('conditions'=>'access_type in (1,0)','fields'=>'id','limit'=>2));
	 	return $result[0]['Quizcat']['id'].','.$result[1]['Quizcat']['id'];
	 }
 }
		
		
}/// End Of Class /// 
        

