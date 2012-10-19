<?php
/*
#################################################################################
#																				#
#   Student Controller 															#
#   file name        	: reports_controller.php								#
#   Developed        	: Meridian Radiology 									#
#   Intialised Variables: name , helpers ,components , uses , layout = (default)#
#																				#
#																				#
#################################################################################
*/


class ReportsController extends AppController {

    	var $name = 'Reports';
        var $uses = array('Challenge','Challengetemp','Challengeuserattempt','Featureuserattempt','User','Userprofile','Testimonial','Poll','Pollanswer','Staticcontent','Polluserattempt','Language','Country_master','State_master','City_master','Ediv_user_master','user_winning_points','language_averages','Quizuserattempt','Feature','Quizcat','Charity','Charityuser','Charityuserfriend','Edivuseramount');
        var $components = array('Usercomponent', 'Session','Image','Email','RequestHandler');
        var $helpers = array('Html', 'Form','Javascript','Ajax','Fck');
    	var $layout = 'admin';

function reportlist(){
    $this->checkadmin();
   
}
/*___________________________________________________________________________________________________
* 
* Method     : registration-cancellation
* Purpose    : Report Generation 
* Parameters : None
* 
* ___________________________________________________________________________________________________
*/	
function registration_cancellation(){
	$this->checkadmin();
	$flg=0;
	if(!empty($this->data)){
		//pr($this->params);
		$dt = date('Y-m-d');
		$cond = array();
		if($this->data['Reports']['type']=='C'){
		switch ($this->data['Reports']['criteria']) {
			    case 'daily':
			    	$cond = array('User.block=0 and DATE_FORMAT(User.block_date ,"%Y-%m-%d")="'.$dt.'"');
			        break;
			    case 'week':
			    	
			    	if($this->params['form']['startDate']!='' && $this->params['form']['endDate']!=''){
			        $cond = array('User.block=0 AND DATE_FORMAT(User.block_date ,"%m/%d/%Y") BETWEEN "'.$this->params['form']['startDate'].'" AND "'.$this->params['form']['endDate'].'"');
			    	}else{
			    		$flg =1;
			    	}
			        break;
			    case 'month':
			    	$month=$this->data['Reports']['month'];
			    	$monthmatch = date('Y').'-'.$month;
			    	$cond = array('User.block=0 AND DATE_FORMAT(User.block_date ,"%Y-%m")="'.$monthmatch.'"');
			        break;
				case 'year':
					$year = $month=$this->data['Reports']['year'];
			        $cond = array('User.block=0 AND DATE_FORMAT(User.block_date ,"%Y")="'.$year.'"');
			        break;
		   }
		
		}elseif($this->data['Reports']['type']=='R'){
			switch ($this->data['Reports']['criteria']) {
			    case 'daily':
			    	$cond = array('DATE_FORMAT(User.rigistration_date ,"%Y-%m-%d")="'.$dt.'"');
			        break;
			    case 'week':
			    	if($this->params['form']['startDate']!='' && $this->params['form']['endDate']!=''){
			        $cond = array('DATE_FORMAT(User.rigistration_date ,"%m/%d/%Y") BETWEEN "'.$this->params['form']['startDate'].'" AND "'.$this->params['form']['endDate'].'"');
			    	}else{
			    	 $flg =1;	
			    	}
			        break;
			    case 'month':
			    	$month=$this->data['Reports']['month'];
			    	$monthmatch = date('Y').'-'.$month;
			    	$cond = array('DATE_FORMAT(User.rigistration_date ,"%Y-%m")="'.$monthmatch.'"');
			        break;
				case 'year':
					$year = $month=$this->data['Reports']['year'];
			        $cond = array('DATE_FORMAT(User.rigistration_date ,"%Y")="'.$year.'"');
			        break;
		   }
		}
		if($flg==0){
		$result = $this->User->find('all',array('conditions'=>$cond));
		$this->set('result',$result);
		}elseif($flg == 1){
			$this->Session->setFlash('Please select the week from calender');
		}
		
		
	}
	
}
/*___________________________________________________________________________________________________
* 
* Method     : referrals_by_registrant
* Purpose    : New Reward 
* Parameters : None
* 
* ___________________________________________________________________________________________________
*/
function referrals_by_registrant(){
	$this->checkadmin();
	$flg=0;
	$cond=array();
	$dt = date('Y-m-d');
/////////////// list country //
	$countries = $this->Country_master->find('list',array('conditions' => array('Country_master.status =' => '1'),
                                                'order' => 'Country_master.country_name ASC',
                                                'limit' => null,
                                                'fields' => 'Country_master.country_name', 'Country_master.id'));
	
	$this->set('countryList',$countries);
    if(!empty($this->data)){
    	
    switch ($this->data['Reports']['criteria']) {
			    case 'daily':
			    	if($this->data['Reports']['country']==40){
			    		if($this->data['Reports']['state']!=0 && $this->data['Reports']['city']==0){
			    			$cond = array('Userprofile.state='.$this->data['Reports']['state'].' And Userprofile.country='.$this->data['Reports']['country'].' And Userprofile.referrals IS NOT NULL And DATE_FORMAT(User.rigistration_date ,"%Y-%m-%d")="'.$dt.'"');
			    		}
			    	    elseif($this->data['Reports']['city']!=0 && $this->data['Reports']['state']==0){
			    			$cond = array('Userprofile.city='.$this->data['Reports']['city'].' And Userprofile.country='.$this->data['Reports']['country'].' And Userprofile.referrals IS NOT NULL And DATE_FORMAT(User.rigistration_date ,"%Y-%m-%d")="'.$dt.'"');
			    		}
			    	   elseif($this->data['Reports']['city']!=0 && $this->data['Reports']['state']!=0){
			    			$cond = array('Userprofile.state='.$this->data['Reports']['state'].' And Userprofile.city='.$this->data['Reports']['city'].' And Userprofile.country='.$this->data['Reports']['country'].' And Userprofile.referrals IS NOT NULL And DATE_FORMAT(User.rigistration_date ,"%Y-%m-%d")="'.$dt.'"');
			    		}
			    		elseif($this->data['Reports']['city']==0 && $this->data['Reports']['state']==0){
			    			$cond = array('Userprofile.country='.$this->data['Reports']['country'].' And Userprofile.referrals IS NOT NULL And DATE_FORMAT(User.rigistration_date ,"%Y-%m-%d")="'.$dt.'"');
			    		}
			    	}else{
			    	if($this->data['Reports']['otherstate']!='' && $this->data['Reports']['othercity']==''){
			    			$cond = array('Userprofile.otherstate="'.$this->data['Reports']['otherstate'].'" And Userprofile.country='.$this->data['Reports']['country'].' And Userprofile.referrals IS NOT NULL And DATE_FORMAT(User.rigistration_date ,"%Y-%m-%d")="'.$dt.'"');
			    		}
			    	    elseif($this->data['Reports']['othercity']!='' && $this->data['Reports']['otherstate']==''){
			    			$cond = array('Userprofile.othercity="'.$this->data['Reports']['othercity'].'" And Userprofile.country='.$this->data['Reports']['country'].' And Userprofile.referrals IS NOT NULL And DATE_FORMAT(User.rigistration_date ,"%Y-%m-%d")="'.$dt.'"');
			    		}
			    	   elseif($this->data['Reports']['othercity']!='' && $this->data['Reports']['otherstate']!=''){
			    			$cond = array('Userprofile.otherstate="'.$this->data['Reports']['otherstate'].'" And Userprofile.othercity="'.$this->data['Reports']['othercity'].'" And Userprofile.country='.$this->data['Reports']['country'].' And Userprofile.referrals IS NOT NULL And DATE_FORMAT(User.rigistration_date ,"%Y-%m-%d")="'.$dt.'"');
			    		}
			    	   elseif($this->data['Reports']['othercity']=='' && $this->data['Reports']['otherstate']==''){
			    			$cond = array('Userprofile.country='.$this->data['Reports']['country'].' And Userprofile.referrals IS NOT NULL And DATE_FORMAT(User.rigistration_date ,"%Y-%m-%d")="'.$dt.'"');
			    		}
			    	}
			    	
			        break;
			    case 'week':
			    	
			    	if($this->params['form']['startDate']!='' && $this->params['form']['endDate']!=''){
			    	if($this->data['Reports']['country']==40){
			    		if($this->data['Reports']['state']!=0 && $this->data['Reports']['city']==0){
			    			$cond = array('Userprofile.state='.$this->data['Reports']['state'].' And Userprofile.country='.$this->data['Reports']['country'].' And Userprofile.referrals IS NOT NULL And DATE_FORMAT(User.rigistration_date ,"%m/%d/%Y") BETWEEN "'.$this->params['form']['startDate'].'" AND "'.$this->params['form']['endDate'].'"');
			    		}
			    	    elseif($this->data['Reports']['city']!=0 && $this->data['Reports']['state']==0){
			    			$cond = array('Userprofile.city='.$this->data['Reports']['city'].' And Userprofile.country='.$this->data['Reports']['country'].' And Userprofile.referrals IS NOT NULL And DATE_FORMAT(User.rigistration_date ,"%m/%d/%Y") BETWEEN "'.$this->params['form']['startDate'].'" AND "'.$this->params['form']['endDate'].'"');
			    		}
			    	   elseif($this->data['Reports']['city']!=0 && $this->data['Reports']['state']!=0){
			    			$cond = array('Userprofile.state='.$this->data['Reports']['state'].' And Userprofile.city='.$this->data['Reports']['city'].' And Userprofile.country='.$this->data['Reports']['country'].' And Userprofile.referrals IS NOT NULL And DATE_FORMAT(User.rigistration_date ,"%m/%d/%Y") BETWEEN "'.$this->params['form']['startDate'].'" AND "'.$this->params['form']['endDate'].'"');
			    		}
			    	elseif($this->data['Reports']['city']==0 && $this->data['Reports']['state']==0){
			    			$cond = array('Userprofile.country='.$this->data['Reports']['country'].' And Userprofile.referrals IS NOT NULL And DATE_FORMAT(User.rigistration_date ,"%m/%d/%Y") BETWEEN "'.$this->params['form']['startDate'].'" AND "'.$this->params['form']['endDate'].'"');
			    		}
			    	}else{
			    	if($this->data['Reports']['otherstate']!='' && $this->data['Reports']['othercity']==''){
			    			$cond = array('Userprofile.otherstate="'.$this->data['Reports']['otherstate'].'" And Userprofile.country='.$this->data['Reports']['country'].' And Userprofile.referrals IS NOT NULL And DATE_FORMAT(User.rigistration_date ,"%m/%d/%Y") BETWEEN "'.$this->params['form']['startDate'].'" AND "'.$this->params['form']['endDate'].'"');
			    		}
			    	    elseif($this->data['Reports']['othercity']!='' && $this->data['Reports']['otherstate']==''){
			    			$cond = array('Userprofile.othercity="'.$this->data['Reports']['othercity'].'" And Userprofile.country='.$this->data['Reports']['country'].' And Userprofile.referrals IS NOT NULL And DATE_FORMAT(User.rigistration_date ,"%m/%d/%Y") BETWEEN "'.$this->params['form']['startDate'].'" AND "'.$this->params['form']['endDate'].'"');
			    		}
			    	   elseif($this->data['Reports']['othercity']!='' && $this->data['Reports']['otherstate']!=''){
			    			$cond = array('Userprofile.otherstate="'.$this->data['Reports']['otherstate'].'" And Userprofile.othercity="'.$this->data['Reports']['othercity'].'" And Userprofile.country='.$this->data['Reports']['country'].' And Userprofile.referrals IS NOT NULL And DATE_FORMAT(User.rigistration_date ,"%m/%d/%Y") BETWEEN "'.$this->params['form']['startDate'].'" AND "'.$this->params['form']['endDate'].'"');
			    		}
			    		elseif($this->data['Reports']['othercity']=='' && $this->data['Reports']['otherstate']==''){
			    			$cond = array('Userprofile.country='.$this->data['Reports']['country'].' And Userprofile.referrals IS NOT NULL And DATE_FORMAT(User.rigistration_date ,"%m/%d/%Y") BETWEEN "'.$this->params['form']['startDate'].'" AND "'.$this->params['form']['endDate'].'"');
			    		}
			    	}
			    		
			    	}else{
			    		$flg =1;
			    	}
			        break;
			    case 'month':
			    	$month=$this->data['Reports']['month'];
			    	$monthmatch = date('Y').'-'.$month;
                    if($this->data['Reports']['country']==40){
			    		if($this->data['Reports']['state']!=0 && $this->data['Reports']['city']==0){
			    			$cond = array('Userprofile.state='.$this->data['Reports']['state'].' And Userprofile.country='.$this->data['Reports']['country'].' And Userprofile.referrals IS NOT NULL And DATE_FORMAT(User.rigistration_date ,"%Y-%m")="'.$monthmatch.'"');
			    		}
			    	    elseif($this->data['Reports']['city']!=0 && $this->data['Reports']['state']==0){
			    			$cond = array('Userprofile.city='.$this->data['Reports']['city'].' And Userprofile.country='.$this->data['Reports']['country'].' And Userprofile.referrals IS NOT NULL And DATE_FORMAT(User.rigistration_date ,"%Y-%m")="'.$monthmatch.'"');
			    		}
			    	   elseif($this->data['Reports']['city']!=0 && $this->data['Reports']['state']!=0){
			    			$cond = array('Userprofile.state='.$this->data['Reports']['state'].' And Userprofile.city='.$this->data['Reports']['city'].' And Userprofile.country='.$this->data['Reports']['country'].' And Userprofile.referrals IS NOT NULL And DATE_FORMAT(User.rigistration_date ,"%Y-%m")="'.$monthmatch.'"');
			    		}
                     elseif($this->data['Reports']['city']==0 && $this->data['Reports']['state']==0){
			    			$cond = array('Userprofile.country='.$this->data['Reports']['country'].' And Userprofile.referrals IS NOT NULL And DATE_FORMAT(User.rigistration_date ,"%Y-%m")="'.$monthmatch.'"');
			    		}
			    	}else{
			    	if($this->data['Reports']['otherstate']!='' && $this->data['Reports']['othercity']==''){
			    			$cond = array('Userprofile.otherstate="'.$this->data['Reports']['otherstate'].'" And Userprofile.country='.$this->data['Reports']['country'].' And Userprofile.referrals IS NOT NULL And DATE_FORMAT(User.rigistration_date ,"%Y-%m")="'.$monthmatch.'"');
			    		}
			    	    elseif($this->data['Reports']['othercity']!='' && $this->data['Reports']['otherstate']==''){
			    			$cond = array('Userprofile.othercity="'.$this->data['Reports']['othercity'].'" And Userprofile.country='.$this->data['Reports']['country'].' And Userprofile.referrals IS NOT NULL And DATE_FORMAT(User.rigistration_date ,"%Y-%m")="'.$monthmatch.'"');
			    		}
			    	   elseif($this->data['Reports']['othercity']!='' && $this->data['Reports']['otherstate']!=''){
			    			$cond = array('Userprofile.otherstate="'.$this->data['Reports']['otherstate'].'" And Userprofile.othercity="'.$this->data['Reports']['othercity'].'" And Userprofile.country='.$this->data['Reports']['country'].' And Userprofile.referrals IS NOT NULL And DATE_FORMAT(User.rigistration_date ,"%Y-%m")="'.$monthmatch.'"');
			    	   }
			    	  elseif($this->data['Reports']['othercity']=='' && $this->data['Reports']['otherstate']==''){
			    			$cond = array('Userprofile.country='.$this->data['Reports']['country'].' And Userprofile.referrals IS NOT NULL And DATE_FORMAT(User.rigistration_date ,"%Y-%m")="'.$monthmatch.'"');
			    	   }
			    	}
			    	
			        break;
				case 'year':
					$year = $month=$this->data['Reports']['year'];
			        
                    if($this->data['Reports']['country']==40){
			    		if($this->data['Reports']['state']!=0 && $this->data['Reports']['city']==0){
			    			$cond = array('Userprofile.state='.$this->data['Reports']['state'].' And Userprofile.country='.$this->data['Reports']['country'].' And Userprofile.referrals IS NOT NULL And DATE_FORMAT(User.rigistration_date ,"%Y")="'.$year.'"');
			    		}
			    	    elseif($this->data['Reports']['city']!=0 && $this->data['Reports']['state']==0){
			    			$cond = array('Userprofile.city='.$this->data['Reports']['city'].' And Userprofile.country='.$this->data['Reports']['country'].' And Userprofile.referrals IS NOT NULL And DATE_FORMAT(User.rigistration_date ,"%Y")="'.$year.'"');
			    		}
			    	   elseif($this->data['Reports']['city']!=0 && $this->data['Reports']['state']!=0){
			    			$cond = array('Userprofile.state='.$this->data['Reports']['state'].' And Userprofile.city='.$this->data['Reports']['city'].' And Userprofile.country='.$this->data['Reports']['country'].' And Userprofile.referrals IS NOT NULL And DATE_FORMAT(User.rigistration_date ,"%Y")="'.$year.'"');
			    		}
                      elseif($this->data['Reports']['city']==0 && $this->data['Reports']['state']==0){
			    			$cond = array('Userprofile.country='.$this->data['Reports']['country'].' And Userprofile.referrals IS NOT NULL And DATE_FORMAT(User.rigistration_date ,"%Y")="'.$year.'"');
			    		}
			    	}else{
			    	if($this->data['Reports']['otherstate']!='' && $this->data['Reports']['othercity']==''){
			    			$cond = array('Userprofile.otherstate="'.$this->data['Reports']['otherstate'].'" And Userprofile.country='.$this->data['Reports']['country'].' And Userprofile.referrals IS NOT NULL And DATE_FORMAT(User.rigistration_date ,"%Y")="'.$year.'"');
			    		}
			    	    elseif($this->data['Reports']['othercity']!='' && $this->data['Reports']['otherstate']==''){
			    			$cond = array('Userprofile.othercity="'.$this->data['Reports']['othercity'].'" And Userprofile.country='.$this->data['Reports']['country'].' And Userprofile.referrals IS NOT NULL And DATE_FORMAT(User.rigistration_date ,"%Y")="'.$year.'"');
			    		}
			    	   elseif($this->data['Reports']['othercity']!='' && $this->data['Reports']['otherstate']!=''){
			    			$cond = array('Userprofile.otherstate="'.$this->data['Reports']['otherstate'].'" And Userprofile.othercity="'.$this->data['Reports']['othercity'].'" And Userprofile.country='.$this->data['Reports']['country'].' And Userprofile.referrals IS NOT NULL And DATE_FORMAT(User.rigistration_date ,"%Y")="'.$year.'"');
			    		}
			    	  elseif($this->data['Reports']['othercity']=='' && $this->data['Reports']['otherstate']==''){
			    			$cond = array('Userprofile.country='.$this->data['Reports']['country'].' And Userprofile.referrals IS NOT NULL And DATE_FORMAT(User.rigistration_date ,"%Y")="'.$year.'"');
			    		}
			    	}
					
			      break;
		   }
        if($flg==0){
	       	$result = $this->User->find('all',array('fields'=>'COUNT(Userprofile.id) as cnt , group_concat(User.id) as re, Userprofile.referrals','conditions'=>$cond,'group'=>'Userprofile.referrals'));
			$this->set('result',$result);
	       	
		}elseif($flg == 1){
			$this->Session->setFlash('Please select the week from calender');
		}
    }

}
/*___________________________________________________________________________________________________
* Method     : stateupdate
* Purpose    : cityupdate 
* Parameters : None
* 
* ___________________________________________________________________________________________________
*/
function stateupdate() {
    $this->autoRender=true;
          if($this->RequestHandler->isAjax()){
             Configure::write('debug', 0);
              $stateList = $this->State_master->find('list',array('conditions' => array('State_master.status' => '1', 'State_master.country_id'=>$_POST['id']),
                                                'order' => 'State_master.state_name ASC',
                                                'limit' => null,
                                                'fields' => 'State_master.state_name', 'State_master.id'));
              $this->set('stateList',$stateList);
    }
}

/*___________________________________________________________________________________________________
* 
* Method     : cityupdate
* Purpose    : cityupdate 
* Parameters : None
* 
* ___________________________________________________________________________________________________
*/
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



/*___________________________________________________________________________________________________
* 
* Method     : viewdetail
* Purpose    : viewdetail 
* Parameters : $id= NULL
* 
* ___________________________________________________________________________________________________
*/
function viewdetail($id=null){
  
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

/*___________________________________________________________________________________________________
* 
* Method     : refname
* Purpose    : Refname 
* Parameters : $id=NULL
* 
* ___________________________________________________________________________________________________
*/
function refname($id=null,$refid=NULL){
  
    $this->layout = '';
    $results=$this->User->find('all',array('conditions'=>array('MD5(User.id)' =>$id)));
    $this->set('result',$results[0]);
    $this->set('refname',$refid);
    

}
/*___________________________________________________________________________________________________
* 
* Method     : refname
* Purpose    : Refname 
* Parameters : $id=NULL
* 
* ___________________________________________________________________________________________________
*/
function challengecolleague(){
		if(!empty($this->data)){
		$flg =0;		
		$dt = date('Y-m-d');
		$cond = array();	
         switch ($this->data['Reports']['criteria']) {
			    case 'daily':
			    	
			    	$cond = array('DATE_FORMAT(Challenge.challenged_dt ,"%Y-%m-%d")="'.$dt.'"');
			        break;
			    case 'week':
			    	
			    	if($this->params['form']['startDate']!='' && $this->params['form']['endDate']!=''){
			        $cond = array('DATE_FORMAT(Challenge.challenged_dt ,"%m/%d/%Y") BETWEEN "'.$this->params['form']['startDate'].'" AND "'.$this->params['form']['endDate'].'"');
			    	}else{
			    		$flg =1;
			    	}
			        break;
			    case 'month':
			    	$month=$this->data['Reports']['month'];
			    	$monthmatch = date('Y').'-'.$month;
			    	$cond = array('DATE_FORMAT(Challenge.challenged_dt ,"%Y-%m")="'.$monthmatch.'"');
			        break;
				case 'year':
					$year = $month=$this->data['Reports']['year'];
			        $cond = array('DATE_FORMAT(Challenge.challenged_dt ,"%Y")="'.$year.'"');
			        break;
		   }
		if($flg==0){
	       	$result = $this->Challenge->find('all',array('fields'=>'count(Challenge.id) as cnt , group_concat(DISTINCT(Challenge.to_id)) as toid ,Challenge.from_id','conditions'=>$cond,'group'=>'Challenge.from_id'));
			$this->set('result',$result);
	       	
		}elseif($flg == 1){
			$this->Session->setFlash('Please select the week from calender');
		}
	}
	
    

}

/*___________________________________________________________________________________________________
* 
* Method     : edivearned
* Purpose    : Refname 
* Parameters : $id=NULL
* 
* ___________________________________________________________________________________________________
*/
function edivearned(){
	
	$this->checkadmin();
	$flg=0;
	$cond=array();
	$dt = date('Y-m-d');
/////////////// list country //
	$countries = $this->Country_master->find('list',array('conditions' => array('Country_master.status =' => '1'),
                                                'order' => 'Country_master.country_name ASC',
                                                'limit' => null,
                                                'fields' => 'Country_master.country_name', 'Country_master.id'));
	
	$this->set('countryList',$countries);
	///// Start Reporting //
	if(!empty($this->data)){
     switch ($this->data['Reports']['criteria']) {
			    case 'daily':
			    	$cond = array('DATE_FORMAT(Edivuseramount.datetime ,"%Y-%m-%d")="'.$dt.'"');
			        break;
			    case 'week':
			    	if($this->params['form']['startDate']!='' && $this->params['form']['endDate']!=''){
			        $cond = array('DATE_FORMAT(Edivuseramount.datetime ,"%m/%d/%Y") BETWEEN "'.$this->params['form']['startDate'].'" AND "'.$this->params['form']['endDate'].'"');
			    	}else{
			    		$flg =1;
			    	}
			        break;
			    case 'month':
			    	$month=$this->data['Reports']['month'];
			    	$monthmatch = date('Y').'-'.$month;
			    	$cond = array('DATE_FORMAT(Edivuseramount.datetime ,"%Y-%m")="'.$monthmatch.'"');
			        break;
				case 'year':
					$year = $month=$this->data['Reports']['year'];
			        $cond = array('DATE_FORMAT(Edivuseramount.datetime ,"%Y")="'.$year.'"');
			        break;
		   }
	  if($flg==0){
	       	$result = $this->Edivuseramount->find('all',array('conditions'=>$cond));
	       	$userAr=array();
			for($i=0;$i<count($result);$i++){
				$checkUser = $this->get_user_info($result[$i]['Edivuseramount']['uid'], 'Userprofile.country,Userprofile.state,Userprofile.city,Userprofile.otherstate,Userprofile.othercity',false);
				
				if($this->data['Reports']['country']==40 && $checkUser['Userprofile']['country']==40){
					//
					if($this->data['Reports']['state']!=0 && $this->data['Reports']['city']!=0){
						if($this->data['Reports']['state']==$checkUser['Userprofile']['state'] && $this->data['Reports']['city']==$checkUser['Userprofile']['city']){
							$userAr[]=$result[$i]['Edivuseramount']['id'];
						}
					}
				elseif($this->data['Reports']['state']==0 && $this->data['Reports']['city']!=0){
						if($this->data['Reports']['city']==$checkUser['Userprofile']['city']){
							$userAr[]=$result[$i]['Edivuseramount']['id'];
						}
					}
				elseif($this->data['Reports']['state']!=0 && $this->data['Reports']['city']==0){
						if($this->data['Reports']['state']==$checkUser['Userprofile']['state']){
							$userAr[]=$result[$i]['Edivuseramount']['id'];
						}
					}
				elseif($this->data['Reports']['state']==0 && $this->data['Reports']['city']==0){
							$userAr[]=$result[$i]['Edivuseramount']['id'];
						
					}// ending all posibility to find  the users //for 40 cont ID //
				}elseif($this->data['Reports']['country']!=40 && $checkUser['Userprofile']['country']!=40 && $checkUser['Userprofile']['country']==$this->data['Reports']['country']){
					//////////////////////////
				if($this->data['Reports']['otherstate']!='' && $this->data['Reports']['othercity']!=''){
						if($this->data['Reports']['otherstate']==$checkUser['Userprofile']['otherstate'] && $this->data['Reports']['othercity']==$checkUser['Userprofile']['othercity']){
							$userAr[]=$result[$i]['Edivuseramount']['id'];
						}
					}
				elseif($this->data['Reports']['otherstate']=='' && $this->data['Reports']['othercity']!=''){
						if($this->data['Reports']['othercity']==$checkUser['Userprofile']['othercity']){
							$userAr[]=$result[$i]['Edivuseramount']['id'];
						}
					}
				elseif($this->data['Reports']['otherstate']!='' && $this->data['Reports']['othercity']==''){
					  
						if($this->data['Reports']['otherstate']==$checkUser['Userprofile']['otherstate']){
							$userAr[]=$result[$i]['Edivuseramount']['id'];
						}
					}
				elseif($this->data['Reports']['otherstate']=='' && $this->data['Reports']['othercity']==''){
							$userAr[]=$result[$i]['Edivuseramount']['id'];
						
					}
					/////////////////////////////
					
				}
			
			}
			$finalresult=array();
			if(!empty($userAr)){
				$str='';
				foreach($userAr as $key=>$val){
					$str = $str.','.$val;
				}
			
			$finalresult = $this->Edivuseramount->find('all',array('fields'=>'uid,ediv_type,SUM(points) as sum','conditions'=>'id  IN ('.substr($str,1).')','group'=>'uid,ediv_type'));
			}else{
				$finalresult =array();
			}
			$this->set('finalresult',$finalresult);
	       	
	       	//$this->set('result',$result);
	       	
		}elseif($flg == 1){
			$this->Session->setFlash('Please select the week from calender');
		}
	
	}
	
}

}// End class

?>
