<?php
/*
##################################################################################
#   Group Controller
#   file name        	: groups_controller.php
#   Developed        	: Meridian Radiology
#   Intialised Variables: name , helpers ,components , uses , layout = (default)
####################################################
*/

class GroupsController extends AppController {

	var $name = 'Groups';
        var $uses = array('User','Userprofile','Country_master','State_master','City_master','Language','Group','UserGroup','Charity','Charityuser','Usernote','Institution_list','users_professional_tracks');
        var $components = array('Usercomponent', 'Session','Image','Email','RequestHandler');
        var $helpers = array('Html', 'Form','Javascript','Ajax');
        var $layout = 'default';

         function friendlist(){
             $this->checkuserlog();

             //FINDING REFERRALS OF USER - STARTS
             $userID =$this->Session->read('User.id');
             $colectRef = $this->Userprofile->find('all',array('conditions'=>array('Userprofile.referrals'=>$userID)));
             $this->set('colectRef',$colectRef);

             //FINDING CHARITY PARTNERS - STARTS
             $charityID = $this->Charityuser->find('all',array('conditions'=>'uid ='.$userID));
            
             if(!empty($charityID)) {
             $charityPartners = $this->Charityuser->find('all',array('conditions'=>"uid !=".$userID . " AND charity_id =".$charityID[0]['Charityuser']['charity_id']));
             

             $this->set('charityPartners',$charityPartners);
             }

             $selectGroupsQry = $this->Group->find('all',array('conditions'=>array('Group.uid'=>$this->Session->read('User.id'))));
             $this->set('groupList',$selectGroupsQry);
             $hobbiesArr = array('Photography','Business','Cooking','Fashion','Health & Fitness','Technology','Motor Sports','History','Music','Travel','Gaming','Art');
             $this->set('hobbies',$hobbiesArr);

             foreach (range(0, 60) as $to) {
                $tolist[$to] = $to;
             }
            $this->set('toOptions',$tolist);

            foreach (range(10, 60) as $to1) {
                $to1list[$to1] = $to1;
            }
            $this->set('to1Options',$to1list);

            $gender = array('M'=>'Male','F'=>'Female');
            $this->set('gender',$gender);

            $languages = $this->Language->find('list',array('conditions' => null,
                                                'order' => 'Language.name ASC',
                                                'limit' => null,
                                                'fields' => 'Language.name', 'Language.id'));
            $this->set('languageList',$languages);

            $institution = $this->Institution_list->find('list',array('conditions' => null,
                                                'order' => 'name ASC',
                                                'limit' => null,
                                                'fields' => 'name', 'id'));
            $this->set('institutionList',$institution);
            
            $tracks = $this->users_professional_tracks->find('list',array('conditions' => null,
                                                'order' => 'id ASC',
                                                'limit' => null,
                                                'fields' => 'professional_track', 'id'));
            $this->set('tracksList',$tracks);

            $countries = $this->Country_master->find('list',array('conditions' => array('Country_master.status =' => '1'),
                                                'order' => 'Country_master.country_name ASC',
                                                'limit' => null,
                                                'fields' => 'Country_master.country_name', 'Country_master.id'));
            $this->set('countryList',$countries);


            $resultNoteQry = $this->Usernote->find('all',array('conditions'=>array('Usernote.from_id ='=>$this->Session->read('User.id')),'fields'=>array('Usernote.to_id') ));
            $notesNameId=array();
            if(count($resultNoteQry)>0) {
               for($i=0; $i<count($resultNoteQry); $i++) {
                   array_push($notesNameId, $resultNoteQry[$i]['Usernote']['to_id']);
                }
            }
            $this->set('noteUserArray',$notesNameId);
//             $resultQry = $this->User->find('all',array('conditions'=>array('User.block'=>1, 'User.id !='=>$this->Session->read('User.id')),'fields'=>array('User.id', 'Userprofile.first_name', 'Userprofile.last_name','Userprofile.image','Userprofile.user_id') ));
            $this->User->find('all',array('fields'=>array('User.id', 'Userprofile.first_name', 'Userprofile.last_name','Userprofile.image','Userprofile.user_id')));
	    $this->paginate = array(
                                    'conditions'=>array('User.block'=>1, 'User.id !='=>$this->Session->read('User.id')),
                                    'limit' => 80
   				  );
	    $resultQry = $this->paginate('User');
            
            $this->set('result',$resultQry);
         }

         function lists($id=NULL) {
              $this->checkuserlog();
              $userID =$this->Session->read('User.id');
               //FINDING REFERRALS OF USER - STARTS
             $userID =$this->Session->read('User.id');
             $colectRef = $this->Userprofile->find('all',array('conditions'=>array('Userprofile.referrals'=>$userID)));
             $this->set('colectRef',$colectRef);

             //FINDING CHARITY PARTNERS - STARTS
             $charityID = $this->Charityuser->find('all',array('conditions'=>'uid ='.$userID));
             $charityPartners = $this->Charityuser->find('all',array('conditions'=>"uid !=".$userID . " AND charity_id =".$charityID[0]['Charityuser']['charity_id']));
             $this->set('charityPartners',$charityPartners);

             $selectGroupsQry = $this->Group->find('all',array('conditions'=>array('Group.uid'=>$this->Session->read('User.id'))));
             $this->set('groupList',$selectGroupsQry);
             $hobbiesArr = array('Photography','Business','Cooking','Fashion','Health & Fitness','Technology','Motor Sports','History','Music','Travel','Gaming','Art');
             $this->set('hobbies',$hobbiesArr);

             foreach (range(0, 60) as $to) {
                $tolist[$to] = $to;
             }
            $this->set('toOptions',$tolist);

            foreach (range(10, 60) as $to1) {
                $to1list[$to1] = $to1;
            }
            $this->set('to1Options',$to1list);

            $gender = array('M'=>'Male','F'=>'Female');
            $this->set('gender',$gender);

            $languages = $this->Language->find('list',array('conditions' => null,
                                                'order' => 'Language.name ASC',
                                                'limit' => null,
                                                'fields' => 'Language.name', 'Language.id'));
            $this->set('languageList',$languages);

            $institution = $this->Institution_list->find('list',array('conditions' => null,
                                                'order' => 'name ASC',
                                                'limit' => null,
                                                'fields' => 'name', 'id'));
            $this->set('institutionList',$institution);

            $tracks = $this->users_professional_tracks->find('list',array('conditions' => null,
                                                'order' => 'id ASC',
                                                'limit' => null,
                                                'fields' => 'professional_track', 'id'));
            $this->set('tracksList',$tracks);

            $countries = $this->Country_master->find('list',array('conditions' => array('Country_master.status =' => '1'),
                                                'order' => 'Country_master.country_name ASC',
                                                'limit' => null,
                                                'fields' => 'Country_master.country_name', 'Country_master.id'));
            $this->set('countryList',$countries);

            $groupUserQry = $this->UserGroup->find('all',array('conditions'=>array('UserGroup.uid'=>$this->Session->read('User.id'),'UserGroup.group_id'=>$id)));

                if(!empty($groupUserQry)) {
                    for($q=0; $q<count($groupUserQry); $q++) {
                        $groupUserArr[]=$groupUserQry[$q]['UserGroup']['user_gid'];
                    }
                    $groupUserIds=implode(',',$groupUserArr);
                    $resultQry = $this->User->find('all',array('conditions'=>array('User.id IN ('.$groupUserIds.')') , 'fields'=>array('User.id', 'Userprofile.first_name', 'Userprofile.last_name','Userprofile.image','Userprofile.user_id')));
                    $this->set('result',$resultQry);
                    $this->set('lid',$id);
                }

             $selGroupsDet = $this->Group->find('all',array('conditions'=>array('Group.id'=>$id)));
             $this->set('groupDet',$selGroupsDet[0]);
            $resultNoteQry = $this->Usernote->find('all',array('conditions'=>array('Usernote.from_id ='=>$this->Session->read('User.id')),'fields'=>array('Usernote.to_id') ));
            $notesNameId=array();
            if(count($resultNoteQry)>0) {
               for($i=0; $i<count($resultNoteQry); $i++) {
                   array_push($notesNameId, $resultNoteQry[$i]['Usernote']['to_id']);
                }
            }
            $this->set('noteUserArray',$notesNameId);
          
         }

         function addgroup() {
             $this->checkuserlog();
             $this->autoRender=false;
              if($this->RequestHandler->isAjax()){
                Configure::write('debug', 0);
                //echo '<pre>'; print_r($this->data); echo '</pre>';
                $this->data['Group']['uid']=$this->Session->read('User.id');
                $this->data['Group']['datetime']=date('Y-m-d H:i:s');
                $chkLabelName = $this->Group->find('count', array('conditions'=>array('uid'=>$this->Session->read('User.id'), 'groupname'=>$this->data['Group']['groupname'])));
                if($chkLabelName==0) {
                    $this->Group->save($this->data);
                    $lastId = $this->Group->getLastInsertID();
                    $arr['name'] =  $this->data['Group']['groupname'];
                    $arr['id'] = $lastId;
                    header('Content-type: application/json');
                    echo json_encode($arr);
                } else {
                    echo 'The label name you have chosen already exists. Please try another name';
                }
              }
         }

         function removegroup(){
              $this->checkuserlog();
             $this->autoRender=false;
              if($this->RequestHandler->isAjax()){
                Configure::write('debug', 2);
               // echo '<pre>'; print_r($_POST); echo '</pre>';
               // 
                $this->Group->delete($id = $_POST['id'], $cascade = true);
                echo $_POST['id'];

              }
         }

         function search() {
             $this->checkuserlog();
             $this->layout='';
            $this->autoRender=true;
            if($this->RequestHandler->isAjax()){
             Configure::write('debug', 2);
            $resultNoteQry = $this->Usernote->find('all',array('conditions'=>array('Usernote.from_id ='=>$this->Session->read('User.id')),'fields'=>array('Usernote.to_id') ));
            $notesNameId=array();
            if(count($resultNoteQry)>0) {
               for($i=0; $i<count($resultNoteQry); $i++) {
                   array_push($notesNameId, $resultNoteQry[$i]['Usernote']['to_id']);
                }
            }
            $this->set('noteUserArray',$notesNameId);

                 if(!empty($this->data)){
                    $userID = $this->Session->read('User.id');
                         $cond = 'Userprofile.user_id !='. $userID;
                         if($this->data['Search']['name']!='') {
                             $name = strtolower($this->data['Search']['name']);
                             $cond .= " AND (LOWER(Userprofile.first_name) LIKE '".$name."%' OR LOWER(Userprofile.last_name) LIKE '".$name."%')";
                         }
                         if($this->data['Search']['study']!='') {
                             $cond .= " AND (Userprofile.learnlanguage=".$this->data['Search']['study'] . " OR  Userprofile.learnlanguage2=".$this->data['Search']['study'] . " OR Userprofile.learnlanguage3=".$this->data['Search']['study'].")";
                         }
                         if($this->data['Search']['country']==40) {
                             if($this->data['Search']['country']!='') {
                                 $cond .= " AND Userprofile.country=".$this->data['Search']['country'];
                             }
                             if($this->data['Search']['state']!='') {
                                $cond .= " AND Userprofile.state=".$this->data['Search']['state'];
                             }
                             if($this->data['Search']['city']!='') {
                                $cond .= " AND Userprofile.city=".$this->data['Search']['city'];
                             }
                         } else {
                             if($this->data['Search']['country']!='') {
                                 $cond .= " AND Userprofile.country=".$this->data['Search']['country'];
                             }
                             if($this->data['Search']['state']!='') {
                                $cond .= " AND Userprofile.state=".$this->data['Search']['ostate'];
                             }
                             if($this->data['Search']['city']!='') {
                                $cond .= " AND Userprofile.city=".$this->data['Search']['ocity'];
                            }
                         }
                         if($this->data['Search']['gender']!=''){
                         if($this->data['Search']['gender']=='F') {
                             $mrs = "Mrs."; $ms = "Ms.";
                                $cond .= " AND (Userprofile.title='$mrs' OR Userprofile.title='$ms')";
                         } else{
                             $mr = "Mr.";
                                $cond .= " AND Userprofile.title='$mr' ";
                         }
                         }
                         if($this->data['Search']['age1']!='' && $this->data['Search']['age2']!='') {
                                $cond .= " AND (EXTRACT(YEAR FROM(FROM_DAYS(DATEDIFF(NOW(), Userprofile.dob))))+0 BETWEEN ".$this->data['Search']['age1']. " AND " . $this->data['Search']['age2'].")";
                         }

                         if($this->data['Search']['institution']!='') {
                             $cond .= " AND (Userprofile.institute=".$this->data['Search']['institution'].")";
                         }

                         if($this->data['Search']['track']!='') {
                             $cond .= " AND (Userprofile.professional_track=".$this->data['Search']['track'].")";
                         }
                         
                         if($this->data['Search']['gyear']!='') {
                             $cond .= " AND (Userprofile.passing_year=".$this->data['Search']['gyear'].")";
                         }

                         if(!empty($this->data['Search']['hobby'])) {
                             $hobby = implode('|',$this->data['Search']['hobby']);
                             $cond .= " AND Userprofile.hobby regexp '[" .$hobby. "]'";
                         }
                         $cond .= " AND User.block=1";
                         $resultQry = $this->User->find('all',array('conditions'=>$cond));
                         $this->set('result',$resultQry);
                 }
         }
        }

        function statelist(){
            if($this->RequestHandler->isAjax()){
            Configure::write('debug', 0);
            $stateList = $this->State_master->find('list',array('conditions' => array('State_master.status' => '1', 'State_master.country_id'=>$_POST['id']),
                                                'order' => 'State_master.state_name ASC',
                                                'limit' => null,
                                                'fields' => 'State_master.state_name', 'State_master.id'));
            $this->set('stateList',$stateList);
            }
        }
        function citylist(){
            if($this->RequestHandler->isAjax()){
            Configure::write('debug', 0);
            $cityList = $this->City_master->find('list',array('conditions' => array('City_master.status' => '1',  'City_master.state_id'=>$_POST['id']),
                                                            'order' => 'City_master.city_name ASC',
                                                            'limit' => null,
                                                            'fields' => 'City_master.city_name', 'City_master.id'));
            $this->set('cityList',$cityList);
            }

        }

        function ajaxcontent(){
            $this->autoRender=false;
            if($this->RequestHandler->isAjax()){
            Configure::write('debug', 0);
            $this->layout='';
           // echo print_r($_POST);
            }
        }

        function addlist(){
            $this->checkuserlog();
            $this->autoRender=true;
            if($this->RequestHandler->isAjax()){
            Configure::write('debug', 0);
            $this->layout='';
            $this->Group->unbindmodel(array('hasMany' => array('UserGroup')));
            $results=$this->Group->find('all',array('conditions'=>array('uid'=>$this->Session->read('User.id'))));
            $this->set('result',$results);
            $this->set('friendids', $_POST);
            
            }
        }

        function savelist() {
             $this->checkuserlog();
            $this->autoRender=false;
            if($this->RequestHandler->isAjax()){
            Configure::write('debug', 0);
            $this->layout='';
            //print_r($_POST);
            //echo "==".$this->data['UserGroup']['fid'];
            if(!strstr($_POST['groupfid'],',')) {
                $this->data['UserGroup']['uid']= $this->Session->read('User.id');
                $this->data['UserGroup']['group_id']=$_POST['groupid'];
                 $this->data['UserGroup']['user_gid']=$_POST['groupfid'];
                 $this->data['UserGroup']['pending_status']=0;
                 $this->data['UserGroup']['datetime']=date('Y-m-d H:i:s');
                 $chkRecordExist = $this->UserGroup->find('count', array('conditions'=>array('uid'=>$this->Session->read('User.id'), 'group_id'=>$_POST['groupid'],'user_gid'=>$_POST['groupfid'])));
                 if($chkRecordExist==0) {
                    $this->UserGroup->save($this->data);
                 }
            } else {
                $arr = explode(',',$_POST['groupfid']);
                for($k=0; $k<count($arr); $k++) {
                 $this->data['UserGroup']['uid']= $this->Session->read('User.id');
                 $this->data['UserGroup']['group_id']=$_POST['groupid'];
                 $this->data['UserGroup']['user_gid']=$arr[$k];
                 $this->data['UserGroup']['pending_status']=0;
                 $this->data['UserGroup']['datetime']=date('Y-m-d H:i:s');
                  $chkRecordExist = $this->UserGroup->find('count', array('conditions'=>array('uid'=>$this->Session->read('User.id'), 'group_id'=>$_POST['groupid'],'user_gid'=>$arr[$k])));
                  if($chkRecordExist==0) {
                    $this->UserGroup->create();
                    $this->UserGroup->save($this->data);
                  }
                }
            
            }
            }
        }

        function deletelist() {
             $this->checkuserlog();
            $this->autoRender=false;
            if($this->RequestHandler->isAjax()){
            Configure::write('debug', 0);
            $this->layout='';
            if(!empty($_POST)) {
             for($i=0; $i<count($_POST['rchk']); $i++) {
                   $this->UserGroup->deleteAll(array('user_gid'=>$_POST['rchk'][$i],'group_id'=>$_POST['data']['removelist']['listid'],'uid'=>$this->Session->read('User.id')));
                }
             header('Content-type: application/json');
             echo json_encode($_POST['rchk']);
            }
             
            
         }
        }

  function viewdetail($id=null){
   // $this->checkuserlogin();
    $institutionName='';
    $passing_year = '';
    $professionalName='';
    $this->layout = '';
    $results=$this->User->find('all',array('conditions'=>array('MD5(User.id)' =>$id)));
    $this->set('result',$results[0]);
    $resultNote=$this->Usernote->find('all',array('conditions'=>array('MD5(Usernote.to_id)' =>$id,'from_id'=> $this->Session->read('User.id'))));
	if(!empty($resultNote)){
		$this->set('resnote',$resultNote);
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

$hobbiesArr = array('Photography','Business','Cooking','Fashion','Health & Fitness','Technology','Motor Sports','History','Music','Travel','Gaming','Art');
$this->set('hobbies',$hobbiesArr);

$proficiencyList = array('1'=>'Beginner','2'=>'Intermediate','3'=>'Expert');
$this->set('proficienyList',$proficiencyList);

if($results[0]['Userprofile']['institute']!=''){
            $resultInstitute = $this->Institution_list->find('all',array('conditions'=>'id ='.$results[0]['Userprofile']['institute']));
            $institutionName = $resultInstitute[0]['Institution_list']['name'];
        }
if($results[0]['Userprofile']['professional_track']!=''){
    $resultProfessional = $this->users_professional_tracks->find('all',array('conditions'=>'id ='.$results[0]['Userprofile']['professional_track']));
    $professionalName = $resultProfessional[0]['users_professional_tracks']['professional_track'];
}

if($results[0]['Userprofile']['passing_year']!='0'){
 $passing_year = $results[0]['Userprofile']['passing_year'];
}
$this->set('passing_year',$passing_year);
$this->set('institutionName',$institutionName);
$this->set('professionalName',$professionalName);
}

  function viewmembers($id=null){
    $this->checkuserlogin();
    $result= $this->UserGroup->find('all',array('conditions'=>array('MD5(UserGroup.group_id)' =>$id)));
    $membersData=array();
    for($i=0;$i<count($result);$i++){
        $contentDetails = $this->Userprofile->query("SELECT first_name, last_name, user_id, image FROM `userprofiles` where user_id =".$result[$i]['UserGroup']['user_gid']);
        $membersData[$i]['name']=$contentDetails[0]['userprofiles']['first_name'].' '.$contentDetails[0]['userprofiles']['last_name'];
        $membersData[$i]['id']=$contentDetails[0]['userprofiles']['user_id'];
        $membersData[$i]['image']=$contentDetails[0]['userprofiles']['image'];
    }
    $this->set('membersData',$membersData);
    $selectGroupsQry = $this->Group->find('all',array('conditions'=>array('MD5(Group.id)' =>$id)));
    $username = $this->Userprofile->query("SELECT first_name, last_name FROM `userprofiles` where user_id =".$selectGroupsQry[0]['Group']['uid']);
    $group_owner= $username[0]['userprofiles']['first_name'].' '.$username[0]['userprofiles']['last_name'];
    $this->set('groupDetails',$selectGroupsQry[0]['Group']['groupname'].' ('.$group_owner.')');
}
 function message($id=NULL){
    $this->checkuserlog();
    $this->layout = '';
    $results=$this->User->find('all',array('conditions'=>array('MD5(User.id)' =>$id),
                                           'fields'=>array('User.id','Userprofile.first_name','Userprofile.last_name')));
    $this->set('result',$results[0]);
}

 function sendmsg() {
             $this->checkuserlog();
            $this->autoRender=false;
            if($this->RequestHandler->isAjax()){
            Configure::write('debug', 0);
            $this->layout='';

            $user_id = $this->Session->read('User.id');
            $subject =  'Message From ' . $this->UserFirstName($user_id);


            $sent['Sent']['from_id'] = $user_id;
            $sent['Sent']['to_id'] = $_POST['data']['Message']['uid'];
            $sent['Sent']['subject'] = $subject;
            $sent['Sent']['message'] = $_POST['data']['Message']['msg'];
            $sent['Sent']['status'] = 0;
            $sent['Sent']['datetime'] = date('Y-m-d H:i:s');
            $this->Sent->create();
            $this->Sent->save($sent);

            $inbox['Inbox']['from_id'] = $user_id;
            $inbox['Inbox']['to_id'] = $_POST['data']['Message']['uid'];
            $inbox['Inbox']['subject']= $subject;
            $inbox['Inbox']['message']= $_POST['data']['Message']['msg'];;
            $inbox['Inbox']['status']= 0;
            $inbox['Inbox']['datetime']= date('Y-m-d H:i:s');
            $this->Inbox->create();
            $this->Inbox->save($inbox);

            echo 'Message has been sent successfully';
       }
 }

 function charitylists() {
 
     $this->checkuserlog();

             //FINDING REFERRALS OF USER - STARTS
             $userID =$this->Session->read('User.id');
             $colectRef = $this->Userprofile->find('all',array('conditions'=>array('Userprofile.referrals'=>$userID)));
             $this->set('colectRef',$colectRef);

             //FINDING CHARITY PARTNERS - STARTS
             $charityID = $this->Charityuser->find('all',array('conditions'=>'uid ='.$userID));
             if(!empty($charityID)) {
             $charityPartners = $this->Charityuser->find('all',array('conditions'=>"uid !=".$userID . " AND charity_id =".$charityID[0]['Charityuser']['charity_id']));
             $this->set('charityPartners',$charityPartners);
             }

             $selectGroupsQry = $this->Group->find('all',array('conditions'=>array('Group.uid'=>$this->Session->read('User.id'))));
             $this->set('groupList',$selectGroupsQry);
             $hobbiesArr = array('Photography','Business','Cooking','Fashion','Health & Fitness','Technology','Motor Sports','History','Music','Travel','Gaming','Art');
             $this->set('hobbies',$hobbiesArr);

             foreach (range(0, 60) as $to) {
                $tolist[$to] = $to;
             }
            $this->set('toOptions',$tolist);

            foreach (range(10, 60) as $to1) {
                $to1list[$to1] = $to1;
            }
            $this->set('to1Options',$to1list);

            $gender = array('M'=>'Male','F'=>'Female');
            $this->set('gender',$gender);

            $languages = $this->Language->find('list',array('conditions' => null,
                                                'order' => 'Language.name ASC',
                                                'limit' => null,
                                                'fields' => 'Language.name', 'Language.id'));
            $this->set('languageList',$languages);

            $institution = $this->Institution_list->find('list',array('conditions' => null,
                                                'order' => 'name ASC',
                                                'limit' => null,
                                                'fields' => 'name', 'id'));
            $this->set('institutionList',$institution);

            $tracks = $this->users_professional_tracks->find('list',array('conditions' => null,
                                                'order' => 'id ASC',
                                                'limit' => null,
                                                'fields' => 'professional_track', 'id'));
            $this->set('tracksList',$tracks);

            $countries = $this->Country_master->find('list',array('conditions' => array('Country_master.status =' => '1'),
                                                'order' => 'Country_master.country_name ASC',
                                                'limit' => null,
                                                'fields' => 'Country_master.country_name', 'Country_master.id'));
            $this->set('countryList',$countries);

            $chrId = $this->Charityuser->find('all',array('fields'=>'charity_id','conditions'=>'uid='.$this->Session->read('User.id')));
           $resultQry=$this->Charityuser->find('all',array('fields'=>'uid','conditions'=>'uid <>'.$this->Session->read('User.id').' and charity_id='.$chrId[0]['Charityuser']['charity_id']));
            
            $this->set('result',$resultQry);

			$charName= $this->Charity->find('all',array('fields'=>'title','conditions'=>'id='.$chrId[0]['Charityuser']['charity_id']));
             $this->set('charName',$charName[0]['Charity']['title']);

             $resultNoteQry = $this->Usernote->find('all',array('conditions'=>array('Usernote.from_id ='=>$this->Session->read('User.id')),'fields'=>array('Usernote.to_id') ));
            $notesNameId=array();
            if(count($resultNoteQry)>0) {
               for($i=0; $i<count($resultNoteQry); $i++) {
                   array_push($notesNameId, $resultNoteQry[$i]['Usernote']['to_id']);
                }
            }
            $this->set('noteUserArray',$notesNameId);

 }

 function reflists(){
 	
 	 //FINDING REFERRALS OF USER - STARTS
             $userID =$this->Session->read('User.id');
             $colectRef = $this->Userprofile->find('all',array('conditions'=>array('Userprofile.referrals'=>$userID)));
             $this->set('colectRef',$colectRef);

             //FINDING CHARITY PARTNERS - STARTS
             $charityID = $this->Charityuser->find('all',array('conditions'=>'uid ='.$userID));
             if(!empty($charityID)) {
             $charityPartners = $this->Charityuser->find('all',array('conditions'=>"uid !=".$userID . " AND charity_id =".$charityID[0]['Charityuser']['charity_id']));
             $this->set('charityPartners',$charityPartners);
             }

             $selectGroupsQry = $this->Group->find('all',array('conditions'=>array('Group.uid'=>$this->Session->read('User.id'))));
             $this->set('groupList',$selectGroupsQry);
             $hobbiesArr = array('Photography','Business','Cooking','Fashion','Health & Fitness','Technology','Motor Sports','History','Music','Travel','Gaming','Art');
             $this->set('hobbies',$hobbiesArr);

             foreach (range(0, 60) as $to) {
                $tolist[$to] = $to;
             }
            $this->set('toOptions',$tolist);

            foreach (range(10, 60) as $to1) {
                $to1list[$to1] = $to1;
            }
            $this->set('to1Options',$to1list);

            $gender = array('M'=>'Male','F'=>'Female');
            $this->set('gender',$gender);

            $languages = $this->Language->find('list',array('conditions' => null,
                                                'order' => 'Language.name ASC',
                                                'limit' => null,
                                                'fields' => 'Language.name', 'Language.id'));
            $this->set('languageList',$languages);

            $institution = $this->Institution_list->find('list',array('conditions' => null,
                                                'order' => 'name ASC',
                                                'limit' => null,
                                                'fields' => 'name', 'id'));
            $this->set('institutionList',$institution);

            $tracks = $this->users_professional_tracks->find('list',array('conditions' => null,
                                                'order' => 'id ASC',
                                                'limit' => null,
                                                'fields' => 'professional_track', 'id'));
            $this->set('tracksList',$tracks);

            $countries = $this->Country_master->find('list',array('conditions' => array('Country_master.status =' => '1'),
                                                'order' => 'Country_master.country_name ASC',
                                                'limit' => null,
                                                'fields' => 'Country_master.country_name', 'Country_master.id'));
            $this->set('countryList',$countries);

            $resultQry = $this->User->find('all',array('conditions'=>array('Userprofile.referrals ='=>$this->Session->read('User.id')),'fields'=>array('User.id', 'Userprofile.first_name', 'Userprofile.last_name','Userprofile.image','Userprofile.user_id') ));
            $this->set('result',$resultQry);
            
            $resultNoteQry = $this->Usernote->find('all',array('conditions'=>array('Usernote.from_id ='=>$this->Session->read('User.id')),'fields'=>array('Usernote.to_id') ));
            $notesNameId=array();
            if(count($resultNoteQry)>0) {
               for($i=0; $i<count($resultNoteQry); $i++) {
                   array_push($notesNameId, $resultNoteQry[$i]['Usernote']['to_id']);
                }
            }
            $this->set('noteUserArray',$notesNameId);

			
 	
     
 }
 
 function usernote($id=NULL){
 			$this->layout='';
 			$this->set('uid',$id);
            
}
function editusernote($id=NULL){
 			$this->layout='';
 			$result = $this->Usernote->find('all',array('conditions'=>'to_id='.$id.' and from_id='.$this->Session->read('User.id')));
 			$this->set('result',$result);
            
}
function saveeditusernote(){

            $this->autoRender=false;
            if($this->RequestHandler->isAjax()){
             Configure::write('debug', 0);
           	 $this->Usernote->save($this->data); 
          }
}
function sendusernote(){

            $this->autoRender=false;
            if($this->RequestHandler->isAjax()){
             Configure::write('debug', 0);
           	 $this->Usernote->save($this->data); 
          }
}
function deleteusernote($id=NULL){
			$userId = $this->Session->read("User.id");
            $this->autoRender=false;
            if($this->RequestHandler->isAjax()){
            Configure::write('debug', 0);
           	$this->Usernote->query('delete from user_notes where to_id='.$id.' and from_id='.$userId );
           	echo "<script>$('#TB_closeWindowButton').click();</script>";
          }
}
function editgroupname($id=NULL){
 			$this->layout='';
 			$result = $this->Group->find('all',array('conditions'=>'id='.$id));
 			$this->set('result',$result);

}
function saveeditgroupname(){

            $this->autoRender=false;
            if($this->RequestHandler->isAjax()){
             Configure::write('debug', 0);
           	 $this->Group->save($this->data);
          }
}
function wheream(){
	 //FINDING REFERRALS OF USER - STARTS
             $userID =$this->Session->read('User.id');
             $colectRef = $this->Userprofile->find('all',array('conditions'=>array('Userprofile.referrals'=>$userID)));
             $this->set('colectRef',$colectRef);

             //FINDING CHARITY PARTNERS - STARTS
             $charityID = $this->Charityuser->find('all',array('conditions'=>'uid ='.$userID));
             if(!empty($charityID)) {
             $charityPartners = $this->Charityuser->find('all',array('conditions'=>"uid !=".$userID . " AND charity_id =".$charityID[0]['Charityuser']['charity_id']));
             $this->set('charityPartners',$charityPartners);
             }

             $selectGroupsQry = $this->Group->find('all',array('conditions'=>array('Group.uid'=>$this->Session->read('User.id'))));
             $this->set('groupList',$selectGroupsQry);
             $hobbiesArr = array('Photography','Business','Cooking','Fashion','Health & Fitness','Technology','Motor Sports','History','Music','Travel','Gaming','Art');
             $this->set('hobbies',$hobbiesArr);

             foreach (range(0, 60) as $to) {
                $tolist[$to] = $to;
             }
            $this->set('toOptions',$tolist);

            foreach (range(10, 60) as $to1) {
                $to1list[$to1] = $to1;
            }
            $this->set('to1Options',$to1list);

            $gender = array('M'=>'Male','F'=>'Female');
            $this->set('gender',$gender);

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

            $institution = $this->Institution_list->find('list',array('conditions' => null,
                                                'order' => 'name ASC',
                                                'limit' => null,
                                                'fields' => 'name', 'id'));
            $this->set('institutionList',$institution);

            $tracks = $this->users_professional_tracks->find('list',array('conditions' => null,
                                                'order' => 'id ASC',
                                                'limit' => null,
                                                'fields' => 'professional_track', 'id'));
            $this->set('tracksList',$tracks);

            $result= $this->UserGroup->find('all',array('conditions'=>'user_gid='.$userID));
			$this->set('result',$result);
			
			
	
}
function unlinkgroup($id=NULL){
	$this->UserGroup->delete($id);
	$this->redirect('wheream');
	
}
function addlistuser($id=NULL){
	$this->layout='';
	$this->set('gruid',$id);
	        $this->Group->unbindmodel(array('hasMany' => array('UserGroup')));
            $results=$this->Group->find('all',array('conditions'=>array('uid'=>$this->Session->read('User.id'))));
            $this->set('result',$results);
            $this->set('friendids', $_POST);
}
 function savelistuser() {
            $this->checkuserlog();
            $this->autoRender=false;
            if($this->RequestHandler->isAjax()){
	            Configure::write('debug', 0);
	            $dt = date('Y-m-d H:i:s');
	            $str ='insert into user_groups (uid,group_id,user_gid,datetime) values ('.$this->Session->read('User.id').','.$this->data['Usergroup']['group_id'].','.$this->data['Usergroup']['user_gid'].',"'.$dt.'" )';
                $chkRecordExist = $this->UserGroup->find('count', array('conditions'=>array('uid'=>$this->Session->read('User.id'), 'group_id'=>$this->data['Usergroup']['group_id'],'user_gid'=>$this->data['Usergroup']['user_gid'])));
                 if($chkRecordExist==0) {
                    $this->UserGroup->query($str);
                 }
	      }
 }
}
?>