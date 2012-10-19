<?php
/* 
#################################################################################
#																				#
#   User Controller 															#
#   file name        	: maths_controller.php									#
#   Developed        	: Meridian Radiology 									#
#   Intialised Variables: name , helpers ,components , uses , layout = (admin)  #
#																				#
#																				#
#################################################################################
*/
class MathsController extends AppController {

	var $name = 'Maths';
	var $helpers = array('Html', 'Form', 'javascript','Ajax','session','Thickbox','Fck');
	var $layout = 'admin';
	var $uses = array('Studentcontent','Studentquestion','Studentanswer','Studentuserattempt','ediv_user_masters','Cafeseriescontent','Cafeseriesquestion','Cafeseriesanswer','Cafeseriesuserattempt','Pausetempuser','Sponsern','Sponserncontent','Sponsernuserattempt','Sponsernquestion','Sponsernanswer','Staticloungevalue','Studentcontentcat','Mathcontent');
	var $components = array('Usercomponent');
	 
	
/*
*___________________________________________________________
*
* Method     : index
* Purpose    : making listing on the page
* Parameters : None
*
*_____________________________________________________________
*/
	function index(){
		$this->checkadmin();
		$str = '';
		$result = $this->Mathcontent->find('all',array('fields'=>'con_id'));
		
		if(empty($result)){
			$this->set('nodata','nodata');
		}else{
			for($i=0;$i<count($result);$i++){
				$str = $str.','.$result[$i]['Mathcontent']['con_id']; 
			}
			$this->Studentcontent->find('all',array('conditions'=>'id in ('.substr($str,1).')'));
			$this->paginate = array(
                                    'conditions' =>'Studentcontent.id in ('.substr($str,1).')',
                                    'order' => array('Studentcontent.id' => 'DESC'),
                                    'limit' => 25
   				  );
	        $data = $this->paginate('Studentcontent');
			
	        $this->set('data',$data);
			
		}
		
			
	 }
/*
*___________________________________________________________
*
* Method     : newmathcontent
* Purpose    : New math content insertion
* Parameters : None
*
*_____________________________________________________________
*/
 function newmathcontent(){
 	$this->checkadmin();
	 	$accessList = array('0'=>'GEN','1'=>'SAT','2'=>'MED');
        $this->set('accessList',$accessList);
	 	
	 	$categoryList = $this->Studentcontentcat->find('list',array('conditions' => null,
                                                'order' => 'Studentcontentcat.name ASC',
                                                'limit' => null,
                                                'fields' => 'Studentcontentcat.name', 'Studentcontentcat.id'));
        $this->set('categoryList',$categoryList);
        if(!empty($this->data)){
        	if(isset($this->data['Students']['content_text']) && $this->data['Students']['content_text']==''){
        		$this->Session->setFlash('Please enter the text content');
        	}else{
		           	if($this->data['Students']['category_type']=='1'){
			               $contentCatId=$this->data['Students']['categoryId'];
			           }else{
				            $contentCatArray['id']='';
				            $contentCatArray['name']=$this->data['Students']['categoryName'];
				            $contentCatArray['datetime'] = date('Y-m-d H:i:s');
				            $this->Studentcontentcat->create();
				            $this->Studentcontentcat->save($contentCatArray);
				            $contentCatId=$this->Studentcontentcat->id;
			            }
		                $contentArray['id']='';
				        $contentArray['type']=1;// 1:for Student Quiz  2:for Student Poll
				        $contentArray['access_type']=$this->data['Students']['accessType'];// 0:for All Student  1:for Non Medical 2:for Medical
				        $contentArray['category']=$contentCatId;
				        $contentArray['name']=$this->data['Students']['name'];
				        $contentArray['instructions']=$this->data['Students']['instructions'];
				        $contentArray['strategy']=$this->data['Students']['strategy'];
				        $contentArray['script']=$this->data['Students']['script'];
				        $contentArray['contentprovider']=$this->data['Students']['contentprovider'];
				        $contentArray['contentprovider_status']=($this->data['Students']['contentprovider_status']==1)? '1' :'0';
				        $contentArray['weblink']=preg_replace('#^https?://#', '', $this->data['Students']['weblink']);
				        $contentArray['weblink_status']=($this->data['Students']['weblink_status']==1)? '1' :'0';
				        $contentArray['content_type']=1;
				        $contentArray['st_dt']=$this->data['Students']['st_dt'].' 00:00:00';
				        $contentArray['end_dt']=$this->data['Students']['end_dt'].' 00:00:00';
				        $contentArray['datetime']=date('Y-m-d H:i:s');
				        $contentArray['ediv_type']=10;
				        $contentArray['content_text']=$this->data['Students']['content_text'];
				        $this->Studentcontent->create();
		                $this->Studentcontent->save($contentArray);
		                $mapArray['id']='';
		                $mapArray['con_id']=$this->Studentcontent->id;
		                $this->Mathcontent->save($mapArray);
		                $this->redirect('enterquestion/'.$this->Studentcontent->id);
                 }
        
         }
  }
/*
*___________________________________________________________
*
* Method     : enterquestion
* Purpose    : New math question insertion
* Parameters : None
*
*_____________________________________________________________
*/
	 function enterquestion($id=NULL){
	 	$this->checkadmin();
	 	$quizArray=array();
	 	$this->set('id',$id);
	 	if(isset($this->data['Students']['question']) && $this->data['Students']['question']==''){
	 		$this->Session->setFlash('Please enter the question content');
	 	}else{
	 	if($this->data){	
         $quizArray['id']='';
         $quizArray['content_id']=$id;
         $quizArray['question']=$this->data['Students']['question'];
         $quizArray['hint']=$this->data['Students']['hint'];
         $quizArray['explanation']=$this->data['Students']['explanation'];
         $this->Studentquestion->save($quizArray);
         $this->redirect('enteranswer/'.$this->Studentquestion->id);
	 	}
	 	}
	 	
	 }
/*
*___________________________________________________________
*
* Method     : enteranswer
* Purpose    : New math answer insertion
* Parameters : None
*
*_____________________________________________________________
*/
	 function enteranswer($id=NULL,$flg=NULL){
	 	$this->checkadmin();
	 	$this->set('id',$id);
	 	if(isset($flg)){
	 		$this->set('flg',$flg);
	 	}else{
			 	$ansArray=array();
			 	$this->set('id',$id);
			 	if(isset($this->data['Students']['answer']) && $this->data['Students']['answer']==''){
			 		$this->Session->setFlash('Please enter the answer content');
			 	}else{
			 	 if(!empty($this->data)){
			 	 	 $ansArray['id']='';
			         $ansArray['qid']=$id;
			         $ansArray['answer']=$this->data['Students']['answer'];
			         if($this->data['Students']['result']==1){
			         	$this->Studentanswer->updateAll(array('result'=>'0'),array('qid'=>$id));
			         }
			         $ansArray['result']=$this->data['Students']['result'];
			         $this->Studentanswer->save($ansArray);
			         $this->redirect('enteranswer/'.$id.'/more');
			 	  }
			 	 }
	 }

}
/*
*___________________________________________________________
*
* Method     : editmathcontent
* Purpose    : edit math content
* Parameters : None
*
*_____________________________________________________________
*/
function editmathcontent($id=NULL,$option=NULL){
	$this->checkadmin();
	$this->set('id',$id);
	   $accessList = array('0'=>'GEN','1'=>'SAT','2'=>'MED');
        $this->set('accessList',$accessList);
	 	
	 	$categoryList = $this->Studentcontentcat->find('list',array('conditions' => null,
                                                'order' => 'Studentcontentcat.name ASC',
                                                'limit' => null,
                                                'fields' => 'Studentcontentcat.name', 'Studentcontentcat.id'));
        $this->set('categoryList',$categoryList);
		
        $result = $this->Studentcontent->find('all',array('conditions'=>'id='.$id));
        $this->set('data',$result);
       if(!empty($this->data)){
        	if(isset($this->data['Students']['content_text']) && $this->data['Students']['content_text']==''){
        		$this->Session->setFlash('Please enter the text content');
        	}else{
		           	if($this->data['Students']['category_type']=='1'){
			               $contentCatId=$this->data['Students']['categoryId'];
			           }else{
				            $contentCatArray['id']='';
				            $contentCatArray['name']=$this->data['Students']['categoryName'];
				            $contentCatArray['datetime'] = date('Y-m-d H:i:s');
				            $this->Studentcontentcat->create();
				            $this->Studentcontentcat->save($contentCatArray);
				            $contentCatId=$this->Studentcontentcat->id;
			            }
		                $contentArray['id']=$id;
				        $contentArray['type']=1;// 1:for Student Quiz  2:for Student Poll
				        $contentArray['access_type']=$this->data['Students']['accessType'];// 0:for All Student  1:for Non Medical 2:for Medical
				        $contentArray['category']=$contentCatId;
				        $contentArray['name']=$this->data['Students']['name'];
				        $contentArray['instructions']=$this->data['Students']['instructions'];
				        $contentArray['strategy']=$this->data['Students']['strategy'];
				        $contentArray['script']=$this->data['Students']['script'];
				        $contentArray['contentprovider']=$this->data['Students']['contentprovider'];
				        $contentArray['contentprovider_status']=($this->data['Students']['contentprovider_status']==1)? '1' :'0';
				        $contentArray['weblink']=preg_replace('#^https?://#', '', $this->data['Students']['weblink']);
				        $contentArray['weblink_status']=($this->data['Students']['weblink_status']==1)? '1' :'0';
				        $contentArray['content_type']=1;
				        $contentArray['st_dt']=$this->data['Students']['st_dt'].' 00:00:00';
				        $contentArray['end_dt']=$this->data['Students']['end_dt'].' 00:00:00';
				        $contentArray['datetime']=date('Y-m-d H:i:s');
				        $contentArray['ediv_type']=10;
				        $contentArray['content_text']=$this->data['Students']['content_text'];
				        $this->Studentcontent->create();
		                $this->Studentcontent->save($contentArray);
		                if($option!=''){
		                $this->redirect('editmathquestion/'.$id);	
		                }else{
		                $this->Session->setFlash('Maths content save successfully');
		                $this->redirect('index');
		                }
                 }
        
         }
	
	
}
/*
*___________________________________________________________
*
* Method     : editmathquestion
* Purpose    : edit math question
* Parameters : $id=NULL,$option=NULL
*
*_____________________________________________________________
*/
function editmathquestion($id=NULL,$option=NULL) {
	$this->checkadmin();
	$result = $this->Studentquestion->find('all',array('conditions'=>'content_id='.$id));
	$this->set('id',$id);
	$this->set('data',$result);
    if(isset($this->data['Students']['question']) && $this->data['Students']['question']==''){
        		$this->Session->setFlash('Please enter the Question content');
      }else{
      	if(isset($this->data['Students']['question']) && $this->data['Students']['question']!=''){
           $quizArray['id'] = $result[0]['Studentquestion']['id'];
           $quizArray['question'] = $this->data['Students']['question'];
           $quizArray['hint'] = $this->data['Students']['hint'];
           $quizArray['explanation'] = $this->data['Students']['explanation'];
           $this->Studentquestion->create();
		   $this->Studentquestion->save($quizArray);
               if($option!=''){
                $this->redirect('editmathanswer/'.$result[0]['Studentquestion']['id']);	
                }else{
                $this->Session->setFlash('Maths content save successfully');
                $this->redirect('index');
                }
           
      	}
    }
}
/*
*___________________________________________________________
*
* Method     : editmathanswer
* Purpose    : edit math answer
* Parameters : $id=NULL,$option=NULL
*
*_____________________________________________________________
*/
function editmathanswer($id=NULL,$option=NULL) {
	$this->checkadmin();
    $this->set('id',$id);
    if(isset($this->data['Students']['aid'])){
      	$str = $this->data['Students']['aid'];
    	
        if(isset($this->data['Students']['answer']) && $this->data['Students']['answer']==''){
        		$this->Session->setFlash('Please enter the Question content');
        		$result = $this->Studentanswer->find('all',array('conditions'=>'qid='.$id.' and id not in ('.$str.')','limit'=>1));
        		$this->set('data',$result);
        		$this->set('str',$str);
         }else{
         	
         	$ansArray['id']=$this->data['Students']['aidMain'];
         	$ansArray['answer']=$this->data['Students']['answer'];
         	$ansArray['result']=$this->data['Students']['result'];
            if($this->data['Students']['result']==1){
				$this->Studentanswer->updateAll(array('result'=>'"0"'),array('qid'=>$id));
			}
			$this->Studentanswer->save($ansArray);
		 	if(isset($option)){
         	$result = $this->Studentanswer->find('all',array('conditions'=>'qid='.$id.' and id not in ('.$str.')','limit'=>1));
    	 	if(count($result)>0){
         	 $str = $this->data['Students']['aid'].','.$result[0]['Studentanswer']['id'];
    	     $this->set('str',$str);
    	     $this->set('data',$result);
    	    }else{
    	    	$this->Session->setFlash('Maths content save successfully');
                $this->redirect('index');
    	    }
         }else{
         	$this->Session->setFlash('Maths content save successfully');
            $this->redirect('index');
         }
				
         }
    }else{
    	$result = $this->Studentanswer->find('all',array('conditions'=>'qid='.$id,'limit'=>1));
    	$str = $result[0]['Studentanswer']['id'];
    	$this->set('str',$str);
    	pr($result);
    	$this->set('data',$result);
    }
	
	
    
      
	
}



}//////////// Class end
?>
 