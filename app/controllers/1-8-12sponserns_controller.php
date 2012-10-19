<?php
/* 
#################################################################################
#																				#
#   Admin Charity Controller 													#
#   file name        	: sponserns_controller.php								#
#   Developed        	: Meridian Radiology 									#	
#   Intialised Variables: name , helpers ,components , uses , layout = (admin)  #
#																				#
#																				#
#################################################################################
*/
class SponsernsController extends AppController {

 	    var $name = 'Sponserns';
        var $uses = array('ediv_user_masters','Sponsern','Sponserncontent','Sponsernquestion','Sponsernanswer','Sponsernuserattempt','Pausetempuser');
        var $components = array('Usercomponent', 'Session','Image','Email','RequestHandler');
        var $helpers = array('Html', 'Form','Javascript','Ajax','Fck');
        var $layout = 'admin';
    
/*___________________________________________________________________________________________________
* 
* Method     : sponsersnlist
* Purpose    : making list of the sponserns
* Parameters : None
* 
* ___________________________________________________________________________________________________
*/	 
function index(){
	   $this->layout = 'admin';
   	   // Check admin athontication // 
		$this->checkadmin();
		$this->Sponsern->find('all');
		$this->paginate = array(
                         'limit' => 25,
                         'page'=>1,
                         'order'=>'Sponsern.index DESC'
    				 );
	     $data = $this->paginate('Sponsern');
         $this->set('data',$data);
}
/*___________________________________________________________________________________________________
* 
* Method     : newsponsern
* Purpose    : making list of the sponserns
* Parameters : None
* 
* ___________________________________________________________________________________________________
*/ 
function newsponsern(){
		 $this->layout = 'admin';
   	     $this->checkadmin();
	        $this->Sponsern->set($this->data);
	       		 if($this->Sponsern->validates()) {
	        		list($width, $height, $type, $attr) = getimagesize($this->data['Sponsern']['logo']['tmp_name']);
	        		if($width<=550 && $height<=450){
	        			
	        		    $rval = $this->Usercomponent->imageGenerateKey();

                                    $ext = explode('.',$this->data['Sponsern']['logo']['name']);
                                    $iname=$rval.'_'.time().'.'.$ext[1];
           	 			if(move_uploaded_file($this->data['Sponsern']['logo']['tmp_name'] ,_ROOT_BASE_PATH."img/sponsern/".$iname)){
           	 				$this->data['Sponsern']['logo'] = $iname;
           	 				$indexing = $this->Sponsern->find('all');
           	 				$this->data['Sponsern']['index']=count($indexing)+1;
           	 				$this->Sponsern->updateAll(array('Sponsern.sponserd' => '0'));
           	 				$this->Sponsern->save($this->data);
           	 				$this->redirect('index');
    		}
	        				
		}else{
		$this->Session->setFlash("Image diamention is greater than 550 X 450");
	}
	       		 		
  }
}
/*___________________________________________________________________________________________________
* 
* Method     : editsponsern
* Purpose    : making updation sponserns
* Parameters : None
* 
* ___________________________________________________________________________________________________
*/
function editsponsern($id=NULL){

 $this->Sponsern->id = $id;
       if (empty($this->data)) {
          $this->data = $this->Sponsern->read();
          $this->set('id',$id);
       }else{
       	       $data = array(
       	       			'id'=>$this->data['Sponsern']['id'],
       	                'company_name'=>$this->data['Sponsern']['company_name'],
       	       			'description'=>$this->data['Sponsern']['description']
       	       );
       	        $this->Sponsern->save($data,$validate = false);
		    	$this->Session->setFlash('Your post has been updated.');
				$this->redirect('index');
		 
       }
	
	
}
/*
_______________________________________________________________________________
* Method sponsernactivedeactive
* Purpose: admin can make deactive the user  
* Parameter : None
* 
* _______________________________________________________________________________
*/
function sponsernactivedeactive($id=NULL,$page=NULL){
        	$this->checkadmin();
        	$a=0;
        $this->autoRender=false;
          if($this->RequestHandler->isAjax()){
             Configure::write('debug', 0);
			   	if(!empty($id)){
				    $results=$this->Sponsern->find('all',array('conditions'=>array('MD5(Sponsern.id)' =>$id)));
				    	if($results[0]['Sponsern']['status']=='1'){
				    		$a = 'inactive_'.$id;
							$data=array(
							'status'=>0,
							'id' =>$results[0]['Sponsern']['id']);
						}else{
							$a = 'active_'.$id;
							$data=array(
							'status'=>1,
							'id' =>$results[0]['Sponsern']['id']);
						}
			   			
						$this->Sponsern->query("update sponserns set status = '".$data['status']."' where id = ".$data['id']);
						echo $a;
										
					}

          }	
   	
   	
   }
 /*_______________________________________________________________________________	
	* Method indexdown
 	* Purpose: admin can make new charity  
 	* Parameter : None
 	* 
 	* ______________________________________________________________________________
 	*/
	function indexdown($id=NULL){
		$result=$this->Sponsern->find('all',array('conditions'=>'Sponsern.id='.$id,'fields'=>'index'));
		echo $result[0]['Sponsern']['index'];
		$newindex =$result[0]['Sponsern']['index']-1;
		echo $newindex;
		$commer = $this->Sponsern->find('all',array('conditions'=>'Sponsern.index='.$newindex,'fields'=>'id,index')); 
		echo $commer[0]['Sponsern']['index'];
		$commerindex=$commer[0]['Sponsern']['index']+1;
		$newId = $commer[0]['Sponsern']['id'];
		$this->Sponsern->updateAll(array('Sponsern.index' =>$newindex ),array('Sponsern.id'=>$id));
		$this->Sponsern->updateAll(array('Sponsern.index' =>$commerindex ),array('Sponsern.id'=>$newId));
		$this->redirect('index');
	}
 /*_______________________________________________________________________________	
	* Method indexdown
 	* Purpose: admin can make new charity  
 	* Parameter : None
 	* 
 	* ______________________________________________________________________________
 	*/
	function indexup($id=NULL){
		$result=$this->Sponsern->find('all',array('conditions'=>'Sponsern.id='.$id,'fields'=>'index'));
		$newindex =$result[0]['Sponsern']['index']+1;
		$commer = $this->Sponsern->find('all',array('conditions'=>'Sponsern.index='.$newindex,'fields'=>'id,index')); 
		$commerindex=$commer[0]['Sponsern']['index']-1;
		$newId = $commer[0]['Sponsern']['id'];
		$this->Sponsern->updateAll(array('Sponsern.index' =>$newindex ),array('Sponsern.id'=>$id));
		$this->Sponsern->updateAll(array('Sponsern.index' =>$commerindex ),array('Sponsern.id'=>$newId));
		
		$this->redirect('index');
	}
/*
_______________________________________________________________________________
* Method sponceractivedeactive
* Purpose: admin can make active to sponcern  
* Parameter : None
* _______________________________________________________________________________
*/         
function sponceractivedeactive($id=NULL){
	$this->Sponsern->updateAll(array('Sponsern.sponserd' => '0'));
	$this->Sponsern->updateAll(array('Sponsern.sponserd' => '1'),array('Sponsern.id'=>$id));
	$this->redirect('index');
}
         
	
	
	
	
 /*_______________________________________________________________________________	
	* Method editsponsernimage
 	* Purpose: admin can edit Image
 	* Parameter : None
 	* 
 	* ______________________________________________________________________________
 	*/   
function editsponsernimage($id=NULL){
	
if(isset($_FILES['data']['name']['Sponsern']['logo']) && $_FILES['data']['name']['Sponsern']['logo']!=''){
    		
    		$ext = explode('/', $_FILES['data']['type']['Sponsern']['logo']);
    				if($ext[1]=='jpeg' || $ext[1]=='jpeg' || $ext[1]=='JPEG' || $ext[1]=='png' || $ext[1]=='JPG' || $ext[1]=='jpg' || $ext[1]=='PNG' || $ext[1]=='GIF' || $ext[1]=='gif'){
		    			$rval = $this->Usercomponent->imageGenerateKey();
                                     $extn = explode('.',$_FILES['data']['name']['Sponsern']['logo']);
                                     $iname=$rval.'_'.time().'.'.$extn[1];
		           	    
		           	    list($width, $height, $type, $attr) = getimagesize($this->data['Sponsern']['logo']['tmp_name']);
		           	    //echo $width.'<=550 && '.$height.'<=450';
                                    if($width<=550 && $height<=450){
				           	    if(move_uploaded_file($_FILES['data']['tmp_name']['Sponsern']['logo'] ,_ROOT_BASE_PATH."img/sponsern/".$iname)){
									
									@chmod(_ROOT_BASE_PATH."img/sponsern/", 0777);
									
									@chmod(_ROOT_BASE_PATH."img/sponsern/" .$imName, 0777);
									
									$delIm = $this->Sponsern->find('all',array('conditions'=>$id,'fields'=>'logo'));
									
									
									
									@unlink(_ROOT_BASE_PATH."img/sponsern/" .$delIm[0]['Sponsern']['logo']);
										
									
									
				                    $this->Sponsern->updateAll(array('Sponsern.logo' =>'"'.$iname.'"'), array('Sponsern.id' => $id));         	    	
				           	    	$this->Session->setFlash('Logo updated successfully');
				    			    $this->redirect('editsponsern/'.$id);
				           	    }
		           	    }else{

		           	    		$this->Session->setFlash('File should not be above than 550 X 450');
    							$this->redirect('editsponsern/'.$id);
    			
		           	    }
		           	  
		    			
    					}else{
    					$this->Session->setFlash('Invalid file type');
    					$this->redirect('editsponsern/'.$id);
    			
    				}
    		
    	}
		else{
			
	    $this->layout = '';
    	$results=$this->Sponsern->find('all',array('conditions'=>array('id' =>$id)));
    	
    	$this->set('result',$results[0]);
	
		}
}
/*_______________________________________________________________________________	
* Method sponsernquestions
 * Purpose: admin can edit Image
 * Parameter : None
 * 
 * ______________________________________________________________________________
 */ 
function sponsernquestions($id=NULL){
	$this->layout = 'admin';
	$calender = $this->Sponserncontent->find('all');
	$answer = $this->Sponsernquestion->find('all');
	// For Total //
	$result = $this->Sponsernanswer->query("SELECT COUNT(id) FROM `sponsern_answers` GROUP BY qid ORDER BY ID ASC LIMIT 1");
	    $this->set('calender',$calender);
        $this->set('answer',$answer);
        $this->set('result',$result[0][0]);
   	    // Check admin athontication // 
		$this->checkadmin();
		$this->Sponserncontent->find('all');
		$this->paginate = array(
                         'limit' => 25,
                         'page'=>1,
		                 'conditions'=>'sponsern_id='.$id
                         
    				 );
	     $data = $this->paginate('Sponserncontent');
         $this->set('data',$data);
         $this->set('id',$id);
}
/*_______________________________________________________________________________	
* Method newsponsernquestion
 * Purpose: admin can enter new sponsern question
 * Parameter : None
 * 
 * ______________________________________________________________________________
 */ 
function newsponsernquestion($id=NULL){
	
	$this->checkadmin();
	$this->set('id',$id);
	$contentArray = array();
     $flag = 0;
    $rval = $this->Usercomponent->imageGenerateKey();
	if(!empty($this->data)){
		//pr($this->data);
	//exit;
	$contentArray['id']='';
        $contentArray['content_type']=$this->data['Sponsern']['content_type'];//1:for text 2:for Image  3:for Vedio
        $contentArray['content_name']=$this->data['Sponsern']['content_name'];
        $contentArray['instructions']=$this->data['Sponsern']['instructions'];
        $contentArray['script']=$this->data['Sponsern']['script'];
        $contentArray['contentprovider']=$this->data['Sponsern']['contentprovider'];
        $contentArray['contentprovider_status']=($this->data['Sponsern']['contentprovider_status']==1)? '1' :'0';
        $contentArray['weblink']=$this->data['Sponsern']['weblink'];
        $contentArray['weblink_status']=($this->data['Sponsern']['weblink_status']==1)? '1' :'0';
        $contentArray['strategy']=$this->data['Sponsern']['strategy'];
	$contentArray['sponsern_id']=$id;
        $contentArray['st_dt']=$this->data['Sponsern']['st_dt'].' 00:00:00';
        $contentArray['end_dt']=$this->data['Sponsern']['end_dt'].' 00:00:00';
        $contentArray['datetime']=date('Y-m-d H:i:s');
        $contentArray['ediv_type']=9;
	    /// Process of content /////
		if($this->data['Sponsern']['content_type']==1){
	            $contentArray['text']=$this->data['Sponsern']['content_text'];
	        }elseif($this->data['Sponsern']['content_type']==2){// Image Process //
	            $ext = explode('.',$this->data['Sponsern']['image']['name']);
	            $iname=$rval.'_'.time().'.'.$ext[1];
	            @chmod(_ROOT_BASE_PATH."img/sponsern/", 0777);
	            if(move_uploaded_file($this->data['Sponsern']['image']['tmp_name'] ,_ROOT_BASE_PATH."img/sponsern/".$iname)){
	                 $contentArray['image_video']=$iname;
	                }
	        }elseif($this->data['Sponsern']['content_type']==3){// Video Process //
	            if($this->data['Sponsern']['video']['size'] > 5242880){ $flag =1;}
	             $ext = explode('.',$this->data['Sponsern']['video']['name']);
	             $inamevideo=$rval.'_'.time().'.'.$ext[1];
	             @chmod(_ROOT_BASE_PATH."img/studentcontent/", 0777);
	             if($flag==0){
	                 if(move_uploaded_file($this->data['Sponsern']['video']['tmp_name'] ,_ROOT_BASE_PATH."img/sponsern/".$inamevideo)){
	                   $contentArray['image_video']=$inamevideo;
	                  }
	              }
	        }
	  if($flag==0){
        $this->Sponserncontent->create();
        $this->Sponserncontent->save($contentArray);
        // Processsing Question //
        $stdContentId = $this->Sponserncontent->id;
        $questionArray = array();
        for($i=0;$i<count($this->params['form']);$i++){
            $questionArray['id']='';
            $questionArray['sponsern_content_id']=$stdContentId;
            $questionArray['question'] = $this->params['form']['q'.($i+1)]['question'][0];
            $questionArray['datetime'] = date('Y-m-d H:i:s');
            $this->Sponsernquestion->save($questionArray);
            $valans = count($this->params['form']['q'.($i+1)]['answer']);
            $stdquizid = $this->Sponsernquestion->id;
            for($j=0;$j<$valans;$j++){
                $answerArray['id']='';
                $answerArray['qid']=$stdquizid;
                $answerArray['answer']=$this->params['form']['q'.($i+1)]['answer'][$j];
                $answerArray['result']=($this->params['form']['q'.($i+1)]['result'][0]==$j)?1:0;
                $answerArray['datetime'] = date('Y-m-d H:i:s');
                $this->Sponsernanswer->save($answerArray);
            }
        }
		$this->Session->setFlash('New content uploaded');
		$this->redirect('sponsernquestions/'.$id);
		}else{
        	$this->Session->setFlash('Video size should not be greater than 5 MB');
        	$this->redirect('sponsernquestions/'.$id);
       }
	}
}
/*
* Method sponsernquizactivedeactive
* Purpose: admin can make deactive the feature question  
* Parameter : $id, $page
* 
* _______________________________________________________________________________
*/
function sponsernquizactivedeactive($id=NULL){
        	$this->checkadmin();
        	$a=0;
        $this->autoRender=false;
          if($this->RequestHandler->isAjax()){
             Configure::write('debug', 0);
			   	if(!empty($id)){
				    $results=$this->Sponserncontent->find('all',array('conditions'=>array('MD5(Sponserncontent.id)' =>$id)));
				    	if($results[0]['Sponserncontent']['status']=='1'){
				    		$a = 'inactive_'.$id;
							$data=array(
							'status'=>0,
							'id' =>$results[0]['Sponserncontent']['id']);
						}else{
							$a = 'active_'.$id;
							$data=array(
							'status'=>1,
							'id' =>$results[0]['Sponserncontent']['id']);
						}
			   			 $query = "update sponsern_contents set status = '".$data['status']."' where id = ".$data['id'];
			   			 //echo $query;
						 $this->Sponserncontent->query($query);
		                	echo $a;
		        }
          }	
   }
/*_______________________________________________________________________________
* Method viewsponsernquiz
* Purpose: admin can view detail
* Parameter : id
*
* _______________________________________________________________________________
*/
function viewsponsernquiz($id=NULL,$id2=NULL){
    $data = $this->Sponserncontent->find('all',array('conditions'=>'id='.$id));
    $this->set('data',$data);
    $dataAns = $this->Sponsernanswer->find('all');
    $this->set('dataAns',$dataAns);
    $this->set('id2',$id2);
}
/*_______________________________________________________________________________
* Method editsponsernquestion
* Purpose: admin can view detail
* Parameter : id
*
* _______________________________________________________________________________
*/  
function editsponsernquestion($id=NULL,$id2=NULL,$flg=NULL){
	
	$this->set('flg',$flg);
	$this->set('id2',$id2);
    $data = $this->Sponserncontent->find('all',array('conditions'=>'id='.$id));
    $this->set('data',$data);
    $list = $this->Sponsernquestion->find('all',array('conditions'=>'sponsern_content_id='.$id));
    $this->set('list',$list);
    
    if(!empty($this->data)){
        $this->Sponserncontent->updateAll(array('Sponserncontent.content_name'=>'"'.$this->data['Sponsern']['content_name'].'"','Sponserncontent.contentprovider'=>'"'.$this->data['Sponsern']['contentprovider'].'"','Sponserncontent.contentprovider_status'=>'"'.$this->data['Sponsern']['contentprovider_status'].'"','Sponserncontent.weblink'=>'"'.$this->data['Sponsern']['weblink'].'"','Sponserncontent.weblink_status'=>'"'.$this->data['Sponsern']['weblink_status'].'"','Sponserncontent.st_dt'=>'"'.$this->data['Sponsern']['st_dt'].'"','Sponserncontent.end_dt'=>'"'.$this->data['Sponsern']['end_dt'].'"'),array('Sponserncontent.id =' => $id));
        $tempint =array();
        $tempint['id']=$id;
        $tempint['instructions']=$this->data['Sponsern']['instructions'];
        $tempint['strategy']=$this->data['Sponsern']['strategy'];
        $tempint['script']=$this->data['Sponsern']['script'];
        $this->Sponserncontent->save($tempint);
        
        if($this->data['Sponsern']['content_type']==1){
        //$this->Sponserncontent->updateAll(array('Sponserncontent.text'=>'"'.$this->data['Sponsern']['text'].'"'),array('Sponserncontent.id =' => $id));
        $temp =array();
        $temp['id']=$id;
        $temp['text']=$this->data['Sponsern']['text'];
        $this->Sponserncontent->save($temp);
        }
        for($i=0;$i<count($this->params['form']);$i++){
            $k = $i+1;
            foreach($this->params['form']['q'.$k]['question'] as $key=>$val){
               $this->Sponsernquestion->updateAll(array('Sponsernquestion.question'=>'"'.$val.'"'),array('Sponsernquestion.id =' => $key));
               $this->Sponsernanswer->updateAll(array('Sponsernanswer.result'=>'0'),array('Sponsernanswer.qid =' => $key));
            }
            foreach($this->params['form']['q'.$k]['answer'] as $keyAns=>$valAns){
               $this->Sponsernanswer->updateAll(array('Sponsernanswer.answer'=>'"'.$valAns.'"'),array('Sponsernanswer.id =' => $keyAns));
            }
            
            foreach($this->params['form']['q'.$k]['result'] as $keyRes=>$valRes){
               $this->Sponsernanswer->updateAll(array('Sponsernanswer.result'=>'1'),array('Sponsernanswer.id =' => $valRes));
             }
            
        }
    
        $this->Session->setFlash('Sponsered content edit successfully');
        $this->redirect('sponsernquestions/'.$id2);
		
    }
    
}
/*_______________________________________________________________________________
* Method editimagevedeo
* Purpose: admin can make viewdetail
* Parameter : id
*
* _______________________________________________________________________________
*/
function editimagevideo($id=NULL,$id2=NULL,$flg=NULL){
   $this->checkadmin();
   $data = $this->Sponserncontent->find('all',array('fields'=>'Sponserncontent.image_video,Sponserncontent.id,Sponserncontent.content_type','conditions'=>'id='.$id));
   $this->set('data',$data);
   $this->set('flg',$flg);
   
   $this->set('id',$id);
    $this->set('id2',$id2);
   if(!empty($this->data)){
       if($data[0]['Sponserncontent']['content_type']==2){
       $ext = explode('.', $this->data['Sponsern']['image_video']['name']);
    		if($ext[1]=='jpeg' || $ext[1]=='jpeg' || $ext[1]=='JPEG' || $ext[1]=='png' || $ext[1]=='JPG' || $ext[1]=='jpg' || $ext[1]=='PNG' || $ext[1]=='GIF' || $ext[1]=='gif'){
    			$rval = $this->Usercomponent->imageGenerateKey();
                        $iname=$rval.'_'.time().'.'.$ext[1];
           	    
           	    if(move_uploaded_file($this->data['Sponsern']['image_video']['tmp_name'] ,_ROOT_BASE_PATH."img/sponsern/".$iname)){
					@chmod(_ROOT_BASE_PATH."img/sponsern/", 0777);
        				@chmod(_ROOT_BASE_PATH."img/sponsern/" .$data[0]['Sponserncontent']['image_video'], 0777);
        				@unlink(_ROOT_BASE_PATH."img/sponsern/" .$data[0]['Sponserncontent']['image_video']);
                        $this->Sponserncontent->updateAll(array('Sponserncontent.image_video' =>'"'.$iname.'"'), array('Sponserncontent.id' => $id));
           	    	$this->Session->setFlash('Logo updated successfully');
    			$this->redirect('editsponsernquestion/'.$id.'/'.$id2.'/'.$flg);
           	    }

    		}else{
                    $this->Session->setFlash('Please upload [jpeg,png,gif]');
                    $this->redirect('editsponsernquestion/'.$id.'/'.$id2.'/'.$flg);
                }

       }elseif($data[0]['Sponserncontent']['content_type']==3){
           $ext = explode('.', $this->data['Sponsern']['image_video']['name']);
           	if($ext[1]=='mp3' || $ext[1]=='x-flv' || $ext[1]=='flv' || $ext[1]=='mp4'){
    			$rval = $this->Usercomponent->imageGenerateKey();
                        $iname=$rval.'_'.time().'.'.$ext[1];
                         if($this->data['Sponsern']['image_video']['size'] < 5242880){
                        if(move_uploaded_file($this->data['Sponsern']['image_video']['tmp_name'] ,_ROOT_BASE_PATH."img/sponsern/".$iname)){
					@chmod(_ROOT_BASE_PATH."img/sponsern/", 0777);
        				@chmod(_ROOT_BASE_PATH."img/sponsern/".$data[0]['Sponserncontent']['image_video'], 0777);
        				@unlink(_ROOT_BASE_PATH."img/sponsern/".$data[0]['Sponserncontent']['image_video']);
                        $this->Sponserncontent->updateAll(array('Sponserncontent.image_video' =>' "' .$iname.' " '), array('Sponserncontent.id' => $id));
           	    	$this->Session->setFlash('Video updated successfully');
    			$this->redirect('editsponsernquestion/'.$id.'/'.$id2.'/'.$flg);
           	    }
                     }else{
                         $this->Session->setFlash('Video size should not more than 5MB');
    			$this->redirect('editsponsernquestion/'.$id.'/'.$id2.'/'.$flg);
                     }

    		}else{
                    $this->Session->setFlash('Please upload [flv,mp4,mp3]');
                    $this->redirect('editsponsernquestion/'.$id.'/'.$id2.'/'.$flg);
            }
       }
   }


}
/*_______________________________________________________________________________
* Method listing all sponsern product
* Purpose: Listing all sponsern product 
* Parameter : NONE
*
* _______________________________________________________________________________
*/
function sponsorlist($id2=NULL,$id3=NULL){
    $this->checkuserlog();
    $this->layout='default';
    $resultFeatured = $this->Sponsern->find('all',array('conditions'=>'status = 1', 'order'=>'index DESC'));
        $this->set('resultFeatured',$resultFeatured);
        if($id2!=''){
            $this->set('id2','featured');
        }
        if($id3!=''){
            $this->set('id3','show');
        }
}
/*_______________________________________________________________________________
* Method studentquiz
* Purpose: admin can make viewdetail
* Parameter : Null
*
* _______________________________________________________________________________
*/
function studentquiz($sponseredId=NULL,$skipId=NULL){
   
   $this->checkuserlog();
    $this->layout='default';
    $userID=$this->Session->read('User.id');
	$newar= array();
	$questionplayed=array();
	$questionpool=array();
	$this->set('sponseredId',$sponseredId);
	/*
	 * Student played quiz with content ID 
	 */
	$playedquiz = $this->Sponsernuserattempt->find('all',array('fields'=>'qid','conditions'=>'uid ='.$userID));// User played
	for($pq=0;$pq<count($playedquiz);$pq++){
		$questionplayed[] = $playedquiz[$pq]['Sponsernuserattempt']['qid'];
	}
	/*
	 * Content on the day 
	 */
	$resultOne = $this->Sponserncontent->find('all',array('conditions'=>'Sponserncontent.st_dt <= NOW() AND NOW() <= Sponserncontent.end_dt AND STATUS ="1" AND sponsern_id ='.$sponseredId));
	
	for($i=0;$i<count($resultOne);$i++){
		for($j=0;$j<count($resultOne[$i]['Sponsernquestion']);$j++){
			$newar[]=$resultOne[$i]['Sponsernquestion'][$j]['id'];
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
		$questionofday = $this->Sponsernquestion->find('all',array('conditions'=>'id ='.$questionpool[0]));// User played
		$resultcontent = $this->Sponserncontent->find('all',array('conditions'=>'Sponserncontent.id ='.$questionofday[0]['Sponsernquestion']['sponsern_content_id']));
		
		$this->set('data',$resultcontent[0]['Sponserncontent']);
		$this->set('questionofday',$questionofday[0]);
		
	}
	if(!empty($this->data)){
		pr($this->params);
		        $qa = explode('_',$this->params['form']['answer']);
				$newdata['id'] = '';
				$newdata['sponsern_id'] =$sponseredId;
				$newdata['sponsern_content_id'] =$this->data['Sponserns']['sponsern_content_id'] ;
				$newdata['qid'] =$qa[0];
				$newdata['aid'] =$qa[1];
				$newdata['uid'] =$userID;
	            $check = $this->Sponsernanswer->find('all',array('fields'=>'result','conditions'=>'id='.$qa[1]));
				if($check[0]['Sponsernanswer']['result']==1){
				$newdata['points'] = $this->get_ediv(9);
				$newdata['final_result']= 1;
				}else{
				$newdata['points'] = 0;
				$newdata['final_result']= '0';
				}
				$this->Sponsernuserattempt->save($newdata);
	            $checkexistuser = $this->Pausetempuser->find('all',array('conditions'=> 'uid='.$userID.' and qid='.$qa[0].' and aid='.$qa[1].' and type="spon"'));
				  if(!empty($checkexistuser)){
			    	  $this->Pausetempuser->deleteAll(array('uid'=>$userID,'qid'=>$qa[0],'type'=>'spon'));
				  }
				  if($check[0]['Sponsernanswer']['result']==1){
				  $this->ediv_user_masters->query("insert into ediv_user_masters (uid,ediv_type,points,datetime) values (".$userID.",9,".$this->get_ediv(9).",'".date('Y-m-d H:i:s')."')");
				  }
				 if($skipId!=''){
				$this->redirect('quizresult/'.$sponseredId.'/'.$qa[0].'/'.$skipId);
				}else{
					$this->redirect('quizresult/'.$sponseredId.'/'.$qa[0]);
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
function quizresult($contID=NULL,$qid=NULL,$skipId=NULL){
   
    $this->layout='default';
    $userID=$this->Session->read('User.id');
    $this->set('contID',$contID);
	$newar= array();
	$questionplayed=array();
	$questionpool=array();
		
    	/*
		 * End checking of poll exist //
		 * Displaying result // of Poll
		 */
		
    	$questionofdayNext = $this->Sponsernquestion->find('all',array('conditions'=>'id ='.$qid));// User played
		$resultcontentNext = $this->Sponserncontent->find('all',array('conditions'=>'Sponserncontent.id ='.$questionofdayNext[0]['Sponsernquestion']['sponsern_content_id']));
		
		$this->set('data',$resultcontentNext[0]['Sponserncontent']);
		$this->set('questionofday',$questionofdayNext[0]);
		if($skipId!=NULL){
			$this->set('skipId',$skipId);
		}
		/////////// Checking for the next qid ///////////////
		/*
		* Student played quiz with content ID 
		*/
			$playedquiz = $this->Sponsernuserattempt->find('all',array('fields'=>'qid','conditions'=>'uid ='.$userID));// User played
			for($pq=0;$pq<count($playedquiz);$pq++){
				$questionplayed[] = $playedquiz[$pq]['Sponsernuserattempt']['qid'];
			}
		/*
		* Content on the day 
		*/
	$resultOne = $this->Sponserncontent->find('all',array('conditions'=>'Sponserncontent.st_dt <= NOW() AND NOW() <= Sponserncontent.end_dt AND STATUS ="1" AND sponsern_id ='.$contID));
	
	for($i=0;$i<count($resultOne);$i++){
		for($j=0;$j<count($resultOne[$i]['Sponsernquestion']);$j++){
			$newar[]=$resultOne[$i]['Sponsernquestion'][$j]['id'];
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
function pausecontentspon(){
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
             		$temp['type']='spon';
             		$existpase=$this->Pausetempuser->find('all',array('conditions'=>array('uid'=>$userID,'qid'=>$val[0],'type'=>'spon')));
             		if(!empty($existpase)){
             			$this->Pausetempuser->deleteAll(array('uid'=>$userID,'qid'=>$val[0],'type'=>'spon'));
             		}
             		$this->Pausetempuser->save($temp);
             		
             }

          
}
/*_______________________________________________________________________________
* Method deleteanswer
* Purpose: admin can delete answer
* Parameter : $conId=NULL,$baseId=NULL,$ansId=NULL
*
* _______________________________________________________________________________
*/
function deleteanswer($conId=NULL,$baseId=NULL,$ansId=NULL){
	 	 $this->Sponsernanswer->delete($ansId);
		 $this->redirect('editsponsernquestion/'.$conId.'/'.$baseId);		
}
/*_______________________________________________________________________________
* Method addanswer
* Purpose: admin can delete answer
* Parameter : $conId=NULL,$baseId=NULL
*
* _______________________________________________________________________________
*/
function addanswer($conId=NULL,$baseId=NULL,$qid=NULL){
	//$result = $this->Sponsernquestion->find('all',array('fields'=>'MAX(id) as id','conditions'=>'sponsern_content_id='.$conId, 'group'=>'sponsern_content_id'));	
	$temp=array();
	$temp['id']='';
	$temp['qid']=$qid;
	$this->Sponsernanswer->save($temp);
	$this->redirect('editsponsernquestion/'.$conId.'/'.$baseId);
	
}
/*_______________________________________________________________________________
* Method addquestion
* Purpose: admin can add Question
* Parameter : Null
*
* _______________________________________________________________________________
*/
function addquestion($conId=NULL,$baseId=NULL){
	$temp=array();
	$temp['id']='';
	$temp['sponsern_content_id']=$conId;
	$this->Sponsernquestion->save($temp);
	$ansQid = $this->Sponsernquestion->id;
	
	$tempAns=array();
	$tempAns['id']='';
	$tempAns['qid']=$ansQid;
	$tempAns['result']='1';
	$this->Sponsernanswer->save($tempAns);
	
	$this->redirect('editsponsernquestion/'.$conId.'/'.$baseId);
}
/*_______________________________________________________________________________
* Method addquestion
* Purpose: admin can add Question
* Parameter : Null
*
* _______________________________________________________________________________
*/
function removequestion($conId=NULL,$baseId=NULL,$qid=NULL){
	
	//$result = $this->Sponsernquestion->find('all',array('fields'=>'MAX(id) as id','conditions'=>'sponsern_content_id='.$conId, 'group'=>'sponsern_content_id'));	
	$this->Sponsernanswer->deleteAll(array('Sponsernanswer.qid'=>$qid));//$result[0][0]['id'],$cascade = true);
	$this->Sponsernquestion->delete($qid);
	$this->redirect('editsponsernquestion/'.$conId.'/'.$baseId);
}

function updatesponsernanswer($id=NULL){
	$this->autoRender=false;
          if($this->RequestHandler->isAjax()){
             Configure::write('debug', 0);
             $q='q'.$id;
            
			 foreach($_REQUEST[$q]['question'] as $key=>$val){
			 	$this->Sponsernquestion->updateAll(array('Sponsernquestion.question'=>'"'.$val.'"'),array('Sponsernquestion.id'=>$key));
             }
             foreach($_REQUEST[$q]['answer'] as $anskey=>$ansval){
             	$this->Sponsernanswer->updateAll(array('Sponsernanswer.answer'=>'"'.$ansval.'"'),array('Sponsernanswer.id'=>$anskey));
             }
              $this->Sponsernanswer->updateAll(array('Sponsernanswer.result'=>'1'),array('Sponsernanswer.id'=>$_REQUEST[$q]['result'][0]));
          }
}

}// END CLASS   removequestion

?>