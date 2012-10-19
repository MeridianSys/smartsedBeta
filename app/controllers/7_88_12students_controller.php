<?php
/*
#################################################################################
#																				#
#   Student Controller 															#
#   file name        	: students_controller.php								#
#   Developed        	: Meridian Radiology 									#
#   Intialised Variables: name , helpers ,components , uses , layout = (default)#
#																				#
#																				#
#################################################################################
*/


class StudentsController extends AppController {

	var $name = 'Students';
        var $uses = array('Studentcontent','Studentquestion','Studentanswer','Studentuserattempt','ediv_user_masters','Cafeseriescontent','Cafeseriesquestion','Cafeseriesanswer','Cafeseriesuserattempt','Pausetempuser','Sponsern','Sponserncontent','Sponsernuserattempt','Sponsernquestion','Sponsernanswer','Staticloungevalue');
        var $components = array('Usercomponent', 'Session','Image','Email','RequestHandler');
        var $helpers = array('Html', 'Form','Javascript','Ajax','Fck');
        var $layout = 'admin';

function index(){
    $this->checkadmin();
    $this->Studentcontent->find('all');
    $this->paginate['Studentcontent']=array('conditions'=>'Studentcontent.type="1"','page'=>1,'limit'=>25);
    $data = $this->paginate('Studentcontent');
    $this->set('data',$data);
}
function poll(){
    $this->checkadmin();
    $this->Studentcontent->find('all');
    $this->paginate['Studentcontent']=array('conditions'=>'Studentcontent.type="2"','page'=>1,'limit'=>25);
    $data = $this->paginate('Studentcontent');
    $this->set('data',$data);
}

function studentcontent($id=NULL){

    $this->checkadmin();
    $typeId=0;
    if($id!='' && $id==2){
        $this->set('typeId',2);
    }else{
    	$this->set('typeId',0);
    }
    $contentArray = array();
     $flag = 0;
    $rval = $this->Usercomponent->imageGenerateKey();
    if(!empty($this->data)){
        $contentArray['id']='';
        $contentArray['type']=$this->data['Students']['type'];// 1:for Student Quiz  2:for Student Poll
        $contentArray['category']=$this->data['Students']['category'];
        $contentArray['name']=$this->data['Students']['name'];
        $contentArray['instructions']=$this->data['Students']['instructions'];
        $contentArray['strategy']=$this->data['Students']['strategy'];
        $contentArray['script']=$this->data['Students']['script'];
        $contentArray['contentprovider']=$this->data['Students']['contentprovider'];
        $contentArray['contentprovider_status']=($this->data['Students']['contentprovider_status']==1)? '1' :'0';
        $contentArray['weblink']=$this->data['Students']['weblink'];
        $contentArray['weblink_status']=($this->data['Students']['weblink_status']==1)? '1' :'0';
        $contentArray['content_type']=$this->data['Students']['content_type'];//1:for text 2:for Image  3:for Vedio
        
        $contentArray['st_dt']=$this->data['Students']['st_dt'].' 00:00:00';
        $contentArray['end_dt']=$this->data['Students']['end_dt'].' 00:00:00';
        $contentArray['datetime']=date('Y-m-d H:i:s');
        $contentArray['ediv_type']=($this->data['Students']['type']==1)?10:11;

        if($this->data['Students']['content_type']==1){
            $contentArray['content_text']=$this->data['Students']['content_text'];
        }elseif($this->data['Students']['content_type']==2){// Image Process //
            $ext = explode('.',$this->data['Students']['image']['name']);
            $iname=$rval.'_'.time().'.'.$ext[1];
            @chmod(_ROOT_BASE_PATH."img/studentcontent/", 0777);
            if(move_uploaded_file($this->data['Students']['image']['tmp_name'] ,_ROOT_BASE_PATH."img/studentcontent/".$iname)){
                        $contentArray['image_video']=$iname;

                }
        }elseif($this->data['Students']['content_type']==3){// Video Process //
           
		   // echo $this->data['Students']['video']['size']." < 5242880";
		   
            if($this->data['Students']['video']['size'] > 5242880){ $flag =1;}
             $ext = explode('.',$this->data['Students']['video']['name']);
             $inamevideo=$rval.'_'.time().'.'.$ext[1];
             @chmod(_ROOT_BASE_PATH."img/studentcontent/", 0777);
             if($flag==0){
             if(move_uploaded_file($this->data['Students']['video']['tmp_name'] ,_ROOT_BASE_PATH."img/studentcontent/".$inamevideo)){
                   $contentArray['image_video']=$inamevideo;
              }

              }
        }
        if($flag==0){
       
        $this->Studentcontent->create();
        $this->Studentcontent->save($contentArray);
        
        // Processsing Question //
        $stdContentId = $this->Studentcontent->id;
        $questionArray = array();
        for($i=0;$i<count($this->params['form']);$i++){
            $questionArray['id']='';
            $questionArray['content_id']=$stdContentId;
            $questionArray['question'] = $this->params['form']['q'.($i+1)]['question'][0];
            $questionArray['datetime'] = date('Y-m-d H:i:s');
            $this->Studentquestion->save($questionArray);
            $valans = count($this->params['form']['q'.($i+1)]['answer']);
            
            $stdquizid = $this->Studentquestion->id;
            for($j=0;$j<$valans;$j++){
                $answerArray['id']='';
                $answerArray['qid']=$stdquizid;
                $answerArray['answer']=$this->params['form']['q'.($i+1)]['answer'][$j];
                if($this->data['Students']['type']==1){
                $answerArray['result']=($this->params['form']['q'.($i+1)]['result'][0]==$j)?1:0;
                }else{
                $answerArray['result']=0;
                }
                $answerArray['datetime'] = date('Y-m-d H:i:s');
                 
                $this->Studentanswer->save($answerArray);
            }
    
        }
		$this->Session->setFlash('New content uploaded');
		if($this->data['Students']['type']==1){
		$this->redirect('index');
		}else{
			$this->redirect('poll');
		}
        }else{
            $this->Session->setFlash('Video size should not be greater than 5 MB');
    	    if($this->data['Students']['type']==1){
			$this->redirect('studentcontent');
			}else{
			$this->redirect('studentcontent/2');
			}

        }
        
   }
}
/*_______________________________________________________________________________
* Method contentactivedeactive
* Purpose: admin can make deactive and active student content
* Parameter : id
*
* _______________________________________________________________________________
*/
function contentactivedeactive($id=NULL){
        	$this->checkadmin();
        	$a=0;
        $this->autoRender=false;
          if($this->RequestHandler->isAjax()){
             Configure::write('debug', 0);
            if(!empty($id)){
                $results=$this->Studentcontent->find('all',array('conditions'=>array('MD5(Studentcontent.id)' =>$id)));
                if($results[0]['Studentcontent']['status']=='1'){
                        $a = 'inactive_'.$id;
                                $data=array(
                                'status'=>0,
                                'id' =>$results[0]['Studentcontent']['id']);
                        }else{
                                $a = 'active_'.$id;
                                $data=array(
                                'status'=>1,
                                'id' =>$results[0]['Studentcontent']['id']);
                 }

                $this->Studentcontent->query("update student_contents set status = '".$data['status']."' where id = ".$data['id']);
                echo $a;
         	}

          }


}
/*_______________________________________________________________________________
* Method viewdetail
* Purpose: admin can make viewdetail
* Parameter : id
*
* _______________________________________________________________________________
*/
function viewdetail($id=NULL){
    $data = $this->Studentcontent->find('all',array('conditions'=>'id='.$id));
    $this->set('data',$data);
    $dataAns = $this->Studentanswer->find('all');
    $this->set('dataAns',$dataAns);
}

/*_______________________________________________________________________________
* Method editcontent
* Purpose: admin can edit content
* Parameter : id
*
* _______________________________________________________________________________
*/
function editcontent($id=NULL,$flg=NULL){
	$this->set('flg',$flg);
    $data = $this->Studentcontent->find('all',array('conditions'=>'id='.$id));
    $this->set('data',$data);
    $list = $this->Studentquestion->find('all',array('conditions'=>'content_id='.$id));
    $this->set('list',$list);
    
    if(!empty($this->data)){
        $this->Studentcontent->updateAll(array('Studentcontent.name'=>'"'.$this->data['Students']['name'].'"','Studentcontent.contentprovider'=>'"'.$this->data['Students']['contentprovider'].'"','Studentcontent.contentprovider_status'=>'"'.$this->data['Students']['contentprovider_status'].'"','Studentcontent.weblink'=>'"'.$this->data['Students']['weblink'].'"','Studentcontent.weblink_status'=>'"'.$this->data['Students']['weblink_status'].'"','Studentcontent.category'=>'"'.$this->data['Students']['category'].'"','Studentcontent.st_dt'=>'"'.$this->data['Students']['st_dt'].'"','Studentcontent.end_dt'=>'"'.$this->data['Students']['end_dt'].'"'),array('Studentcontent.id =' => $id));
        $tempint =array();
        $tempint['id']=$id;
        $tempint['instructions']=$this->data['Students']['instructions'];
        $tempint['strategy']=$this->data['Students']['strategy'];
        $tempint['script']=$this->data['Students']['script'];
        $this->Studentcontent->save($tempint);
        
        if($this->data['Students']['content_type']==1){
        $temp = array();
        $temp['id']=$id;
        $temp['content_text']=$this->data['Students']['content_text'];
        $this->Studentcontent->save($temp);
        	//$this->Studentcontent->updateAll(array('Studentcontent.content_text'=>'"'.$this->data['Students']['content_text'].'"'),array('Studentcontent.id =' => $id));
        }
        for($i=0;$i<count($this->params['form']);$i++){
            $k = $i+1;
            foreach($this->params['form']['q'.$k]['question'] as $key=>$val){
               $this->Studentquestion->updateAll(array('Studentquestion.question'=>'"'.$val.'"'),array('Studentquestion.id =' => $key));
               $this->Studentanswer->updateAll(array('Studentanswer.result'=>'0'),array('Studentanswer.qid =' => $key));
            }
            foreach($this->params['form']['q'.$k]['answer'] as $keyAns=>$valAns){
               $this->Studentanswer->updateAll(array('Studentanswer.answer'=>'"'.$valAns.'"'),array('Studentanswer.id =' => $keyAns));
            }
            if($this->data['Students']['type']==1){
            foreach($this->params['form']['q'.$k]['result'] as $keyRes=>$valRes){
               $this->Studentanswer->updateAll(array('Studentanswer.result'=>'1'),array('Studentanswer.id =' => $valRes));
             }
            }
        }
        $this->Session->setFlash('Sponsern content edit successfully');
       if($this->data['Students']['type']==1){
		$this->redirect('index');
		}else{
			$this->redirect('poll');
		}
    }
}

/*_______________________________________________________________________________
* Method editimagevedeo
* Purpose: admin can make viewdetail
* Parameter : id
*
* _______________________________________________________________________________
*/
function editimagevideo($id=NULL,$flg=NULL){
   $this->checkadmin();
   $data = $this->Studentcontent->find('all',array('fields'=>'Studentcontent.image_video,Studentcontent.id,Studentcontent.content_type','conditions'=>'id='.$id));
   $this->set('data',$data);
   $this->set('id',$id);
   $this->set('flg',$flg);
   
   if(!empty($this->data)){
       
       echo $data[0]['Studentcontent']['content_type'];
       
       if($data[0]['Studentcontent']['content_type']==2){
       $ext = explode('.', $this->data['Students']['image_video']['name']);
    		if($ext[1]=='jpeg' || $ext[1]=='jpeg' || $ext[1]=='JPEG' || $ext[1]=='png' || $ext[1]=='JPG' || $ext[1]=='jpg' || $ext[1]=='PNG' || $ext[1]=='GIF' || $ext[1]=='gif'){
    			$rval = $this->Usercomponent->imageGenerateKey();
                        $iname=$rval.'_'.time().'.'.$ext[1];
           	    
           	    if(move_uploaded_file($this->data['Students']['image_video']['tmp_name'] ,_ROOT_BASE_PATH."img/studentcontent/" . $iname)){
					@chmod(_ROOT_BASE_PATH."img/studentcontent/", 0777);
        				@chmod(_ROOT_BASE_PATH."img/studentcontent/" .$data[0]['Studentcontent']['image_video'], 0777);
        				@unlink(_ROOT_BASE_PATH."img/studentcontent/" .$data[0]['Studentcontent']['image_video']);
                        $this->Studentcontent->updateAll(array('Studentcontent.image_video' =>' "' .$iname.' " '), array('Studentcontent.id' => $id));
           	    	$this->Session->setFlash('Logo updated successfully');
    			$this->redirect('editcontent/'.$id.'/'.$flg);
           	    }

    		}else{
                    $this->Session->setFlash('Please upload [jpeg,png,gif]');
                    $this->redirect('editcontent/'.$id.'/'.$flg);
                }

       }elseif($data[0]['Studentcontent']['content_type']==3){
          
           $ext = explode('.', $this->data['Students']['image_video']['name']);
           
         
    		if($ext[1]=='mp3' || $ext[1]=='x-flv' || $ext[1]=='flv' || $ext[1]=='mp4'){
    			$rval = $this->Usercomponent->imageGenerateKey();
                        $iname=$rval.'_'.time().'.'.$ext[1];
                         if($this->data['Students']['image_video']['size'] < 5242880){
                        if(move_uploaded_file($this->data['Students']['image_video']['tmp_name'] ,_ROOT_BASE_PATH."img/studentcontent/" . $iname)){
					@chmod(_ROOT_BASE_PATH."img/studentcontent/", 0777);
        				@chmod(_ROOT_BASE_PATH."img/studentcontent/" .$data[0]['Studentcontent']['image_video'], 0777);
        				@unlink(_ROOT_BASE_PATH."img/studentcontent/" .$data[0]['Studentcontent']['image_video']);
                        $this->Studentcontent->updateAll(array('Studentcontent.image_video' =>' "' .$iname.' " '), array('Studentcontent.id' => $id));
           	    	$this->Session->setFlash('Video updated successfully');
    			$this->redirect('editcontent/'.$id.'/'.$flg);
           	    }
                     }else{
                         $this->Session->setFlash('Video size should not more than 5MB');
    			$this->redirect('editcontent/'.$id.'/'.$flg);
                     }

    		}else{
                    $this->Session->setFlash('Please upload [flv,mp4,mp3]');
                    $this->redirect('editcontent/'.$id.'/'.$flg);
            }
       }
   }


}
/*_______________________________________________________________________________
* Method studentoption
* Purpose: admin can make viewdetail
* Parameter : Null
*
* _______________________________________________________________________________
*/
function studentoption(){
    $this->checkuserlog();
    $this->layout='default';
    
}
/*_______________________________________________________________________________
* Method studentquiz
* Purpose: admin can make viewdetail
* Parameter : Null
*
* _______________________________________________________________________________
*/
function studentquiz($skipId=NULL){
   
   $this->checkuserlog();
    $this->layout='default';
    $userID=$this->Session->read('User.id');
	$newar= array();
	$questionplayed=array();
	$questionpool=array();
	/*
	 * Student played quiz with content ID 
	 */
	$playedquiz = $this->Studentuserattempt->find('all',array('fields'=>'qid','conditions'=>'uid ='.$userID));// User played
	for($pq=0;$pq<count($playedquiz);$pq++){
		$questionplayed[] = $playedquiz[$pq]['Studentuserattempt']['qid'];
	}
	/*
	 * Content on the day 
	 */
	$resultOne = $this->Studentcontent->find('all',array('conditions'=>'Studentcontent.st_dt <= NOW() AND NOW() <= Studentcontent.end_dt AND STATUS ="1" AND type =1'));
	
	for($i=0;$i<count($resultOne);$i++){
		for($j=0;$j<count($resultOne[$i]['Studentquestion']);$j++){
			$newar[]=$resultOne[$i]['Studentquestion'][$j]['id'];
		}
	}
	/*
	 * Required question for play
	 */
	$questionpool = array_values(array_diff($newar, $questionplayed));
	if($skipId!=''){
		$skipAr = explode(',',$skipId);
		$questionpool = array_values(array_diff($questionpool, $skipAr));
		$this->set('skipId',$skipId);
		if(empty($questionpool)){
			$this->set('noanotherquestion','noanotherquestion');
		}
	}
	if(!empty($questionpool)){
		$questionofday = $this->Studentquestion->find('all',array('conditions'=>'id ='.$questionpool[0]));// User played
		$resultcontent = $this->Studentcontent->find('all',array('conditions'=>'Studentcontent.id ='.$questionofday[0]['Studentquestion']['content_id']));
		
		$this->set('data',$resultcontent[0]['Studentcontent']);
		$this->set('questionofday',$questionofday[0]);
		
	}
	if(!empty($this->data)){
		        $qa = explode('_',$this->params['form']['answer']);
				$newdata['id'] = '';
				$newdata['type'] =$this->data['Students']['type'] ;
				$newdata['content_id'] =$this->data['Students']['content_id'] ;
				$newdata['content_type'] =$this->data['Students']['content_type'] ;
			    $newdata['qid'] =$qa[0];
				$newdata['aid'] =$qa[1];
				$newdata['uid'] =$userID;
	            $check = $this->Studentanswer->find('all',array('fields'=>'result','conditions'=>'id='.$qa[1]));
				if($check[0]['Studentanswer']['result']==1){
				$newdata['points'] = $this->get_ediv(10);
				$newdata['final_result']= 1;
				}else{
				$newdata['points'] = 0;
				$newdata['final_result']= '0';
				}
				$this->Studentuserattempt->save($newdata);
	            $checkexistuser = $this->Pausetempuser->find('all',array('conditions'=> 'uid='.$userID.' and qid='.$qa[0].' and aid='.$qa[1].' and type="quiz"'));
				  if(!empty($checkexistuser)){
			    	  $this->Pausetempuser->deleteAll(array('uid'=>$userID,'qid'=>$qa[0],'type'=>'quiz'));
				  }
				  if($check[0]['Studentanswer']['result']==1){
				 $this->ediv_user_masters->query("insert into ediv_user_masters (uid,ediv_type,points,datetime) values (".$userID.",10,".$this->get_ediv(10).",'".date('Y-m-d H:i:s')."')");
				  }
				if($skipId!=''){
				$this->redirect('quizresult/'.$qa[0].'/'.$skipId);
				}else{
					$this->redirect('quizresult/'.$qa[0]);
				}
		
	}
	
   
}

/*_______________________________________________________________________________
* Method quizresult
* Purpose: user can view quiz
* Parameter : Null
*
* _______________________________________________________________________________
*/
function quizresult($qid=NULL,$skipId=NULL){
    $this->checkuserlog();
    $this->layout='default';
    $userID=$this->Session->read('User.id');
	$newar= array();
	$questionplayed=array();
	$questionpool=array();
		
    	/*
		 * End checking of poll exist //
		 * Displaying result // of Poll
		 */
		
    	$questionofdayNext = $this->Studentquestion->find('all',array('conditions'=>'id ='.$qid));// User played
		$resultcontentNext = $this->Studentcontent->find('all',array('conditions'=>'Studentcontent.id ='.$questionofdayNext[0]['Studentquestion']['content_id']));
		
		$this->set('data',$resultcontentNext[0]['Studentcontent']);
		$this->set('questionofday',$questionofdayNext[0]);
		if($skipId!=NULL){
			$this->set('skipId',$skipId);
		}
		/////////// Checking for the next qid ///////////////
		/*
		* Student played quiz with content ID 
		*/
			$playedquiz = $this->Studentuserattempt->find('all',array('fields'=>'qid','conditions'=>'uid ='.$userID));// User played
			for($pq=0;$pq<count($playedquiz);$pq++){
				$questionplayed[] = $playedquiz[$pq]['Studentuserattempt']['qid'];
			}
		/*
		* Content on the day 
		*/
	$resultOne = $this->Studentcontent->find('all',array('conditions'=>'Studentcontent.st_dt <= NOW() AND NOW() <= Studentcontent.end_dt AND STATUS ="1" AND type =1'));
	
	for($i=0;$i<count($resultOne);$i++){
		for($j=0;$j<count($resultOne[$i]['Studentquestion']);$j++){
			$newar[]=$resultOne[$i]['Studentquestion'][$j]['id'];
		}
	}
	/*
	 * Required question for play
	 */
	$questionpool = array_values(array_diff($newar, $questionplayed));
	
	if(!empty($questionpool)){
		$this->set('resultnext',$questionpool);
	}
    
	
}
/*_______________________________________________________________________________
* Method pausecontentquiz
* Purpose: user can pause quiz
* Parameter : Null
*
* _______________________________________________________________________________
*/
function pausecontentquiz(){
	    	$temp=array();
             $this->autoRender=false;
             if($this->RequestHandler->isAjax()){
             Configure::write('debug', 0);
             $userID=$this->Session->read('User.id');
             
             		$val=explode('_',$this->params['form']['answer']);
             		$temp['id']='';
             		$temp['uid']=$userID;
             		$temp['qid']=$val[0];
             		$temp['aid']=$val[1];
             		$temp['type']='quiz';
             		$existpase=$this->Pausetempuser->find('all',array('conditions'=>array('uid'=>$userID,'qid'=>$val[0],'type'=>'quiz')));
             		if(!empty($existpase)){
             			$this->Pausetempuser->deleteAll(array('uid'=>$userID,'qid'=>$val[0],'type'=>'quiz'));
             		}
             		$this->Pausetempuser->save($temp);
             		
             }

          
}

/*_______________________________________________________________________________
* Method studentpoll
* Purpose: admin can make viewdetail
* Parameter : Null
*
* _______________________________________________________________________________
*/
function studentpoll($skipId=NULL){
    $this->checkuserlog();
    $this->layout='default';
    $userID=$this->Session->read('User.id');
	$newar= array();
	$questionplayed=array();
	$questionpool=array();
	/*
	 * Student played quiz with content ID 
	 */
	$playedquiz = $this->Studentuserattempt->find('all',array('fields'=>'qid','conditions'=>'uid ='.$userID));// User played
	for($pq=0;$pq<count($playedquiz);$pq++){
		$questionplayed[] = $playedquiz[$pq]['Studentuserattempt']['qid'];
	}
	/*
	 * Content on the day 
	 */
	$resultOne = $this->Studentcontent->find('all',array('conditions'=>'Studentcontent.st_dt <= NOW() AND NOW() <= Studentcontent.end_dt AND STATUS ="1" AND type =2'));
	
	for($i=0;$i<count($resultOne);$i++){
		for($j=0;$j<count($resultOne[$i]['Studentquestion']);$j++){
			$newar[]=$resultOne[$i]['Studentquestion'][$j]['id'];
		}
	}
	/*
	 * Required question for play
	 */
	$questionpool = array_values(array_diff($newar, $questionplayed));
	if($skipId!=''){
		$skipAr = explode(',',$skipId);
		$questionpool = array_values(array_diff($questionpool, $skipAr));
		$this->set('skipId',$skipId);
		if(empty($questionpool)){
			$this->set('noanotherquestion','noanotherquestion');
		}
	}
	if(!empty($questionpool)){
		$questionofday = $this->Studentquestion->find('all',array('conditions'=>'id ='.$questionpool[0]));// User played
		$resultcontent = $this->Studentcontent->find('all',array('conditions'=>'Studentcontent.id ='.$questionofday[0]['Studentquestion']['content_id']));
		
		$this->set('data',$resultcontent[0]['Studentcontent']);
		$this->set('questionofday',$questionofday[0]);
		
	}
	if(!empty($this->data)){
		        $qa = explode('_',$this->params['form']['answer']);
				$newdata['id'] = '';
				$newdata['type'] =$this->data['Students']['type'] ;
				$newdata['content_id'] =$this->data['Students']['content_id'] ;
				$newdata['content_type'] =$this->data['Students']['content_type'] ;
			    $newdata['qid'] =$qa[0];
				$newdata['aid'] =$qa[1];
				$newdata['uid'] =$userID;
				$newdata['final_result'] =0;
				$newdata['points'] =0;
				$this->Studentuserattempt->save($newdata);
	            $checkexistuser = $this->Pausetempuser->find('all',array('conditions'=> 'uid='.$userID.' and qid='.$qa[0].' and aid='.$qa[1].' and type="poll"'));
				  if(!empty($checkexistuser)){
			    	  $this->Pausetempuser->deleteAll(array('uid'=>$userID,'qid'=>$qa[0],'type'=>'poll'));
				  }
				 $this->ediv_user_masters->query("insert into ediv_user_masters (uid,ediv_type,points,datetime) values (".$userID.",11,".$this->get_ediv(11).",'".date('Y-m-d H:i:s')."')");
				if($skipId!=''){
				$this->redirect('pollresult/'.$qa[0].'/'.$skipId);
				}else{
					$this->redirect('pollresult/'.$qa[0]);
				}
		
	}
	
    
}
function pollresult($qid=NULL,$skipId=NULL){
	$this->checkuserlog();
    $this->layout='default';
    $userID=$this->Session->read('User.id');
	$newar= array();
	$questionplayed=array();
	$questionpool=array();
		
    	/*
		 * End checking of poll exist //
		 * Displaying result // of Poll
		 */
		
    	$questionofdayNext = $this->Studentquestion->find('all',array('conditions'=>'id ='.$qid));// User played
		$resultcontentNext = $this->Studentcontent->find('all',array('conditions'=>'Studentcontent.id ='.$questionofdayNext[0]['Studentquestion']['content_id']));
		
		$this->set('data',$resultcontentNext[0]['Studentcontent']);
		$this->set('questionofday',$questionofdayNext[0]);
		if($skipId!=NULL){
			$this->set('skipId',$skipId);
		}
		/////////// Checking for the next qid ///////////////
		/*
		* Student played quiz with content ID 
		*/
			$playedquiz = $this->Studentuserattempt->find('all',array('fields'=>'qid','conditions'=>'uid ='.$userID));// User played
			for($pq=0;$pq<count($playedquiz);$pq++){
				$questionplayed[] = $playedquiz[$pq]['Studentuserattempt']['qid'];
			}
		/*
		* Content on the day 
		*/
	$resultOne = $this->Studentcontent->find('all',array('conditions'=>'Studentcontent.st_dt <= NOW() AND NOW() <= Studentcontent.end_dt AND STATUS ="1" AND type =2'));
	
	for($i=0;$i<count($resultOne);$i++){
		for($j=0;$j<count($resultOne[$i]['Studentquestion']);$j++){
			$newar[]=$resultOne[$i]['Studentquestion'][$j]['id'];
		}
	}
	/*
	 * Required question for play
	 */
	$questionpool = array_values(array_diff($newar, $questionplayed));
	
	if(!empty($questionpool)){
		$this->set('resultnext',$questionpool);
	}
}

/*_______________________________________________________________________________
* Method pausecontentpoll
* Purpose: user can pause poll
* Parameter : Null
*
* _______________________________________________________________________________
*/
function pausecontentpoll(){
	    	$temp=array();
             $this->autoRender=false;
             if($this->RequestHandler->isAjax()){
             Configure::write('debug', 0);
             $userID=$this->Session->read('User.id');
             		$val=explode('_',$this->params['form']['answer']);
             		$temp['id']='';
             		$temp['uid']=$userID;
             		$temp['qid']=$val[0];
             		$temp['aid']=$val[1];
             		$temp['type']='poll';
             		$existpase=$this->Pausetempuser->find('all',array('conditions'=>array('uid'=>$userID,'qid'=>$val[0],'type'=>'poll')));
             		if(!empty($existpase)){
             			$this->Pausetempuser->deleteAll(array('uid'=>$userID,'qid'=>$val[0],'type'=>'poll'));
             		}
             		$this->Pausetempuser->save($temp);
             
          }
}

/*_______________________________________________________________________________
* Method cafeseries
* Purpose: admin can make list cafe series
* Parameter : Null
*
* _______________________________________________________________________________
*/
function cafeseries(){
	 $this->checkadmin();
    $this->Cafeseriescontent->find('all');
    $this->paginate['Cafeseriescontent']=array('page'=>1,'limit'=>25);
    $data = $this->paginate('Cafeseriescontent');
    $this->set('data',$data);
}
/*_______________________________________________________________________________
* Method vedioactivedeactive
* Purpose: admin active and deactive
* Parameter : Null
*
* _______________________________________________________________________________
*/
function vedioactivedeactive($id=NULL){
        	$this->checkadmin();
        	$a=0;
        $this->autoRender=false;
          if($this->RequestHandler->isAjax()){
             Configure::write('debug', 0);
            if(!empty($id)){
                $results=$this->Cafeseriescontent->find('all',array('conditions'=>array('MD5(Cafeseriescontent.id)' =>$id)));
                if($results[0]['Cafeseriescontent']['status']=='1'){
                        $a = 'inactive_'.$id;
                                $data=array(
                                'status'=>0,
                                'id' =>$results[0]['Cafeseriescontent']['id']);
                        }else{
                                $a = 'active_'.$id;
                                $data=array(
                                'status'=>1,
                                'id' =>$results[0]['Cafeseriescontent']['id']);
                 }

                $this->Cafeseriescontent->query("update cafeseries_contents set status = '".$data['status']."' where id = ".$data['id']);
                echo $a;
         	}

          }


}
/*_______________________________________________________________________________
* Method vedioviewdeatil
* Purpose: admin display information
* Parameter : Null
*
* _______________________________________________________________________________
*/
function vedioviewdetail($id=NULL){
    $data = $this->Cafeseriescontent->find('all',array('conditions'=>'id='.$id));
    $this->set('data',$data);
    $dataAns = $this->Cafeseriesanswer->find('all');
    $this->set('dataAns',$dataAns);
}
/*_______________________________________________________________________________
* Method cafeseries
* Purpose: admin can make new cafe series
* Parameter : Null
*
* _______________________________________________________________________________
*/
function newcafeseries(){

    $this->checkadmin();
    
    $contentArray = array();
     $flag = 0;
    $rval = $this->Usercomponent->imageGenerateKey();
    if(!empty($this->data)){
        $contentArray['id']='';
        $contentArray['name']=$this->data['Students']['name'];
        $contentArray['instructions']=$this->data['Students']['instructions'];
        $contentArray['strategy']=$this->data['Students']['strategy'];
        $contentArray['script']=$this->data['Students']['script'];
        $contentArray['contentprovider']=$this->data['Students']['contentprovider'];
        $contentArray['contentprovider_status']=($this->data['Students']['contentprovider_status']==1)? '1' :'0';
        $contentArray['weblink']=$this->data['Students']['weblink'];
        $contentArray['weblink_status']=($this->data['Students']['weblink_status']==1)? '1' :'0';
        $contentArray['st_dt']=$this->data['Students']['st_dt'].' 00:00:00';
        $contentArray['end_dt']=$this->data['Students']['end_dt'].' 00:00:00';
        $contentArray['datetime']=date('Y-m-d H:i:s');
        $contentArray['ediv_type']=12;

        // Video Process //
           
		   // echo $this->data['Students']['video']['size']." < 5242880";
		   
            if($this->data['Students']['video']['size'] > 5242880){ $flag =1;}
             $ext = explode('.',$this->data['Students']['video']['name']);
             $inamevideo=$rval.'_'.time().'.'.$ext[1];
             @chmod(_ROOT_BASE_PATH."img/studentcontent/", 0777);
             if($flag==0){
             if(move_uploaded_file($this->data['Students']['video']['tmp_name'] ,_ROOT_BASE_PATH."img/studentcontent/".$inamevideo)){
                   $contentArray['video']=$inamevideo;
              }
             }
        if($flag==0){ //: Every thing is ok//
        $this->Cafeseriescontent->create();
        $this->Cafeseriescontent->save($contentArray);
        
        // Processsing Question //
        $stdContentId = $this->Cafeseriescontent->id;
        $questionArray = array();
        for($i=0;$i<count($this->params['form']);$i++){
            $questionArray['id']='';
            $questionArray['cafeseries_id']=$stdContentId;
            $questionArray['question'] = $this->params['form']['q'.($i+1)]['question'][0];
            $questionArray['datetime'] = date('Y-m-d H:i:s');
            $this->Cafeseriesquestion->save($questionArray);
            $valans = count($this->params['form']['q'.($i+1)]['answer']);
            
            $stdquizid = $this->Cafeseriesquestion->id;
            for($j=0;$j<$valans;$j++){
                $answerArray['id']='';
                $answerArray['qid']=$stdquizid;
                $answerArray['answer']=$this->params['form']['q'.($i+1)]['answer'][$j];
                $answerArray['result']=($this->params['form']['q'.($i+1)]['result'][0]==$j)?1:0;
                $answerArray['datetime'] = date('Y-m-d H:i:s');
                 
                $this->Cafeseriesanswer->save($answerArray);
            }
    
        }
		$this->Session->setFlash('New content uploaded');
        $this->redirect('cafeseries');
        }else{
            $this->Session->setFlash('Video size should not be greater than 5 MB');
    	    

        }
        
   }
}
/*_______________________________________________________________________________
* Method videoeditcontent
* Purpose: admin can edit content
* Parameter : id
*
* _______________________________________________________________________________
*/
function videoeditcontent($id=NULL,$flg=NULL){
	$this->set('flg',$flg);
	
    $data = $this->Cafeseriescontent->find('all',array('conditions'=>'id='.$id));
    $this->set('data',$data);
    $list = $this->Cafeseriesquestion->find('all',array('conditions'=>'cafeseries_id='.$id));
    $this->set('list',$list);
    
    if(!empty($this->data)){
        $this->Cafeseriescontent->updateAll(array('Cafeseriescontent.name'=>'"'.$this->data['Students']['name'].'"','Cafeseriescontent.contentprovider'=>'"'.$this->data['Students']['contentprovider'].'"','Cafeseriescontent.contentprovider_status'=>'"'.$this->data['Students']['contentprovider_status'].'"','Cafeseriescontent.weblink'=>'"'.$this->data['Students']['weblink'].'"','Cafeseriescontent.weblink_status'=>'"'.$this->data['Students']['weblink_status'].'"','Cafeseriescontent.st_dt'=>'"'.$this->data['Students']['st_dt'].'"','Cafeseriescontent.end_dt'=>'"'.$this->data['Students']['end_dt'].'"'),array('Cafeseriescontent.id =' => $id));
        $tempint =array();
        $tempint['id']=$id;
        $tempint['instructions']=$this->data['Students']['instructions'];
        $tempint['strategy']=$this->data['Students']['strategy'];
        $tempint['script']=$this->data['Students']['script'];
        $this->Cafeseriescontent->save($tempint);
        
        for($i=0;$i<count($this->params['form']);$i++){
            $k = $i+1;
            foreach($this->params['form']['q'.$k]['question'] as $key=>$val){
               $this->Cafeseriesquestion->updateAll(array('Cafeseriesquestion.question'=>'"'.$val.'"'),array('Cafeseriesquestion.id =' => $key));
               $this->Cafeseriesanswer->updateAll(array('Cafeseriesanswer.result'=>'0'),array('Cafeseriesanswer.qid =' => $key));
            }
            foreach($this->params['form']['q'.$k]['answer'] as $keyAns=>$valAns){
               $this->Cafeseriesanswer->updateAll(array('Cafeseriesanswer.answer'=>'"'.$valAns.'"'),array('Cafeseriesanswer.id =' => $keyAns));
            }
           
            foreach($this->params['form']['q'.$k]['result'] as $keyRes=>$valRes){
               $this->Cafeseriesanswer->updateAll(array('Cafeseriesanswer.result'=>'1'),array('Cafeseriesanswer.id =' => $valRes));
             }
           
        }
        $this->Session->setFlash('Definition of day edit successfully');
        $this->redirect('cafeseries');
    }
}
/*_______________________________________________________________________________
* Method videoedit
* Purpose: admin can make videoedit
* Parameter : id
*
* _______________________________________________________________________________
*/
function videoedit($id=NULL,$flg=NULL){
   $this->checkadmin();
   $this->set('flg',$flg);
   $data = $this->Cafeseriescontent->find('all',array('fields'=>'Cafeseriescontent.video,Cafeseriescontent.id','conditions'=>'id='.$id));
   $this->set('data',$data);
   $this->set('id',$id);
   
   if(!empty($this->data)){
          
           
           $ext = explode('.', $this->data['Students']['image_video']['name']);
           
         if($ext[1]=='mp3' || $ext[1]=='x-flv' || $ext[1]=='flv' || $ext[1]=='mp4' || $ext[1]=='mpg' || $ext[1]=='wav'){
    			$rval = $this->Usercomponent->imageGenerateKey();
                        $iname=$rval.'_'.time().'.'.$ext[1];
                         if($this->data['Students']['image_video']['size'] < 5242880){
                        if(move_uploaded_file($this->data['Students']['image_video']['tmp_name'] ,_ROOT_BASE_PATH."img/studentcontent/".$iname)){
					    @chmod(_ROOT_BASE_PATH."img/studentcontent/", 0777);
        				@chmod(_ROOT_BASE_PATH."img/studentcontent/" .$data[0]['Cafeseriescontent']['video'], 0777);
        				@unlink(_ROOT_BASE_PATH."img/studentcontent/" .$data[0]['Cafeseriescontent']['video']);
                        $this->Cafeseriescontent->updateAll(array('Cafeseriescontent.video' =>' "'.$iname.'" '), array('Cafeseriescontent.id' => $id));
           	    	$this->Session->setFlash('Video updated successfully');
    			$this->redirect('videoeditcontent/'.$id.'/'.$flg);
           	    }
                     }else{
                         $this->Session->setFlash('Video size should not more than 5MB');
    			$this->redirect('videoeditcontent/'.$id.'/'.$flg);
                     }

    		}else{
                    $this->Session->setFlash('Please upload [flv,mp4,mp3]');
                    $this->redirect('videoeditcontent/'.$id.'/'.$flg);
            }
      
   }


}
/*_______________________________________________________________________________
* Method usercafeseries
* Purpose: user can attempt cafeseries viedio ;
* Parameter : id
*
* _______________________________________________________________________________
*/
function usercafeseries($skipId=NULL){
	
   $this->checkuserlog();
    $this->layout='default';
    $userID=$this->Session->read('User.id');
	$newar= array();
	$questionplayed=array();
	$questionpool=array();
	/*
	 * Student played quiz with content ID 
	 */
	$playedquiz = $this->Cafeseriesuserattempt->find('all',array('fields'=>'qid','conditions'=>'uid ='.$userID));// User played
	for($pq=0;$pq<count($playedquiz);$pq++){
		$questionplayed[] = $playedquiz[$pq]['Cafeseriesuserattempt']['qid'];
	}
	/*
	 * Content on the day 
	 */
	$resultOne = $this->Cafeseriescontent->find('all',array('conditions'=>'Cafeseriescontent.st_dt <= NOW() AND NOW() <= Cafeseriescontent.end_dt AND STATUS ="1"'));
	//pr($resultOne);
	for($i=0;$i<count($resultOne);$i++){
		for($j=0;$j<count($resultOne[$i]['Cafeseriesquestion']);$j++){
			$newar[]=$resultOne[$i]['Cafeseriesquestion'][$j]['id'];
		}
	}
	/*
	 * Required question for play
	 */
	//pr($newar);
	//pr($questionplayed);
	$questionpool = array_values(array_diff($newar, $questionplayed));
	if($skipId!=''){
		$skipAr = explode(',',$skipId);
		$questionpool = array_values(array_diff($questionpool, $skipAr));
		$this->set('skipId',$skipId);
		if(empty($questionpool)){
			$this->set('noanotherquestion','noanotherquestion');
		}
	}
	if(!empty($questionpool)){
		$questionofday = $this->Cafeseriesquestion->find('all',array('conditions'=>'id ='.$questionpool[0]));// User played
		$resultcontent = $this->Cafeseriescontent->find('all',array('conditions'=>'Cafeseriescontent.id ='.$questionofday[0]['Cafeseriesquestion']['cafeseries_id']));
		
		$this->set('data',$resultcontent[0]['Cafeseriescontent']);
		$this->set('questionofday',$questionofday[0]);
		
	}
	if(!empty($this->data)){
		        $qa = explode('_',$this->params['form']['answer']);
				$newdata['id'] = '';
				$newdata['cafeseries_id'] =$this->data['Students']['cafeseries_id'] ;
				$newdata['qid'] =$qa[0];
				$newdata['aid'] =$qa[1];
				$newdata['uid'] =$userID;
	            $check = $this->Cafeseriesanswer->find('all',array('fields'=>'result','conditions'=>'id='.$qa[1]));
		    if($check[0]['Cafeseriesanswer']['result']==1){
				$newdata['points'] = $this->get_ediv(12);
				$newdata['final_result']= 1;
				}else{
				$newdata['points'] = 0;
				$newdata['final_result']= '0';
				}
				$this->Cafeseriesuserattempt->save($newdata);
	            $checkexistuser = $this->Pausetempuser->find('all',array('conditions'=> 'uid='.$userID.' and qid='.$qa[0].' and aid='.$qa[1].' and type="video"'));
				  if(!empty($checkexistuser)){
			    	  $this->Pausetempuser->deleteAll(array('uid'=>$userID,'qid'=>$qa[0],'type'=>'video'));
				  }
				  if($check[0]['Cafeseriesanswer']['result']==1){
				 $this->ediv_user_masters->query("insert into ediv_user_masters (uid,ediv_type,points,datetime) values (".$userID.",12,".$this->get_ediv(12).",'".date('Y-m-d H:i:s')."')");
				  }
				if($skipId!=''){
				$this->redirect('videoresult/'.$qa[0].'/'.$skipId);
				}else{
					$this->redirect('videoresult/'.$qa[0]);
				}
		
	}

    
}
/*_______________________________________________________________________________
* Method pausecontentpoll
* Purpose: user can pause poll
* Parameter : Null
*
* _______________________________________________________________________________
*/
function pausevideoquiz(){
	    	$temp=array();
             $this->autoRender=false;
             if($this->RequestHandler->isAjax()){
             Configure::write('debug', 0);
             $userID=$this->Session->read('User.id');
             		$val=explode('_',$this->params['form']['answer']);
             		$temp['id']='';
             		$temp['uid']=$userID;
             		$temp['qid']=$val[0];
             		$temp['aid']=$val[1];
             		$temp['type']='video';
             		$existpase=$this->Pausetempuser->find('all',array('conditions'=>array('uid'=>$userID,'qid'=>$val[0],'type'=>'video')));
             		if(!empty($existpase)){
             			$this->Pausetempuser->deleteAll(array('uid'=>$userID,'qid'=>$val[0],'type'=>'video'));
             		}
             		$this->Pausetempuser->save($temp);
             		
               }

          
}
/*_______________________________________________________________________________
* Method videoresult
* Purpose: user can view video result
* Parameter : Null
*
* _______________________________________________________________________________
*/
function videoresult($qid=NULL,$skipId=NULL){
    
	$this->checkuserlog();
    $this->layout='default';
    $userID=$this->Session->read('User.id');
	$newar= array();
	$questionplayed=array();
	$questionpool=array();
		
    	/*
		 * End checking of poll exist //
		 * Displaying result // of Poll
		 */
		
    	$questionofdayNext = $this->Cafeseriesquestion->find('all',array('conditions'=>'id ='.$qid));// User played
		$resultcontentNext = $this->Cafeseriescontent->find('all',array('conditions'=>'Cafeseriescontent.id ='.$questionofdayNext[0]['Cafeseriesquestion']['cafeseries_id']));
		
		$this->set('data',$resultcontentNext[0]['Cafeseriescontent']);
		$this->set('questionofday',$questionofdayNext[0]);
		if($skipId!=NULL){
			$this->set('skipId',$skipId);
		}
		/////////// Checking for the next qid ///////////////
		/*
		* Student played quiz with content ID 
		*/
			$playedquiz = $this->Cafeseriesuserattempt->find('all',array('fields'=>'qid','conditions'=>'uid ='.$userID));// User played
			for($pq=0;$pq<count($playedquiz);$pq++){
				$questionplayed[] = $playedquiz[$pq]['Cafeseriesuserattempt']['qid'];
			}
		/*
		* Content on the day 
		*/
	$resultOne = $this->Cafeseriescontent->find('all',array('conditions'=>'Cafeseriescontent.st_dt <= NOW() AND NOW() <= Cafeseriescontent.end_dt AND STATUS ="1"'));
	
	for($i=0;$i<count($resultOne);$i++){
		for($j=0;$j<count($resultOne[$i]['Cafeseriesquestion']);$j++){
			$newar[]=$resultOne[$i]['Cafeseriesquestion'][$j]['id'];
		}
	}
	/*
	 * Required question for play
	 */
	$questionpool = array_values(array_diff($newar, $questionplayed));
	
	if(!empty($questionpool)){
		$this->set('resultnext',$questionpool);
	}
    
	
}

function languagelab(){
        $this->checkuserlog();
	$this->layout='default';
}
/*_______________________________________________________________________________
* Method deleteanswer
* Purpose: admin can delete answer
* Parameter : $conId=NULL,$baseId=NULL,$ansId=NULL
*
* _______________________________________________________________________________
*/
function deleteanswer($conId=NULL,$ansId=NULL){
	 	 $this->Cafeseriesanswer->delete($ansId);
		 $this->redirect('videoeditcontent/'.$conId);		
}
/*_______________________________________________________________________________
* Method addanswer
* Purpose: admin can delete answer
* Parameter : $conId=NULL,$baseId=NULL
*
* _______________________________________________________________________________
*/
function addanswer($conId=NULL,$qid=NULL){
	//$result = $this->Cafeseriesquestion->find('all',array('fields'=>'MAX(id) as id','conditions'=>'cafeseries_id='.$conId, 'group'=>'cafeseries_id'));	
	$temp=array();
	$temp['id']='';
	$temp['qid']=$qid;
	$this->Cafeseriesanswer->save($temp);
	$this->redirect('videoeditcontent/'.$conId);
	
}
/*_______________________________________________________________________________
* Method addquestion
* Purpose: admin can add Question
* Parameter : Null
*
* _______________________________________________________________________________
*/
function addquestion($conId=NULL){
	$temp=array();
	$temp['id']='';
	$temp['cafeseries_id']=$conId;
	$this->Cafeseriesquestion->save($temp);
	$ansQid = $this->Cafeseriesquestion->id;
	
	$tempAns=array();
	$tempAns['id']='';
	$tempAns['qid']=$ansQid;
	$tempAns['result']='1';
	$this->Cafeseriesanswer->save($tempAns);
	
	$this->redirect('videoeditcontent/'.$conId);
}
/*_______________________________________________________________________________
* Method addquestion
* Purpose: admin can add Question
* Parameter : Null
*
* _______________________________________________________________________________
*/
function removequestion($conId=NULL,$qid=NULL){
	
//	$result = $this->Cafeseriesquestion->find('all',array('fields'=>'MAX(id) as id','conditions'=>'cafeseries_id='.$conId, 'group'=>'cafeseries_id'));	
	$this->Cafeseriesanswer->deleteAll(array('Cafeseriesanswer.qid'=>$qid));//$result[0][0]['id'],$cascade = true);
	$this->Cafeseriesquestion->delete($qid);
	$this->redirect('videoeditcontent/'.$conId);
}

///////////////////////////////////////////
/*_______________________________________________________________________________
* Method deleteanswercon
* Purpose: admin can delete answer
* Parameter : $conId=NULL,$baseId=NULL,$ansId=NULL
*
* _______________________________________________________________________________
*/
function deleteanswercon($conId=NULL,$ansId=NULL){
	 	 $this->Studentanswer->delete($ansId);
		 $this->redirect('editcontent/'.$conId);		
}

/*_______________________________________________________________________________
* Method addanswercon
* Purpose: admin can delete answer
* Parameter : $conId=NULL,$baseId=NULL
*
* _______________________________________________________________________________
*/
function addanswercon($conId=NULL,$qid=NULL){
	//$result = $this->Studentquestion->find('all',array('fields'=>'MAX(id) as id','conditions'=>'content_id='.$conId, 'group'=>'content_id'));	
	$temp=array();
	$temp['id']='';
	$temp['qid']=$qid;
	$this->Studentanswer->save($temp);
	$this->redirect('editcontent/'.$conId);
	
}
/*_______________________________________________________________________________
* Method addquestion
* Purpose: admin can add Question
* Parameter : Null
*
* _______________________________________________________________________________
*/
function addquestioncon($conId=NULL){
	$temp=array();
	$temp['id']='';
	$temp['content_id']=$conId;
	$this->Studentquestion->save($temp);
	$ansQid = $this->Studentquestion->id;
	
	$tempAns=array();
	$tempAns['id']='';
	$tempAns['qid']=$ansQid;
	$tempAns['result']='1';
	$this->Studentanswer->save($tempAns);
	
	$this->redirect('editcontent/'.$conId);
}
/*_______________________________________________________________________________
* Method addquestion
* Purpose: admin can add Question
* Parameter : Null
*
* _______________________________________________________________________________
*/
function removequestioncon($conId=NULL,$qid=NULL){
	
	//$result = $this->Studentquestion->find('all',array('fields'=>'MAX(id) as id','conditions'=>'content_id='.$conId, 'group'=>'content_id'));	
	$this->Studentanswer->deleteAll(array('Studentanswer.qid'=>$qid));//$result[0][0]['id'],$cascade = true);
	$this->Studentquestion->delete($qid);
	$this->redirect('editcontent/'.$conId);
}






function dailyquiz(){
/* IMPORTANT QUESTIONS
*  TOEFL Questions User Play /
*/
$this->checkuserlog();
$userID=$this->Session->read('User.id');
$this->User->id=$userID;
$userinfo = $this->User->read();
$this->set('userinfo',$userinfo);
$dt = date('Y-m-d');
$resultQuizbtn = $this->Quizuserattempt->find('all',array('conditions'=>'uid='.$userID.' and date_format(played_date, "%Y-%m-%d") = "'.$dt.'"'));
//pr($resultQuizbtn);
if(count($resultQuizbtn)>0){
	$this->set('hide','hide');
}
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

    $this->layout='default';
}


function archives(){
/* IMPORTANT QUESTIONS
*  TOEFL Questions User Play /
*/
$this->checkuserlog();
$userID=$this->Session->read('User.id');
$this->User->id=$userID;
$userinfo = $this->User->read();
$this->set('userinfo',$userinfo);

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

// My Study Materials
$resultContentData = $this->Studentuserattempt->query("SELECT DATE_FORMAT(played_date, '%m/%d/%Y') AS DT, date_format(played_date, '%Y-%m-%d %H:%i') AS PD, content_id, qid, SUM(points) AS point FROM `student_user_attempts` where uid =".$this->Session->read('User.id')." AND type=1  GROUP BY content_id ORDER BY played_date DESC ");
	$studyData=array();
	for($i=0;$i<count($resultContentData);$i++){
            $studyData[$i]['DTT']=$resultContentData[$i][0]['PD'];
            $studyData[$i]['DT']=$resultContentData[$i][0]['DT'];
            $qid=$resultContentData[$i]['student_user_attempts']['qid'];
            $contentId = $resultContentData[$i]['student_user_attempts']['content_id'];
            $contentDetails = $this->Studentcontent->query("SELECT category, name, content_type FROM `student_contents` where id =".$contentId);
                if($contentDetails[0]['student_contents']['content_type']==1){
                   $contentType= 'Text';
                }else if($contentDetails[0]['student_contents']['content_type']==2){
                    $contentType= 'Image';
                }else{
                    $contentType= 'Video';
                }
            $studyData[$i]['contentLink']='/smartsed/students/archivequizresult/'.$qid.'/'.$contentId;
            $studyData[$i]['catName']=$contentDetails[0]['student_contents']['name'];
//            $studyData[$i]['catName']=$contentDetails[0]['student_contents']['name'].' ('.$contentDetails[0]['student_contents']['category'].'-'.$contentType.')';
            $studyData[$i]['point']=(strlen($resultContentData[$i][0]['point'])==5)? '0'.$resultContentData[$i][0]['point']:$resultContentData[$i][0]['point'];
	}
	$this->set('studyList',$studyData);

// Today's Concept
$resultConceptData = $this->Studentuserattempt->query("SELECT count(id) AS tot, DATE_FORMAT(played_date, '%m/%d/%Y') AS DT, date_format(played_date, '%Y-%m-%d %H:%i') AS PD, content_id, qid, SUM(points) AS point FROM `student_user_attempts` where uid =".$this->Session->read('User.id')." AND type=2  GROUP BY content_id ORDER BY played_date DESC ");
	$conceptData=array();
	for($i=0;$i<count($resultConceptData);$i++){
            $conceptData[$i]['DTT']=$resultConceptData[$i][0]['PD'];
            $conceptData[$i]['DT']=$resultConceptData[$i][0]['DT'];
            $qid=$resultConceptData[$i]['student_user_attempts']['qid'];
            $conceptId = $resultConceptData[$i]['student_user_attempts']['content_id'];
            $conceptDetails = $this->Studentcontent->query("SELECT category, name, content_type FROM `student_contents` where id =".$conceptId);
                if($conceptDetails[0]['student_contents']['content_type']==1){
                   $conceptType= 'Text';
                }else if($conceptDetails[0]['student_contents']['content_type']==2){
                    $conceptType= 'Image';
                }else{
                    $conceptType= 'Video';
                }
            $conceptData[$i]['conceptLink']='/smartsed/students/archivepollresult/'.$qid.'/'.$conceptId;
//            $conceptData[$i]['catName']=$conceptDetails[0]['student_contents']['name'].' ('.$conceptDetails[0]['student_contents']['category'].'-'.$conceptType.')';
            $conceptData[$i]['catName']=$conceptDetails[0]['student_contents']['name'];
            $conceptData[$i]['point']=$this->get_ediv(11)*$resultConceptData[$i][0]['tot'];
	}
	$this->set('conceptList',$conceptData);

// Cafe Series
$resultCafeData = $this->Cafeseriesuserattempt->query("SELECT DATE_FORMAT(played_date, '%m/%d/%Y') AS DT, date_format(played_date, '%Y-%m-%d %H:%i') AS PD, qid, cafeseries_id, SUM(points) AS point FROM `cafeseries_user_attempts` where uid =".$this->Session->read('User.id')." GROUP BY cafeseries_id ORDER BY played_date DESC ");
	$cafeData=array();
	for($i=0;$i<count($resultCafeData);$i++){
            $cafeData[$i]['DTT']=$resultCafeData[$i][0]['PD'];
            $cafeData[$i]['DT']=$resultCafeData[$i][0]['DT'];
            $qid=$resultCafeData[$i]['cafeseries_user_attempts']['qid'];
            $cafeId = $resultCafeData[$i]['cafeseries_user_attempts']['cafeseries_id'];
            $cafeDetails = $this->Cafeseriescontent->query("SELECT name FROM `cafeseries_contents` where id =".$cafeId);
            $cafeData[$i]['cafeLink']='/smartsed/students/archivevideoresult/'.$qid.'/'.$cafeId;
//            $cafeData[$i]['catName']=$cafeDetails[0]['cafeseries_contents']['name'];
            $cafeData[$i]['catName']=$cafeDetails[0]['cafeseries_contents']['name'];
            $cafeData[$i]['point']=(strlen($resultCafeData[$i][0]['point'])==5)? '0'.$resultCafeData[$i][0]['point']:$resultCafeData[$i][0]['point'];
	}
	$this->set('cafeList',$cafeData);


// Sponsored Content
$resultSponsoredData = $this->Sponsernuserattempt->query("SELECT DATE_FORMAT(played_date, '%m/%d/%Y') AS DT, date_format(played_date, '%Y-%m-%d %H:%i') AS PD, sponsern_content_id, qid, sponsern_id, SUM(points) AS point FROM `sponsern_user_attempts` where uid =".$this->Session->read('User.id')." GROUP BY sponsern_content_id ORDER BY played_date DESC ");
	$sponsoredData=array();
	for($i=0;$i<count($resultSponsoredData);$i++){
            $sponsoredData[$i]['DTT']=$resultSponsoredData[$i][0]['PD'];
            $sponsoredData[$i]['DT']=$resultSponsoredData[$i][0]['DT'];
            $sponsernContentId = $resultSponsoredData[$i]['sponsern_user_attempts']['sponsern_content_id'];
            $sponsernId = $resultSponsoredData[$i]['sponsern_user_attempts']['sponsern_id'];
            $sponsernContentId = $resultSponsoredData[$i]['sponsern_user_attempts']['sponsern_content_id'];
            $qid=$resultSponsoredData[$i]['sponsern_user_attempts']['qid'];
            $sponsernContentDetails = $this->Sponserncontent->query("SELECT content_name, content_type FROM `sponsern_contents` where id =".$sponsernContentId);
            $sponsernDetails = $this->Sponsern->query("SELECT company_name FROM `sponserns` where id =".$sponsernId);
            if($sponsernContentDetails[0]['sponsern_contents']['content_type']==1){
                   $conceptType= 'Text';
                }else if($sponsernContentDetails[0]['sponsern_contents']['content_type']==2){
                    $conceptType= 'Image';
                }else{
                    $conceptType= 'Video';
                }
            $sponsoredData[$i]['sponsoredLink']='/smartsed/students/archivesponsorresult/'.$qid.'/'.$sponsernId.'/'.$sponsernContentId;
//            $sponsoredData[$i]['catName']=$sponsernDetails[0]['sponserns']['company_name'].' ('.$sponsernContentDetails[0]['sponsern_contents']['content_name'].'-'.$conceptType.')';
            $sponsoredData[$i]['catName']=$sponsernContentDetails[0]['sponsern_contents']['content_name'];
            $sponsoredData[$i]['point']=(strlen($resultSponsoredData[$i][0]['point'])==5)? '0'.$resultSponsoredData[$i][0]['point']:$resultSponsoredData[$i][0]['point'];
	}
	$this->set('sponsoredList',$sponsoredData);

 // Replay Question //
        $recordPlay = $this->Quizuserattempt->find('all',array('fields'=>'Distinct(catid),uid','conditions'=>'uid ='.$this->Session->read('User.id'), 'limit'=>'1','order'=>'rand()'));
        $this->set('rePlay',$recordPlay[0]['Quizuserattempt']['catid']);


    $this->layout='default';
}


// My Study Materials details
function archivequizresult($qid=NULL,$contentId=NULL,$str=NULL){
    $this->checkuserlog();
    $this->layout='default';
    $userID=$this->Session->read('User.id');
	$newar= array();
	$questionplayed=array();
	$questionpool=array();
        if($str!=''){
            $strNext=$str.''.$qid.',';
            $strNotin=substr($strNext, 0, -1);
        }else{
         $strNext=$qid.',';
         $strNotin=$qid;
        }
        $questionofdayNext = $this->Studentquestion->find('all',array('conditions'=>'id ='.$qid));// User played
	$resultcontentNext = $this->Studentcontent->find('all',array('conditions'=>'Studentcontent.id ='.$questionofdayNext[0]['Studentquestion']['content_id']));
	$this->set('data',$resultcontentNext[0]['Studentcontent']);
	$this->set('questionofday',$questionofdayNext[0]);
		
		/////////// Checking for the next qid ///////////////
	$playedquiz = $this->Studentuserattempt->find('all',array('fields'=>'qid','conditions'=>'uid ='.$userID.' AND qid NOT IN ('.$strNotin.') AND content_id ='.$contentId));// User played
	if(count($playedquiz)>0){
		$questionpool=$playedquiz[0]['Studentuserattempt']['qid'];
	    }
	if(!empty($playedquiz)){
		$this->set('resultnext',$questionpool);
		$this->set('contentId',$contentId);
		$this->set('strNext',$strNext);
	}


}

function archivepollresult($qid=NULL,$contentId=NULL,$str=NULL){
	$this->checkuserlog();
        $this->layout='default';
        $userID=$this->Session->read('User.id');
	$newar= array();
	$questionplayed=array();
	$questionpool=array();

        if($str!=''){
            $strNext=$str.''.$qid.',';
            $strNotin=substr($strNext, 0, -1);
        }else{
         $strNext=$qid.',';
         $strNotin=$qid;
        }
        $questionofdayNext = $this->Studentquestion->find('all',array('conditions'=>'id ='.$qid));// User played
	$resultcontentNext = $this->Studentcontent->find('all',array('conditions'=>'Studentcontent.id ='.$questionofdayNext[0]['Studentquestion']['content_id']));
	$this->set('data',$resultcontentNext[0]['Studentcontent']);
	$this->set('questionofday',$questionofdayNext[0]);

		/////////// Checking for the next qid ///////////////
	$playedquiz = $this->Studentuserattempt->find('all',array('fields'=>'qid','conditions'=>'uid ='.$userID.' AND qid NOT IN ('.$strNotin.') AND content_id ='.$contentId));// User played
	if(count($playedquiz)>0){
		$questionpool=$playedquiz[0]['Studentuserattempt']['qid'];
	    }
	if(!empty($playedquiz)){
		$this->set('resultnext',$questionpool);
		$this->set('contentId',$contentId);
		$this->set('strNext',$strNext);
	}
    	
}

function archivevideoresult($qid=NULL,$contentId=NULL,$str=NULL){

	$this->checkuserlog();
        $this->layout='default';
        $userID=$this->Session->read('User.id');
	$newar= array();
	$questionplayed=array();
	$questionpool=array();

         if($str!=''){
            $strNext=$str.''.$qid.',';
            $strNotin=substr($strNext, 0, -1);
        }else{
         $strNext=$qid.',';
         $strNotin=$qid;
        }

    	$questionofdayNext = $this->Cafeseriesquestion->find('all',array('conditions'=>'id ='.$qid));// User played
	$resultcontentNext = $this->Cafeseriescontent->find('all',array('conditions'=>'Cafeseriescontent.id ='.$questionofdayNext[0]['Cafeseriesquestion']['cafeseries_id']));
        $this->set('data',$resultcontentNext[0]['Cafeseriescontent']);
	$this->set('questionofday',$questionofdayNext[0]);
		
		/////////// Checking for the next qid ///////////////
	$playedquiz = $this->Cafeseriesuserattempt->find('all',array('fields'=>'qid','conditions'=>'uid ='.$userID.' AND qid NOT IN ('.$strNotin.') AND cafeseries_id ='.$contentId));// User played
	if(count($playedquiz)>0){
		$questionpool=$playedquiz[0]['Cafeseriesuserattempt']['qid'];
	    }

        if(!empty($playedquiz)){
		$this->set('resultnext',$questionpool);
		$this->set('contentId',$contentId);
		$this->set('strNext',$strNext);
	}
}

function archivesponsorresult($qid=NULL,$sponsernId=NULL,$sponsernContentId=NULL,$str=NULL){

    $this->layout='default';
    $userID=$this->Session->read('User.id');
    $sponsernDetails = $this->Sponsern->query("SELECT company_name FROM `sponserns` where id =".$sponsernId);
    $this->set('companyName',$sponsernDetails[0]['sponserns']['company_name']);
	$newar= array();
	$questionplayed=array();
	$questionpool=array();
        if($str!=''){
            $strNext=$str.''.$qid.',';
            $strNotin=substr($strNext, 0, -1);
        }else{
         $strNext=$qid.',';
         $strNotin=$qid;
        }
    	$questionofdayNext = $this->Sponsernquestion->find('all',array('conditions'=>'id ='.$qid));// User played
	$resultcontentNext = $this->Sponserncontent->find('all',array('conditions'=>'Sponserncontent.id ='.$questionofdayNext[0]['Sponsernquestion']['sponsern_content_id']));
        $this->set('data',$resultcontentNext[0]['Sponserncontent']);
	$this->set('questionofday',$questionofdayNext[0]);

	$playedquiz = $this->Sponsernuserattempt->find('all',array('fields'=>'qid','conditions'=>'uid ='.$userID.' AND qid NOT IN ('.$strNotin.') AND sponsern_id ='.$sponsernId.' AND sponsern_content_id='.$sponsernContentId));// User played
	//echo "<pre>";print_r($playedquiz);
        if(count($playedquiz)>0){
		$questionpool=$playedquiz[0]['Sponsernuserattempt']['qid'];
	    }

	if(!empty($playedquiz)){
		$this->set('resultnext',$questionpool);
		$this->set('sponsernId',$sponsernId);
		$this->set('sponsernContentId',$sponsernContentId);
		$this->set('strNext',$strNext);
	}
}
function updatecafeanswer($id=NULL){
	$this->autoRender=false;
          if($this->RequestHandler->isAjax()){
             Configure::write('debug', 0);
             $q='q'.$id;
             echo $q;
             echo '<pre>';
             print_r($_REQUEST[$q]);
			 foreach($_REQUEST[$q]['question'] as $key=>$val){
			 	echo $val.'___'.$key;
             	$this->Cafeseriesquestion->updateAll(array('Cafeseriesquestion.question'=>'"'.$val.'"'),array('Cafeseriesquestion.id'=>$key));
             }
             foreach($_REQUEST[$q]['answer'] as $anskey=>$ansval){
             	$this->Cafeseriesanswer->updateAll(array('Cafeseriesanswer.answer'=>'"'.$ansval.'"'),array('Cafeseriesanswer.id'=>$anskey));
             }
              $this->Cafeseriesanswer->updateAll(array('Cafeseriesanswer.result'=>'1'),array('Cafeseriesanswer.id'=>$_REQUEST[$q]['result'][0]));
          }
}
function updatepollanswer($id=NULL){
	$this->autoRender=false;
          if($this->RequestHandler->isAjax()){
             Configure::write('debug', 0);
             $q='q'.$id;
            
			 foreach($_REQUEST[$q]['question'] as $key=>$val){
			 	$this->Studentquestion->updateAll(array('Studentquestion.question'=>'"'.$val.'"'),array('Studentquestion.id'=>$key));
             }
             foreach($_REQUEST[$q]['answer'] as $anskey=>$ansval){
             	$this->Studentanswer->updateAll(array('Studentanswer.answer'=>'"'.$ansval.'"'),array('Studentanswer.id'=>$anskey));
             }
             // $this->Studentanswer->updateAll(array('Studentanswer.result'=>'1'),array('Studentanswer.id'=>$_REQUEST[$q]['result'][0]));
          }
}
function updateconanswer($id=NULL){
	$this->autoRender=false;
          if($this->RequestHandler->isAjax()){
             Configure::write('debug', 0);
             $q='q'.$id;
            
			 foreach($_REQUEST[$q]['question'] as $key=>$val){
			 	$this->Studentquestion->updateAll(array('Studentquestion.question'=>'"'.$val.'"'),array('Studentquestion.id'=>$key));
             }
             foreach($_REQUEST[$q]['answer'] as $anskey=>$ansval){
             	$this->Studentanswer->updateAll(array('Studentanswer.answer'=>'"'.$ansval.'"'),array('Studentanswer.id'=>$anskey));
             }
              $this->Studentanswer->updateAll(array('Studentanswer.result'=>'1'),array('Studentanswer.id'=>$_REQUEST[$q]['result'][0]));
          }
}
function copysponsern($id=NULL){
	    $contentArray['id']='';
        $contentArray['name']=$this->data['Students']['name'];
        $contentArray['instructions']=$this->data['Students']['instructions'];
        $contentArray['strategy']=$this->data['Students']['strategy'];
        $contentArray['script']=$this->data['Students']['script'];
        $contentArray['contentprovider']=$this->data['Students']['contentprovider'];
        $contentArray['contentprovider_status']=($this->data['Students']['contentprovider_status']==1)? '1' :'0';
        $contentArray['weblink']=$this->data['Students']['weblink'];
        $contentArray['weblink_status']=($this->data['Students']['weblink_status']==1)? '1' :'0';
        $contentArray['st_dt']=$this->data['Students']['st_dt'].' 00:00:00';
        $contentArray['end_dt']=$this->data['Students']['end_dt'].' 00:00:00';
        $contentArray['datetime']=date('Y-m-d H:i:s');
        $contentArray['ediv_type']=12;
         $imagevideo = $this->Cafeseriescontent->find('all',array('fields'=>'video','conditions'=>'id='.$id));
        	$file = $imagevideo[0]['Cafeseriescontent']['video'];
        	$ext = explode('.',$file);
        	$newfile = md5(date('Y-m-d H:i:s')).'.'.$ext[1];
            @chmod(_ROOT_BASE_PATH."img/studentcontent/", 0777);
        	if (!copy(_ROOT_BASE_PATH."img/studentcontent/".$file, _ROOT_BASE_PATH."img/studentcontent/".$newfile)) {
    			//
			}else{
				$contentArray['video']=$newfile;
			}
			$this->Cafeseriescontent->save($contentArray);
            $stdContentId = $this->Cafeseriescontent->id;
            $questionArray = array();
         for($i=0;$i<count($this->params['form']);$i++){
            $k = $i+1;
            foreach($this->params['form']['q'.$k]['question'] as $key=>$val){
               $questionArray['id']='';
               $questionArray['cafeseries_id']=$stdContentId;
               $questionArray['question'] = $val;
               $questionArray['datetime'] = date('Y-m-d H:i:s');
               $this->Cafeseriesquestion->save($questionArray);	
             foreach($this->params['form']['q'.$k]['answer'] as $keyAns=>$valAns){
               $answerArray['id']='';
               $answerArray['qid'] = $this->Cafeseriesquestion->id;
               $answerArray['answer']=$valAns;
               $answerArray['datetime'] = date('Y-m-d H:i:s');
               $answerArray['result']=($this->params['form']['q'.$k]['result'][0]==$keyAns)?'1':'0';
               $this->Cafeseriesanswer->save($answerArray);
             }
           }
           
        }
            $this->Session->setFlash('New Copyed content');
            $this->redirect('videoeditcontent/'.$stdContentId);
}

function copystudentcontent($id=NULL){
	    $contentArray['id']='';
        $contentArray['type']=$this->data['Students']['type'];// 1:for Student Quiz  2:for Student Poll
        $contentArray['category']=$this->data['Students']['category'];
        $contentArray['name']=$this->data['Students']['name'];
        $contentArray['instructions']=$this->data['Students']['instructions'];
        $contentArray['strategy']=$this->data['Students']['strategy'];
        $contentArray['script']=$this->data['Students']['script'];
        $contentArray['contentprovider']=$this->data['Students']['contentprovider'];
        $contentArray['contentprovider_status']=($this->data['Students']['contentprovider_status']==1)? '1' :'0';
        $contentArray['weblink']=$this->data['Students']['weblink'];
        $contentArray['weblink_status']=($this->data['Students']['weblink_status']==1)? '1' :'0';
        $contentArray['content_type']=$this->data['Students']['content_type'];//1:for text 2:for Image  3:for Vedio
        
        $contentArray['st_dt']=$this->data['Students']['st_dt'].' 00:00:00';
        $contentArray['end_dt']=$this->data['Students']['end_dt'].' 00:00:00';
        $contentArray['datetime']=date('Y-m-d H:i:s');
        $contentArray['ediv_type']=($this->data['Students']['type']==1)?10:11;
        if($this->data['Students']['content_type']==1){
            $contentArray['content_text']=$this->data['Students']['content_text'];
        }elseif($this->data['Students']['content_type']==2 || $this->data['Students']['content_type']==3){
        $imagevideo = $this->Studentcontent->find('all',array('fields'=>'image_video','conditions'=>'id='.$id));
        	$file = $imagevideo[0]['Studentcontent']['image_video'];
        	$ext = explode('.',$file);
        	$newfile = md5(date('Y-m-d H:i:s')).'.'.$ext[1];
            @chmod(_ROOT_BASE_PATH."img/studentcontent/", 0777);
        	if (!copy(_ROOT_BASE_PATH."img/studentcontent/".$file, _ROOT_BASE_PATH."img/studentcontent/".$newfile)) {
    			//
			}else{
				$contentArray['image_video']=$newfile;
			}
        }
        $this->Studentcontent->create();
        $this->Studentcontent->save($contentArray);
        // Processsing Question //
        $stdContentId = $this->Studentcontent->id;
        $questionArray = array();
         for($i=0;$i<count($this->params['form']);$i++){
            $k = $i+1;
            foreach($this->params['form']['q'.$k]['question'] as $key=>$val){
               $questionArray['id']='';
               $questionArray['content_id']=$stdContentId;
               $questionArray['question'] = $val;
               $questionArray['datetime'] = date('Y-m-d H:i:s');
               $this->Studentquestion->save($questionArray);	
             foreach($this->params['form']['q'.$k]['answer'] as $keyAns=>$valAns){
               $answerArray['id']='';
               $answerArray['qid'] = $this->Studentquestion->id;
               $answerArray['answer']=$valAns;
               $answerArray['datetime'] = date('Y-m-d H:i:s');
               $answerArray['result']=($this->params['form']['q'.$k]['result'][0]==$keyAns)?'1':'0';
               $this->Studentanswer->save($answerArray);
             }
           }
        }
           $this->Session->setFlash('New Copyed content');
            $this->redirect('editcontent/'.$stdContentId);
        
        
}

// End Of the function for students_controller Class
}// End class

?>
