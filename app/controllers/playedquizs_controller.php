<?php
/* 
#################################################################################
#																				#
#   User Controller 															#
#   file name        	: playedquizs_controller.php							#
#   Developed        	: Meridian Radiology 									#	
#   Intialised Variables: name , helpers ,components , uses , layout = (default)#
#																				#
#																				#
#################################################################################
*/


class PlayedquizsController extends AppController {

	    var $name = 'Playedquizs';
            var $uses = array('User','Userprofile','Quizcat','Quizquestion','Quizanswer','Quizuserattempt');
            var $components = array('Usercomponent', 'Session','Image','Email','RequestHandler');
	        var $helpers = array('Html', 'Form','Javascript','Ajax', 'Crypt');
   

		


/******************************************************************************************************/
/*
* ___________________________________________________________________
* Method quizstart
* Purpose: Display Quiz question
* Parameters : None
*
* _______________________________________________________________________
    */
function quizstart($id=NULL,$id2=NULl){
	
	// Check user athontication //
	$this->checkUserLogin();
	// Set Layout
	
	// Set category
	$cond = array();
	
	$records = $this->Quizcat->find('all',array('fields'=>'category_name,parent_id','conditions'=>array('id IN ('.$id.','.$id2.')')));
	$this->set('rootCat',$this->catname($records[0]['Quizcat']['parent_id']));
	$this->set('data',$records);
	$this->set('url',$id.'/'.$id2);
        $this->set('catSetId',$id);
	
	
	
	if(isset($this->data['Playedquizs']['qnum']) && !empty($this->data['Playedquizs']['qnum'])){
		$qnum=$this->data['Playedquizs']['qnum'];
		$cond=array('cat_id IN ('.$id.','.$id2.') and question_id  NOT IN  ('.$this->data['Playedquizs']['qid'].')'); 
		
	}else{
		$qnum=1;
		
		$cond=array('cat_id IN ('.$id.','.$id2.') ');	
			
	}
	// For taking played qiuiz game //
	$this->Quizquestion->bindModel(array('hasOne'   => array(
	                                     'Quizuserattempt' => array('foreignKey' => false,'type'=>'inner','conditions'=>'Quizquestion.question_id=Quizuserattempt.qid and Quizuserattempt.uid = '.$this->Session->read('User.id')))),
	                                false);
	                               
	
	if($qnum==1){
		$qrecord = $this->Quizquestion->find('all',array('order'=>'rand()','limit'=>1,'fields'=>'question_id,image_name,question_text,cat_id','conditions'=>$cond));
		$arecord = $this->Quizanswer->find('all',array('fields'=>'id,answer_text,answer_discription','conditions'=>array('qid'=>$qrecord[0]['Quizquestion']['question_id'])));
		$this->set('qdata',$qrecord[0]['Quizquestion']);
		$this->set('adata',$arecord);
		$this->set('num',$qnum);
		$this->set('qset',$qrecord[0]['Quizquestion']['question_id']);
		
	}
	elseif($qnum==2){
		 $this->Session->write('replay.1.qid' , $this->data['Playedquizs']['qid']);
		 $this->Session->write('replay.1.cat' , $this->data['Playedquizs']['cat']);
		 $this->Session->write('replay.1.selectAns' , $this->data['Playedquizs']['answer']);
		 $qrecord = $this->Quizquestion->find('all',array('order'=>'rand()','limit'=>1,'fields'=>'question_id,image_name,question_text,cat_id','conditions'=>$cond));
		$arecord = $this->Quizanswer->find('all',array('fields'=>'id,answer_text,answer_discription','conditions'=>array('qid'=>$qrecord[0]['Quizquestion']['question_id'])));
		$this->set('qdata',$qrecord[0]['Quizquestion']);
		$this->set('adata',$arecord);
		$this->set('num',$qnum);
		$this->set('qset',$this->data['Playedquizs']['qid'].','.$qrecord[0]['Quizquestion']['question_id']);
		
	}
    elseif($qnum==3){
    	 $Qid=explode(",",$this->data['Playedquizs']['qid']);
    	 $this->Session->write('replay.2.qid' ,$Qid[1]);
		 $this->Session->write('replay.2.cat' , $this->data['Playedquizs']['cat']);
		 $this->Session->write('replay.2.selectAns' , $this->data['Playedquizs']['answer']);
		$qrecord = $this->Quizquestion->find('all',array('order'=>'rand()','limit'=>1,'fields'=>'question_id,image_name,question_text,cat_id','conditions'=>$cond));
		$arecord = $this->Quizanswer->find('all',array('fields'=>'id,answer_text,answer_discription','conditions'=>array('qid'=>$qrecord[0]['Quizquestion']['question_id'])));
		$this->set('qdata',$qrecord[0]['Quizquestion']);
		$this->set('adata',$arecord);
		$this->set('num',$qnum);
		$this->set('qset',$this->data['Playedquizs']['qid'].','.$qrecord[0]['Quizquestion']['question_id']);
		
	}
    elseif($qnum==4){
    	$Qid=explode(",",$this->data['Playedquizs']['qid']);
    	$this->Session->write('replay.3.qid' , $Qid[2]);
		 $this->Session->write('replay.3.cat' , $this->data['Playedquizs']['cat']);
		 $this->Session->write('replay.3.selectAns' , $this->data['Playedquizs']['answer']);
		$qrecord = $this->Quizquestion->find('all',array('order'=>'rand()','limit'=>1,'fields'=>'question_id,image_name,question_text,cat_id','conditions'=>$cond));
		$arecord = $this->Quizanswer->find('all',array('fields'=>'id,answer_text,answer_discription','conditions'=>array('qid'=>$qrecord[0]['Quizquestion']['question_id'])));
		$this->set('qdata',$qrecord[0]['Quizquestion']);
		$this->set('adata',$arecord);
		$this->set('num',$qnum);
		$this->set('qset',$this->data['Playedquizs']['qid'].','.$qrecord[0]['Quizquestion']['question_id']);
		
	}
  elseif($qnum==5){
  	    $Qid=explode(",",$this->data['Playedquizs']['qid']);
  	    $this->Session->write('replay.4.qid' , $Qid[3]);
		 $this->Session->write('replay.4.cat' , $this->data['Playedquizs']['cat']);
		 $this->Session->write('replay.4.selectAns' , $this->data['Playedquizs']['answer']);
		$qrecord = $this->Quizquestion->find('all',array('order'=>'rand()','limit'=>1,'fields'=>'question_id,image_name,question_text,cat_id','conditions'=>$cond));
		$arecord = $this->Quizanswer->find('all',array('fields'=>'id,answer_text,answer_discription','conditions'=>array('qid'=>$qrecord[0]['Quizquestion']['question_id'])));
		$this->set('qdata',$qrecord[0]['Quizquestion']);
		$this->set('adata',$arecord);
		$this->set('num',$qnum);
		$this->set('qset',$this->data['Playedquizs']['qid'].','.$qrecord[0]['Quizquestion']['question_id']);
			
	}
elseif($qnum==6){
	    if(isset($this->data['Playedquizs']['final']) && !empty($this->data['Playedquizs']['final'])){
	    	$Qid=explode(",",$this->data['Playedquizs']['qid']);
	    	$this->Session->write('replay.6.qid' , $Qid[5]);
		 	$this->Session->write('replay.6.cat' , $this->data['Playedquizs']['cat']);
		 	$this->Session->write('replay.6.selectAns' , $this->data['Playedquizs']['answer']);	
		 	$this->set('dataVal',$this->Session->read('replay'));
			$this->set('finalCheck','finished');	 	
		 	
	    }else{
	    	$Qid=explode(",",$this->data['Playedquizs']['qid']);
	    	$this->Session->write('replay.5.qid' , $Qid[4]);
		 	$this->Session->write('replay.5.cat' , $this->data['Playedquizs']['cat']);
		 	$this->Session->write('replay.5.selectAns' , $this->data['Playedquizs']['answer']);
		 	$qrecord = $this->Quizquestion->find('all',array('order'=>'rand()','limit'=>1,'fields'=>'question_id,image_name,question_text,cat_id','conditions'=>$cond));
		    $arecord = $this->Quizanswer->find('all',array('fields'=>'id,answer_text,answer_discription','conditions'=>array('qid'=>$qrecord[0]['Quizquestion']['question_id'])));
		    $this->set('qdata',$qrecord[0]['Quizquestion']);
		    $this->set('adata',$arecord);
		    $this->set('num',$qnum);
		    $this->set('qset',$this->data['Playedquizs']['qid'].','.$qrecord[0]['Quizquestion']['question_id']);
		
		 	
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
function reviewquestion($id=NULL,$id2=NULL){
	// Check user athontication //
	$this->checkuserlogin();
	// Reading Session Array // 
	if($this->Session->read('replay')!=''){
	$qar = $this->Session->read('replay');
	
	$this->set('dataVal',$qar);
	 $this->set('catSetId',$id);
	// Setting Numbers // 
	$qnum=1;
	if(isset($this->data['Playedquizs']['qnum']) && !empty($this->data['Playedquizs']['qnum'])){
		$qnum=$this->data['Playedquizs']['qnum'];
	}else{
		$num=1;
		
	}
    // cat name //
    $records = $this->Quizcat->find('all',array('fields'=>'category_name,parent_id','conditions'=>array('id IN ('.$id.','.$id2.')')));
	$this->set('data',$records);	
	$this->set('rootCat',$this->catname($records[0]['Quizcat']['parent_id']));
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
		 $this->Session->write('replay.1.qid' , $this->data['Playedquizs']['qid']);
		 $this->Session->write('replay.1.cat' , $this->data['Playedquizs']['cat']);
		 $this->Session->write('replay.1.selectAns' , $this->data['Playedquizs']['answer']);
		
		$cond=array('cat_id='.$qar['2']['cat'].' and question_id = '.$qar['2']['qid']);	
		$qrecord = $this->Quizquestion->find('all',array('limit'=>1,'fields'=>'question_id,image_name,question_text,cat_id','conditions'=>$cond));
		$arecord = $this->Quizanswer->find('all',array('fields'=>'id,answer_text,answer_discription','conditions'=>array('qid'=>$qrecord[0]['Quizquestion']['question_id'])));
		$this->set('qdata',$qrecord[0]['Quizquestion']);
		$this->set('adata',$arecord);
		$this->set('num',$qnum);
		$this->set('qset',$qrecord[0]['Quizquestion']['question_id']);
		
	}
	if($qnum==3){
		 $this->Session->write('replay.2.qid' , $this->data['Playedquizs']['qid']);
		 $this->Session->write('replay.2.cat' , $this->data['Playedquizs']['cat']);
		 $this->Session->write('replay.2.selectAns' , $this->data['Playedquizs']['answer']);
		
		 $cond=array('cat_id='.$qar['3']['cat'].' and question_id = '.$qar['3']['qid']);	
		$qrecord = $this->Quizquestion->find('all',array('limit'=>1,'fields'=>'question_id,image_name,question_text,cat_id','conditions'=>$cond));
		$arecord = $this->Quizanswer->find('all',array('fields'=>'id,answer_text,answer_discription','conditions'=>array('qid'=>$qrecord[0]['Quizquestion']['question_id'])));
		$this->set('qdata',$qrecord[0]['Quizquestion']);
		$this->set('adata',$arecord);
		$this->set('num',$qnum);
		$this->set('qset',$qrecord[0]['Quizquestion']['question_id']);
		
	}
	if($qnum==4){
		 $this->Session->write('replay.3.qid' , $this->data['Playedquizs']['qid']);
		 $this->Session->write('replay.3.cat' , $this->data['Playedquizs']['cat']);
		 $this->Session->write('replay.3.selectAns' , $this->data['Playedquizs']['answer']);
		
		$cond=array('cat_id='.$qar['4']['cat'].' and question_id = '.$qar['4']['qid']);	
		$qrecord = $this->Quizquestion->find('all',array('limit'=>1,'fields'=>'question_id,image_name,question_text,cat_id','conditions'=>$cond));
		$arecord = $this->Quizanswer->find('all',array('fields'=>'id,answer_text,answer_discription','conditions'=>array('qid'=>$qrecord[0]['Quizquestion']['question_id'])));
		$this->set('qdata',$qrecord[0]['Quizquestion']);
		$this->set('adata',$arecord);
		$this->set('num',$qnum);
		$this->set('qset',$qrecord[0]['Quizquestion']['question_id']);
		
	}
	if($qnum==5){
		 $this->Session->write('replay.4.qid' , $this->data['Playedquizs']['qid']);
		 $this->Session->write('replay.4.cat' , $this->data['Playedquizs']['cat']);
		 $this->Session->write('replay.4.selectAns' , $this->data['Playedquizs']['answer']);

		 $cond=array('cat_id='.$qar['5']['cat'].' and question_id = '.$qar['5']['qid']);	
		$qrecord = $this->Quizquestion->find('all',array('limit'=>1,'fields'=>'question_id,image_name,question_text,cat_id','conditions'=>$cond));
		$arecord = $this->Quizanswer->find('all',array('fields'=>'id,answer_text,answer_discription','conditions'=>array('qid'=>$qrecord[0]['Quizquestion']['question_id'])));
		$this->set('qdata',$qrecord[0]['Quizquestion']);
		$this->set('adata',$arecord);
		$this->set('num',$qnum);
		$this->set('qset',$qrecord[0]['Quizquestion']['question_id']);
		
	}
if($qnum==6){

		if(isset($this->data['Playedquizs']['final']) && !empty($this->data['Playedquizs']['final'])){
	    	$this->Session->write('replay.6.qid' , $this->data['Playedquizs']['qid']);
		 	$this->Session->write('replay.6.cat' , $this->data['Playedquizs']['cat']);
		 	$this->Session->write('replay.6.selectAns' , $this->data['Playedquizs']['answer']);	
		 	$this->set('dataVal',$this->Session->read('play'));
			$this->set('finalCheck','finished');
				
		}else{
	     $this->Session->write('replay.5.qid' , $this->data['Playedquizs']['qid']);
		 $this->Session->write('replay.5.cat' , $this->data['Playedquizs']['cat']);
		 $this->Session->write('replay.5.selectAns' , $this->data['Playedquizs']['answer']);
		
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
     $data = $this->Quizuserattempt->find('all',array('conditions'=>array("date_format(played_date, '%Y-%m-%d %H:%i')"=>"".base64_decode($pd)."", 'uid'=>$this->Session->read('User.id'))));
     $this->set('data',$data);
     $this->set('pd',$pd);
     
    
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
             
 $incorrectData = $this->Quizuserattempt->find('all',array('conditions'=>array("date_format(played_date, '%Y-%m-%d %H:%i')"=>"".base64_decode($pd)."", 'uid'=>$this->Session->read('User.id'), 'id !='=> Configure::read('contoller')->_decrypt($attemptid), 'id >'=> Configure::read('contoller')->_decrypt($attemptid)),'fields'=>array('id','uid','qid','aid','final_result', 'points',"date_format(played_date, '%Y-%m-%d %H:%i') AS PD",'catid','serial_num')));
 $this->set('nextIncorrectQ', $incorrectData);

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
     // Check user athontication //
     $this->checkuserlogin();
     $ansDetail= array();
     $url=$id.'-'.$id2 ;
     $userId =$this->Session->read('User.id');
     $insertAr= array();
     
     if($this->Session->read('replay')!=''){
     for($i=1;$i<=count($this->Session->read('replay'));$i++){

     	  $ansDetail= $this->answerdetail($this->Session->read('replay.'.$i.'.selectAns'));
		  $insertAr['id'] = ''; 	
          $insertAr['uid'] = $userId;
          $insertAr['qid'] = $this->Session->read('replay.'.$i.'.qid');
          $insertAr['aid'] = $this->Session->read('replay.'.$i.'.selectAns');
          $insertAr['final_result'] = $ansDetail['answer_result'];
		  $insertAr['points'] = $ansDetail['point'];
		  $insertAr['serial_num'] = $i;
		  $insertAr['played_date']=date('Y-m-d H:i:s');
		  $insertAr['catid']=$this->Session->read('replay.'.$i.'.cat');
		  $insertAr['name']=$this->catname($this->Session->read('replay.'.$i.'.cat'));
          //$this->Quizuserattempt->save($insertAr);
          
          $this->Session->write('refinalQuiz.'.$i.'.qid' , $this->Session->read('replay.'.$i.'.qid'));
          $this->Session->write('refinalQuiz.'.$i.'.aid' , $this->Session->read('replay.'.$i.'.selectAns'));
          $this->Session->write('refinalQuiz.'.$i.'.points' , $ansDetail['point']);
		  $this->Session->write('refinalQuiz.'.$i.'.final_result' , $ansDetail['answer_result']);
		  $this->Session->write('refinalQuiz.'.$i.'.serial_num' , $i);	
		  $this->Session->write('refinalQuiz.'.$i.'.name' , $insertAr['name']);	          
     	  
     }
     
     
     }
     
     $this->set('data',$this->Session->read('refinalQuiz'));
     $this->set('url',$url);
	 $this->Session->delete('replay');	
       
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
function quizexplaination($qid=NULL,$cid=NULL,$serialnum=NULL){
	
 // Check user athontication //
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
	     $this->set('catBack',$cid);
	     
	    
	     $this->set('sesAns',$this->Session->read('refinalQuiz.'.$serialnum.'.aid'));    
	 
	
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
		$arDetail['point'] = 5;        
    }else{
        $arDetail['answer_result'] = $data[0]['Quizanswer']['result'];
        $arDetail['answer_discription'] = '';
        $arDetail['point'] = 0;
    }
    return $arDetail;
	
	
}
      
      	// End Of the function for users_controller Class
}

?>
