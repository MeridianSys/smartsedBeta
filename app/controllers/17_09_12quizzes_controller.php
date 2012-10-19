<?php
/* 
#################################################################################
#																				#
#   User Controller 															#
#   file name        	: quizzes_controller.php									#
#   Developed        	: Meridian Radiology 									#	
#   Intialised Variables: name , helpers ,components , uses , layout = (default)#
#																				#
#																				#
#################################################################################
*/


class QuizzesController extends AppController {

	var $name = 'Quizzes';
        var $uses = array('User','Userprofile','Quizcat','Quizquestion','Quizanswer','Quizuserattempt','ediv_user_masters');
        var $components = array('Usercomponent', 'Session','Image','Email','RequestHandler');
        var $helpers = array('Html', 'Form','Javascript','Ajax','Fck');


/*___________________________________________________________
* Method     : startquiz
* Purpose    : making new user registration
* Parameters : None
*_____________________________________________________________
*/

function changecategory(){
   $this->layout = '';
   $userID=$this->Session->read('User.id');
   $this->User->id=$userID;
   $userType = Configure::read('contoller')->get_user_info($userID, 'Userprofile.title',false);
   $userTypeAccess=substr($userType['Userprofile']['title'], 0, -1);
        if($userTypeAccess!='Dr'){
           $addQuery='access_type !=2';
        }else{
            $addQuery='access_type !=1';
        }
   $userinfo = $this->User->read();
   $this->set('userinfo',$userinfo);
   $category = $this->Quizcat->find('all',array('conditions'=>$addQuery));
   $this->set('category',$category);

}
function updatecategory(){
    $userID=$this->Session->read('User.id');
   $this->User->id=$userID;
    $this->autoRender=false;
      if($this->RequestHandler->isAjax()){
         Configure::write('debug', 0);
         //echo '<pre>';
         //print_r($_REQUEST);
         $str=$_REQUEST['cat'][0].','.$_REQUEST['cat'][1];
         $this->User->updateAll(array('Userprofile.quiz_cat' => "'$str'"),array('User.id'=>$userID));
         echo '<h4>'.$this->catname($_REQUEST['cat'][0]).', '.$this->catname($_REQUEST['cat'][1]).'_'.$_REQUEST['cat'][0].'/'.$_REQUEST['cat'][1];//.$_REQUEST["catval"];

      }
}

/*
* ___________________________________________________________________
* Method quizstart
* Purpose: Display Quiz question
* Parameters : None
*
* _______________________________________________________________________
*/
function quizstart($id=NULL,$id2=NULl){
	$this->checkuserlogin();
	$userID=$this->Session->read('User.id');
        $this->User->id=$userID;
        $userinfo = $this->User->read();
        $this->set('userinfo',$userinfo);
	$cond = array();

	$records = $this->Quizcat->find('all',array('fields'=>'category_name,parent_id','conditions'=>array('id IN ('.$id.','.$id2.')')));
	$this->set('rootCat',$this->catname($records[0]['Quizcat']['parent_id']));
	$this->set('data',$records);
	$this->set('url',$id.'/'.$id2);

	$inarr = $this->Quizuserattempt->find('all',array('fields'=>'qid','conditions'=>array('uid ='.$this->Session->read('User.id').' ')));
	$str='';
	if(count($inarr)>0){
	for($j=0;$j<count($inarr);$j++){

			$str = $str .','.$inarr[$j]['Quizuserattempt']['qid'];

		}
	}
	// Checking Count //
   if($str!=''){
   $condCount = array('cat_id IN ('.$id.','.$id2.') and question_id  NOT IN ('.substr($str,1).')');
   }else{
   	$condCount = array('cat_id IN ('.$id.','.$id2.')');
   }
   $qrecordCount = $this->Quizquestion->find('all',array('fields'=>'question_id,image_name,question_text,cat_id','conditions'=>$condCount));
   //pr($qrecordCount);
   if(count($qrecordCount)>=6){
   
   //
	if(isset($this->data['Quizze']['qnum']) && !empty($this->data['Quizze']['qnum'])){
		$qnum=$this->data['Quizze']['qnum'];
		$cond=array('cat_id IN ('.$id.','.$id2.') and question_id  NOT IN  ('.$this->data['Quizze']['qid'].$str.')');

	}else{
		$qnum=1;
		if($str!=''){
		$cond=array('cat_id IN ('.$id.','.$id2.') and question_id  NOT IN ('.substr($str,1).')');
		}else{
		$cond=array('cat_id IN ('.$id.','.$id2.')');
		}
	}

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
           
		 $this->Session->write('play.1.qid' , $this->data['Quizze']['qid']);
		 $this->Session->write('play.1.cat' , $this->data['Quizze']['cat']);
		 $this->Session->write('play.1.selectAns' , $this->data['Quizze']['answer']);
		$qrecord = $this->Quizquestion->find('all',array('order'=>'rand()','limit'=>1,'fields'=>'question_id,image_name,question_text,cat_id','conditions'=>$cond));
		$arecord = $this->Quizanswer->find('all',array('fields'=>'id,answer_text,answer_discription','conditions'=>array('qid'=>$qrecord[0]['Quizquestion']['question_id'])));
		$this->set('qdata',$qrecord[0]['Quizquestion']);
		$this->set('adata',$arecord);
		$this->set('num',$qnum);
		$this->set('qset',$this->data['Quizze']['qid'].','.$qrecord[0]['Quizquestion']['question_id']);

	}
    elseif($qnum==3){
    	 $Qid=explode(",",$this->data['Quizze']['qid']);
    	 $this->Session->write('play.2.qid' ,$Qid[1]);
		 $this->Session->write('play.2.cat' , $this->data['Quizze']['cat']);
		 $this->Session->write('play.2.selectAns' , $this->data['Quizze']['answer']);
		$qrecord = $this->Quizquestion->find('all',array('order'=>'rand()','limit'=>1,'fields'=>'question_id,image_name,question_text,cat_id','conditions'=>$cond));
		$arecord = $this->Quizanswer->find('all',array('fields'=>'id,answer_text,answer_discription','conditions'=>array('qid'=>$qrecord[0]['Quizquestion']['question_id'])));
		$this->set('qdata',$qrecord[0]['Quizquestion']);
		$this->set('adata',$arecord);
		$this->set('num',$qnum);
		$this->set('qset',$this->data['Quizze']['qid'].','.$qrecord[0]['Quizquestion']['question_id']);

	}
    elseif($qnum==4){
    	$Qid=explode(",",$this->data['Quizze']['qid']);
    	$this->Session->write('play.3.qid' , $Qid[2]);
		 $this->Session->write('play.3.cat' , $this->data['Quizze']['cat']);
		 $this->Session->write('play.3.selectAns' , $this->data['Quizze']['answer']);
		$qrecord = $this->Quizquestion->find('all',array('order'=>'rand()','limit'=>1,'fields'=>'question_id,image_name,question_text,cat_id','conditions'=>$cond));
		$arecord = $this->Quizanswer->find('all',array('fields'=>'id,answer_text,answer_discription','conditions'=>array('qid'=>$qrecord[0]['Quizquestion']['question_id'])));
		$this->set('qdata',$qrecord[0]['Quizquestion']);
		$this->set('adata',$arecord);
		$this->set('num',$qnum);
		$this->set('qset',$this->data['Quizze']['qid'].','.$qrecord[0]['Quizquestion']['question_id']);

	}
  elseif($qnum==5){
  	    $Qid=explode(",",$this->data['Quizze']['qid']);
  	    $this->Session->write('play.4.qid' , $Qid[3]);
		 $this->Session->write('play.4.cat' , $this->data['Quizze']['cat']);
		 $this->Session->write('play.4.selectAns' , $this->data['Quizze']['answer']);
		$qrecord = $this->Quizquestion->find('all',array('order'=>'rand()','limit'=>1,'fields'=>'question_id,image_name,question_text,cat_id','conditions'=>$cond));
		$arecord = $this->Quizanswer->find('all',array('fields'=>'id,answer_text,answer_discription','conditions'=>array('qid'=>$qrecord[0]['Quizquestion']['question_id'])));
		$this->set('qdata',$qrecord[0]['Quizquestion']);
		$this->set('adata',$arecord);
		$this->set('num',$qnum);
		$this->set('qset',$this->data['Quizze']['qid'].','.$qrecord[0]['Quizquestion']['question_id']);

	}
elseif($qnum==6){
	    if(isset($this->data['Quizze']['final']) && !empty($this->data['Quizze']['final'])){
                $this->Session->write('timeend' , date('Y-m-d H:i:s'));
	    	$Qid=explode(",",$this->data['Quizze']['qid']);
	    	$this->Session->write('play.6.qid' , $Qid[5]);
		 	$this->Session->write('play.6.cat' , $this->data['Quizze']['cat']);
		 	$this->Session->write('play.6.selectAns' , $this->data['Quizze']['answer']);
		 	$this->set('dataVal',$this->Session->read('play'));
			$this->set('finalCheck','finished');

	    }else{
	    	$Qid=explode(",",$this->data['Quizze']['qid']);
	    	 $this->Session->write('play.5.qid' , $Qid[4]);
		 $this->Session->write('play.5.cat' , $this->data['Quizze']['cat']);
		 $this->Session->write('play.5.selectAns' , $this->data['Quizze']['answer']);
		 $qrecord = $this->Quizquestion->find('all',array('order'=>'rand()','limit'=>1,'fields'=>'question_id,image_name,question_text,cat_id','conditions'=>$cond));
		    $arecord = $this->Quizanswer->find('all',array('fields'=>'id,answer_text,answer_discription','conditions'=>array('qid'=>$qrecord[0]['Quizquestion']['question_id'])));
		    $this->set('qdata',$qrecord[0]['Quizquestion']);
		    $this->set('adata',$arecord);
		    $this->set('num',$qnum);
		    $this->set('qset',$this->data['Quizze']['qid'].','.$qrecord[0]['Quizquestion']['question_id']);

	    }
	}
   }else{
 	
 	$this->set('dataVal','messagelessquestion');
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
function reviewquestion($id=NULL,$id2=NULL){
	$this->checkuserlogin();
	$userID=$this->Session->read('User.id');
        $this->User->id=$userID;
        $userinfo = $this->User->read();
        $this->set('userinfo',$userinfo);
        // Reading Session Array //
	if($this->Session->read('play')!=''){
	$qar = $this->Session->read('play');

	$this->set('dataVal',$qar);

	// Setting Numbers //
	$qnum=1;
	if(isset($this->data['Quizze']['qnum']) && !empty($this->data['Quizze']['qnum'])){
		$qnum=$this->data['Quizze']['qnum'];
	}else{
		$num=1;

	}
    // cat name //
    $records = $this->Quizcat->find('all',array('fields'=>'category_name,parent_id','conditions'=>array('id IN ('.$id.','.$id2.')')));
		$this->set('rootCat',$this->catname($records[0]['Quizcat']['parent_id']));
    $this->set('data',$records);
	$this->set('url',$id.'/'.$id2);


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
		 $this->Session->write('play.1.qid' , $this->data['Quizze']['qid']);
		 $this->Session->write('play.1.cat' , $this->data['Quizze']['cat']);
		 $this->Session->write('play.1.selectAns' , $this->data['Quizze']['answer']);

		$cond=array('cat_id='.$qar['2']['cat'].' and question_id = '.$qar['2']['qid']);
		$qrecord = $this->Quizquestion->find('all',array('limit'=>1,'fields'=>'question_id,image_name,question_text,cat_id','conditions'=>$cond));
		$arecord = $this->Quizanswer->find('all',array('fields'=>'id,answer_text,answer_discription','conditions'=>array('qid'=>$qrecord[0]['Quizquestion']['question_id'])));
		$this->set('qdata',$qrecord[0]['Quizquestion']);
		$this->set('adata',$arecord);
		$this->set('num',$qnum);
		$this->set('qset',$qrecord[0]['Quizquestion']['question_id']);

	}
	if($qnum==3){
		 $this->Session->write('play.2.qid' , $this->data['Quizze']['qid']);
		 $this->Session->write('play.2.cat' , $this->data['Quizze']['cat']);
		 $this->Session->write('play.2.selectAns' , $this->data['Quizze']['answer']);

		 $cond=array('cat_id='.$qar['3']['cat'].' and question_id = '.$qar['3']['qid']);
		$qrecord = $this->Quizquestion->find('all',array('limit'=>1,'fields'=>'question_id,image_name,question_text,cat_id','conditions'=>$cond));
		$arecord = $this->Quizanswer->find('all',array('fields'=>'id,answer_text,answer_discription','conditions'=>array('qid'=>$qrecord[0]['Quizquestion']['question_id'])));
		$this->set('qdata',$qrecord[0]['Quizquestion']);
		$this->set('adata',$arecord);
		$this->set('num',$qnum);
		$this->set('qset',$qrecord[0]['Quizquestion']['question_id']);

	}
	if($qnum==4){
		 $this->Session->write('play.3.qid' , $this->data['Quizze']['qid']);
		 $this->Session->write('play.3.cat' , $this->data['Quizze']['cat']);
		 $this->Session->write('play.3.selectAns' , $this->data['Quizze']['answer']);

		$cond=array('cat_id='.$qar['4']['cat'].' and question_id = '.$qar['4']['qid']);
		$qrecord = $this->Quizquestion->find('all',array('limit'=>1,'fields'=>'question_id,image_name,question_text,cat_id','conditions'=>$cond));
		$arecord = $this->Quizanswer->find('all',array('fields'=>'id,answer_text,answer_discription','conditions'=>array('qid'=>$qrecord[0]['Quizquestion']['question_id'])));
		$this->set('qdata',$qrecord[0]['Quizquestion']);
		$this->set('adata',$arecord);
		$this->set('num',$qnum);
		$this->set('qset',$qrecord[0]['Quizquestion']['question_id']);

	}
	if($qnum==5){
		 $this->Session->write('play.4.qid' , $this->data['Quizze']['qid']);
		 $this->Session->write('play.4.cat' , $this->data['Quizze']['cat']);
		 $this->Session->write('play.4.selectAns' , $this->data['Quizze']['answer']);

		 $cond=array('cat_id='.$qar['5']['cat'].' and question_id = '.$qar['5']['qid']);
		$qrecord = $this->Quizquestion->find('all',array('limit'=>1,'fields'=>'question_id,image_name,question_text,cat_id','conditions'=>$cond));
		$arecord = $this->Quizanswer->find('all',array('fields'=>'id,answer_text,answer_discription','conditions'=>array('qid'=>$qrecord[0]['Quizquestion']['question_id'])));
		$this->set('qdata',$qrecord[0]['Quizquestion']);
		$this->set('adata',$arecord);
		$this->set('num',$qnum);
		$this->set('qset',$qrecord[0]['Quizquestion']['question_id']);

	}
if($qnum==6){

		if(isset($this->data['Quizze']['final']) && !empty($this->data['Quizze']['final'])){
                $this->Session->write('timeend' , date('Y-m-d H:i:s'));
	    	$this->Session->write('play.6.qid' , $this->data['Quizze']['qid']);
                $this->Session->write('play.6.cat' , $this->data['Quizze']['cat']);
                $this->Session->write('play.6.selectAns' , $this->data['Quizze']['answer']);
                $this->set('dataVal',$this->Session->read('play'));
                $this->set('finalCheck','finished');
		}else{
	     $this->Session->write('play.5.qid' , $this->data['Quizze']['qid']);
		 $this->Session->write('play.5.cat' , $this->data['Quizze']['cat']);
		 $this->Session->write('play.5.selectAns' , $this->data['Quizze']['answer']);

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
function quizfinalise($id=NULL,$id2=NULL){
     $this->checkuserlogin();
     
     $ansDetail= array();
     $url=$id.'-'.$id2 ;
     $userId =$this->Session->read('User.id');
     $insertAr= array();
     $sum =0;
     if($this->Session->read('play')!=''){
     for($i=1;$i<=count($this->Session->read('play'));$i++){

     	  $ansDetail= $this->answerdetail($this->Session->read('play.'.$i.'.selectAns'));
		  $insertAr['id'] = '';
          $insertAr['uid'] = $userId;
          $insertAr['qid'] = $this->Session->read('play.'.$i.'.qid');
          $insertAr['aid'] = $this->Session->read('play.'.$i.'.selectAns');
          $insertAr['final_result'] = $ansDetail['answer_result'];
          if($insertAr['final_result']==1){
          $sum = $sum + 1;
          }
          $min = round((strtotime($this->Session->read('timeend'))-strtotime($this->Session->read('timestart')))/60);
       	  $sec = round((strtotime($this->Session->read('timeend'))-strtotime($this->Session->read('timestart')))%60);
          $timetook=$min.' Minutes '.$sec.' Seconds';
                  $insertAr['points'] = $ansDetail['point'];
		  $insertAr['serial_num'] = $i;
		  $insertAr['played_date']= date('Y-m-d H:i:s');
		  $insertAr['catid']=$this->Session->read('play.'.$i.'.cat');
		  $insertAr['name']=$this->catname($this->Session->read('play.'.$i.'.cat'));
		  $insertAr['timetook']=$timetook;
                  $this->Quizuserattempt->save($insertAr);
                  $this->Session->write('finalQuiz.'.$i.'.qid' , $this->Session->read('play.'.$i.'.qid'));
                  $this->Session->write('finalQuiz.'.$i.'.aid' , $this->Session->read('play.'.$i.'.selectAns'));
                  $this->Session->write('finalQuiz.'.$i.'.points' , $ansDetail['point']);
		  $this->Session->write('finalQuiz.'.$i.'.final_result' , $ansDetail['answer_result']);
		  $this->Session->write('finalQuiz.'.$i.'.serial_num' , $i);
		  $this->Session->write('finalQuiz.'.$i.'.name' , $insertAr['name']);

     }
       $edivPoint =$sum * $this->get_ediv(8);
       if($edivPoint > 0){
       $this->ediv_user_masters->query("insert into ediv_user_masters (uid,ediv_type,points,datetime) values (".$userId.",8,".$edivPoint.",'".$insertAr['played_date']."')");
       }
       
     }

     $this->set('timestart',$this->Session->read('timestart'));
     $this->set('timeend',$this->Session->read('timeend'));
     $this->set('data',$this->Session->read('finalQuiz'));
     $this->set('url',$url);
     $this->Session->delete('play');

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
     $this->checkUserLogin();

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
     $this->checkUserLogin();
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
    $this->set('sesAns',$this->Session->read('finalQuiz.'.$serialnum.'.aid'));
    if($chk!=''){
    	$this->set('chk',$chk);
    }
}
/*
* ___________________________________________________________________
* Method quizimport
* Purpose: providing the answer detail of wrong question
* Parameters : $qid,$aid,$serialnum
* Note : function retreive the information
*
* _______________________________________________________________________
*/
function quizimport(){
	 $this->layout = 'admin';
     $this->checkadmin();
    if(!empty($this->data)){
     if($this->data['User']['csvfile']['size'] <= 5242880){
     	@chmod(_ROOT_BASE_PATH."img/quizcsv/", 0777);
     	$ext = explode('.',$this->data['User']['csvfile']['name']);
        $inamecsv=md5(time()).'.'.$ext[1];
        if(move_uploaded_file($this->data['User']['csvfile']['tmp_name'] ,_ROOT_BASE_PATH."img/quizcsv/".$inamecsv)){
                if (($handle = fopen(_ROOT_BASE_PATH."img/quizcsv/".$inamecsv, "r")) !== FALSE) {
				//$movecounter = 1;
                	while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                	   	$cat = $data[0];
                       	$catId = $this->checkcategoryname(strtolower($cat));
                       	if($catId!=0){ // Category exixts previously //
							$tempQuestion['id']='';
							$tempQuestion['question_id']=($this->maxquestionid()+1);
							$tempQuestion['provider_name']='Admin';
							$tempQuestion['cat_id']=$catId;
							$tempQuestion['question_text']=$data[1];
							$tempQuestion['status']='1';
							$tempQuestion['created_date']=date('Y-m-d H:i:s');
                       		$qid[]=$tempQuestion['question_id'];
							$this->Quizquestion->save($tempQuestion);
                       		
                       	    // Answer Process 
                       		$ans =1;
                       		for($j=2;$j<(count($data)-2);$j++){
                       			
                       			$tempans['id']='';
                         		$tempans['qid']=$tempQuestion['question_id'];
                         		$tempans['answer_text']=$data[$j];
                         		if($data[count($data)-2]==$ans){
                         		$tempans['answer_discription']=$data[count($data)-1];
                         		$tempans['result']='1';
                         		}else{
                         		$tempans['answer_discription']='';
                         		$tempans['result']='0';
                         		}
                         		$this->Quizanswer->save($tempans);
                         		$ans++;
                       	     }
                       	 }elseif($catId==0){ // 
                       	 	$tempcat['id']='';
                       	 	$tempcat['category_name']=$data[0];
                       	 	$tempcat['parent_id']=0;
                       	 	$this->Quizcat->save($tempcat);
                       	 	$catId = $this->Quizcat->id;
                       	 	$tempQuestion['id']='';
							$tempQuestion['question_id']=($this->maxquestionid()+1);
							$tempQuestion['provider_name']='Admin';
							$tempQuestion['cat_id']=$catId;
							$tempQuestion['question_text']=$data[1];
							$tempQuestion['status']='1';
							$tempQuestion['created_date']=date('Y-m-d H:i:s');
							$qid[]=$tempQuestion['question_id'];
                       		$this->Quizquestion->save($tempQuestion);
                       	   
                       	   	
                       		// Answer Process 
                       		
							$ans =1;
                       		for($j=2;$j<(count($data)-2);$j++){
                       	    	
                       	        $tempans['id']='';
                         		$tempans['qid']=$tempQuestion['question_id'];
                         		$tempans['answer_text']=$data[$j];
                         		if($data[count($data)-2]==$ans){
                         		$tempans['answer_discription']=$data[count($data)-1];
                         		$tempans['result']='1';
                         		}else{
                         		$tempans['answer_discription']='';
                         		$tempans['result']='0';
                         		}
                       	    	$this->Quizanswer->save($tempans);
                       	    	$ans++;
                            }
                           
                       	}
                	}// End of while loop
    	  } // End of fopen if condition 
        }// End safely move file to folder
        //$this->set('qid',$qid);
        // List of uploaded file //
        $str = '';
        foreach($qid as $key =>$val){
        	$str = $str.','.$val;
        }
      
        $nstr = substr($str,1);
      
        $this->Session->setFlash('Quiz CSV Import successsully ');
        $this->redirect('listofquestions/'.base64_encode($nstr));
     }else{
     	$this->Session->setFlash('Above than 5 MB is not permitted');
        
     } // End of 5MB file
   }// End of data post from server 
   
}
function checkcategoryname($catname=NULL){
	$result = $this->Quizcat->find('all',array('conditions'=>'LOWER(category_name)="'.$catname.'"','fields'=>'id,category_name'));
	if(!empty($result)){
		return $result[0]['Quizcat']['id'];
	}else{
		return 0;
	}
	
}     
function maxquestionid(){
	$result = $this->Quizquestion->find('all',array('fields'=>'MAX(question_id) as quizid'));
	return $result[0][0]['quizid'];
	
}
function listofquestions($str=NULL){
	 $this->layout = 'admin';
     if($str!=''){
	 $inId = explode(',',base64_decode($str));
	 $strNew = '';
        foreach($inId as $key =>$val){
        	$strNew = $strNew . ','.$val;
        }
        $nstr = substr($strNew,1);
        $resultQuestion = $this->Quizquestion->find('all',array('conditions'=>'Quizquestion.question_id in ('.$nstr.')'));
        $this->set('str',$str);
        $this->set('resultQuestion',$resultQuestion);
     }
}     
function deleteuploadedquiz($str=NULL,$id=NULL){
	$strAr = explode(',',base64_decode($str));
	$strId[0] =$id;
    $newAr = array_values(array_diff($strAr,$strId));	
	$strNew = '';
        foreach($newAr as $key =>$val){
        	$strNew = $strNew . ','.$val;
        }
        $nstr = substr($strNew,1);
        
        $this->Quizanswer->deleteAll(array('qid'=>$id));
	    $this->Quizquestion->deleteAll(array('question_id'=>$id));
        $this->redirect('listofquestions/'.base64_encode($nstr));
    	
 }
 
 
}// End
?>