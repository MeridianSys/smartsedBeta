<?php
/* 
#################################################################################
#																				#
#   Featured Controller 															#
#   file name        	: Features_controller.php								#
#   Developed        	: Meridian Radiology 									#	
#   Intialised Variables: name , helpers ,components , uses , layout = (admin)  #
#																				#
#																				#
#################################################################################
*/
class FeaturesController extends AppController {

 	 var $name = 'Features';
         var $helpers = array('html','Form','javascript','Ajax','session','Thickbox','Fck');
	 var $uses = array('ediv_user_masters','Feature','Featurequestion','Featureanswer','Featureuserattempt','Featuredproduct');
	 var $components = array('Usercomponent');
	 
    
/*___________________________________________________________________________________________________
* 
* Method     : index
* Purpose    : making list of the featured company
* Parameters : None
* 
* ___________________________________________________________________________________________________
*/	 
function index(){
	 $this->layout = 'admin';
   	 // Check admin athontication // 
		$this->checkadmin();
		$this->Feature->find('all');
		$this->paginate = array(
                         'limit' => 25,
                         'page'=>1,
                         'order'=>'Feature.index DESC'
    				 );
	     
	     $data = $this->paginate('Feature');
         $this->set('data',$data);
			
    	
    }
    /*_______________________________________________________________________________	
	* Method indexdown
 	* Purpose: admin can make new charity  
 	* Parameter : None
 	* 
 	* ______________________________________________________________________________
 	*/
	function indexdown($id=NULL){
		$result=$this->Feature->find('all',array('conditions'=>'Feature.id='.$id,'fields'=>'index'));
		$newindex =$result[0]['Feature']['index']-1;
		
		$commer = $this->Feature->find('all',array('conditions'=>'Feature.index='.$newindex,'fields'=>'id,index')); 
		$commerindex=$commer[0]['Feature']['index']+1;
		$this->Feature->updateAll(array('Feature.index' =>$commerindex ),array('Feature.id'=>$commer[0]['Feature']['id']));
		$this->Feature->updateAll(array('Feature.index' =>$newindex ),array('Feature.id'=>$id));
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
		$result=$this->Feature->find('all',array('conditions'=>'Feature.id='.$id,'fields'=>'index'));
		$newindex =$result[0]['Feature']['index']+1;
		$commer = $this->Feature->find('all',array('conditions'=>'Feature.index='.$newindex,'fields'=>'id,index')); 
		$commerindex=$commer[0]['Feature']['index']-1;
		$this->Feature->updateAll(array('Feature.index' =>$commerindex ),array('Feature.id'=>$commer[0]['Feature']['id']));
		$this->Feature->updateAll(array('Feature.index' =>$newindex ),array('Feature.id'=>$id));
		$this->redirect('index');
	}

 /*_______________________________________________________________________________	
	* Method newcharity
 	* Purpose: admin can make new charity  
 	* Parameter : None
 	* 
 	* ______________________________________________________________________________
 	*/
    function newfeature(){
    	
    	$this->layout = 'admin';
        $this->checkadmin();
       // pr($this->data);
        if(!empty($this->data['Feature'])){
	        $this->Feature->set($this->data);
	       		 if($this->Feature->validates()) {
	        		list($width, $height, $type, $attr) = getimagesize($this->data['Feature']['logo']['tmp_name']);
	        		if($width<=550 && $height<=450){
	        			
	        		    $rval = $this->Usercomponent->imageGenerateKey();

                                    $ext = explode('.',$this->data['Feature']['logo']['name']);
                                    $iname=$rval.'_'.time().'.'.$ext[1];
           	 			if(move_uploaded_file($this->data['Feature']['logo']['tmp_name'] ,_ROOT_BASE_PATH."img/featured/".$iname)){
           	 				$this->data['Feature']['logo'] = $iname;
           	 				$indexing = $this->Feature->find('all');
           	 				$this->data['Feature']['index']=count($indexing)+1;
           	 				$this->Feature->updateAll(array('Feature.sponserd' => '0'));
           	 				$this->Feature->save($this->data);
           	 				$this->redirect('index');
           	 			}
	        				
	        		}else{
	        			$this->Session->setFlash("Image diamention is greater than 550 X 450");
	        		}
	       		 		
	         }
        }
         }

/*
_______________________________________________________________________________
* Method sponceractivedeactive
* Purpose: admin can make active to sponcern  
* Parameter : None
* _______________________________________________________________________________
*/         
function sponceractivedeactive($id=NULL){
	$this->Feature->updateAll(array('Feature.sponserd' => '0'));
	$this->Feature->updateAll(array('Feature.sponserd' => '1'),array('Feature.id'=>$id));
	$this->redirect('index');
}
         
         
         
         
/*
_______________________________________________________________________________
* Method edivactivedeactive
* Purpose: admin can make deactive the user  
* Parameter : None
* 
* _______________________________________________________________________________
*/
function featureactivedeactive($id=NULL,$page=NULL){
        	$this->checkadmin();
        	$a=0;
        $this->autoRender=false;
          if($this->RequestHandler->isAjax()){
             Configure::write('debug', 0);
			   	if(!empty($id)){
				    $results=$this->Feature->find('all',array('conditions'=>array('MD5(Feature.id)' =>$id)));
				    	if($results[0]['Feature']['status']=='1'){
				    		$a = 'inactive_'.$id;
							$data=array(
							'status'=>0,
							'id' =>$results[0]['Feature']['id']);
						}else{
							$a = 'active_'.$id;
							$data=array(
							'status'=>1,
							'id' =>$results[0]['Feature']['id']);
						}
			   			
						$this->Feature->query("update featured_treatments set status = '".$data['status']."' where id = ".$data['id']);
						echo $a;
										
					}

          }	
   	
   	
   }
/*
_______________________________________________________________________________
* Method editfeature
* Purpose: admin can edit the featured content  
* Parameter : None
* 
* _______________________________________________________________________________
*/	
function editfeature($id=NULL){
      // Check admin athontication // 
      $this->layout = 'admin';
	   $this->checkadmin();
	   $this->Feature->id = $id;
       if (empty($this->data)) {
          $this->data = $this->Feature->read();
          $this->set('id',$id);
       }else{
       	       $data = array(
       	       			'id'=>$this->data['Feature']['id'],
       	                'company_name'=>$this->data['Feature']['company_name'],
       	       			'description'=>$this->data['Feature']['description']
       	       );
       	        $this->Feature->save($data,$validate = false);
		    	$this->Session->setFlash('Your post has been updated.');
				$this->redirect('index');
		 
       }
}
/*
_______________________________________________________________________________
* Method editfeatureimage
* Purpose: admin can edit image  
* Parameter : None
* 
* _______________________________________________________________________________
*/	    
function editfeatureimage($id=NULL){
	    $this->checkadmin();
		if(isset($_FILES['data']['name']['Feature']['logo']) && $_FILES['data']['name']['Feature']['logo']!=''){
    		
    		$ext = explode('/', $_FILES['data']['type']['Feature']['logo']);
    				if($ext[1]=='jpeg' || $ext[1]=='jpeg' || $ext[1]=='JPEG' || $ext[1]=='png' || $ext[1]=='JPG' || $ext[1]=='jpg' || $ext[1]=='PNG' || $ext[1]=='GIF' || $ext[1]=='gif'){
		    			$rval = $this->Usercomponent->imageGenerateKey();
                                     $extn = explode('.',$_FILES['data']['name']['Feature']['logo']);
                                     $iname=$rval.'_'.time().'.'.$extn[1];
		           	    
		           	    list($width, $height, $type, $attr) = getimagesize($this->data['Feature']['logo']['tmp_name']);
		           	    //echo $width.'<=550 && '.$height.'<=450';
                                    if($width<=550 && $height<=450){
				           	    if(move_uploaded_file($_FILES['data']['tmp_name']['Feature']['logo'] ,_ROOT_BASE_PATH."img/featured/".$iname)){
									
									@chmod(_ROOT_BASE_PATH."img/featured/", 0777);
									
									@chmod(_ROOT_BASE_PATH."img/featured/" .$imName, 0777);
									
									$delIm = $this->Feature->find('all',array('conditions'=>$id,'fields'=>'logo'));
									
									
									
									@unlink(_ROOT_BASE_PATH."img/featured/" .$delIm[0]['Feature']['logo']);
										
									
									
				                    $this->Feature->updateAll(array('Feature.logo' =>'"'.$iname.'"'), array('Feature.id' => $id));         	    	
				           	    	$this->Session->setFlash('Logo updated successfully');
				    			    $this->redirect('editfeature/'.$id);
				           	    }
		           	    }else{

		           	    		$this->Session->setFlash('File should not be above than 550 X 450');
    							$this->redirect('editfeature/'.$id);
    			
		           	    }
		           	  
		    			
    					}else{
    					$this->Session->setFlash('Invalid file type');
    					$this->redirect('editfeature/'.$id);
    			
    				}
    		
    	}
		else{
			
	    $this->layout = '';
    	$results=$this->Feature->find('all',array('conditions'=>array('id' =>$id)));
    	
    	$this->set('result',$results[0]);
	
		}
	
}

/*_______________________________________________________________________________
* Method questions
* Purpose: admin can list the questions  
* Parameter : None
* 
* _______________________________________________________________________________
*/	   
function questions($id=NULL){
	 $this->layout = 'admin';
   	 // Check admin athontication // 
		$this->checkadmin();
		$this->Featurequestion->find('all');
		$this->paginate = array(
                         'limit' => 25,
                         'page'=>1,
		                 'conditions'=>'featured_id='.$id
                         
    				 );
	 $data = $this->paginate('Featurequestion');
         $this->set('data',$data);
         $this->set('id',$id);
}

/*_______________________________________________________________________________
* Method question
* Purpose: admin can set  new questions  
* Parameter : None
* 
* _______________________________________________________________________________
*/	   
function newquestion($id=NULL){
	$this->checkadmin();
	$this->layout = 'admin';
	$this->set('id',$id);
	
	if(!empty($this->data)){
		/// inter question to question table //
		$this->data['Featurequestion']['featured_id'] = $id;
		$this->Featurequestion->save($this->data);
		$data = array();
		$data['qid']=$this->Featurequestion->id;
		
		for($i=1;$i<=count($this->params['form']);$i++){
			
			$data['id']='';
			$data['answer'] = $this->params["form"]["name".$i]; 
			if($i == $this->data['Featured']['result']){
				$data['result']=1;
			}else{
				$data['result']=0;
			}
			$this->Featureanswer->save($data);
			
			
		}
		
		$this->redirect('questions/'.$id);
		
		
		
	}
}
/*
_______________________________________________________________________________
* Method quizactivedeactive
* Purpose: admin can make deactive the feature question  
* Parameter : $id, $page
* 
* _______________________________________________________________________________
*/
function quizactivedeactive($id=NULL,$page=NULL){
        	$this->checkadmin();
        	$a=0;
        $this->autoRender=false;
          if($this->RequestHandler->isAjax()){
             Configure::write('debug', 0);
			   	if(!empty($id)){
				    $results=$this->Featurequestion->find('all',array('conditions'=>array('MD5(Featurequestion.id)' =>$id)));
				    	if($results[0]['Featurequestion']['status']=='1'){
				    		$a = 'inactive_'.$id;
							$data=array(
							'status'=>0,
							'id' =>$results[0]['Featurequestion']['id']);
						}else{
							$a = 'active_'.$id;
							$data=array(
							'status'=>1,
							'id' =>$results[0]['Featurequestion']['id']);
						}
			   			 $query = "update featured_questions set status = '".$data['status']."' where id = ".$data['id'];
						 $this->Featurequestion->query($query);
		                	echo $a;
		}
          }	
   }

/*_______________________________________________________________________________
* 
* Method editfeaturequestion
* Purpose: admin can make deactive the feature question  
* Parameter : $id, $page
* 
* _______________________________________________________________________________
*/
   
function editfeaturequestion($id=NULL,$comp=NULL){
	$this->checkadmin();
	$this->layout = 'admin';
   
    $this->set('id',$id);
    $this->set('comp',$comp);
	   $this->Featurequestion->id = $id;
       if (empty($this->data)) {
          $this->data = $this->Featurequestion->read();
          $data = $this->Featureanswer->find('all',array('conditions'=>'qid = '.$id));
          $this->set('data',$data);
          
       }else{
       	$data = $this->Featureanswer->find('all',array('conditions'=>'qid = '.$id));
        $this->set('data',$data);
        if($this->Featurequestion->save($this->data)) {
        	$ansVal = array();
        		foreach($this->params["form"] as $id=>$value){
        		$val = explode('_',$id);
				$ansVal['id'] = $val[1];
        		$ansVal['answer'] = $value;
        		if($ansVal['id']==$this->data['Featured']['result']){
        			$ansVal['result']=1;
        		}else{
        			$ansVal['result']=0;
        		}
        		$this->Featureanswer->save($ansVal);
        	}
        	$this->redirect('questions/'.$comp);
        }
       	
     }
	
	
 }
    
/*_______________________________________________________________________________
* 
* Method featuredproduct
* Purpose: admin can make new product for featured   
* Parameter : $id, $page
* 
* _______________________________________________________________________________
*/    
function featuredproduct($id=NULL){

	 $this->layout = 'admin';
   	 // Check admin athontication // 
		$this->checkadmin();
		$this->Featuredproduct->find('all');
		$this->paginate = array(
                         'limit' => 25,
                         'page'=>1,
		                 'conditions'=>'featured_id='.$id
                         
    				 );
	     $data = $this->paginate('Featuredproduct');
         $this->set('data',$data);
         $this->set('id',$id);
	
	
}
 
/*_______________________________________________________________________________
* 
* Method featuredproduct
* Purpose: admin can make new product for featured   
* Parameter : $id, $page
* 
* _______________________________________________________________________________
*/ 
function newfproduct($id=NULL){
	
	 $this->layout = 'admin';
   	 // Check admin athontication // 
	 $this->checkadmin();
	 $this->set('id',$id); 	
      
        if(!empty($this->data['Featuredproduct'])){
	        $this->Featuredproduct->set($this->data);
	       		 if($this->Featuredproduct->validates()) {
	       		 	if(!empty($this->data['Featuredproduct']['pdf_name']['name'])){
	       		 	$rval = $this->Usercomponent->imageGenerateKey();
           	 			$iname=$rval.'_'.$this->data['Featuredproduct']['pdf_name']['name'];
           	 			if(move_uploaded_file($this->data['Featuredproduct']['pdf_name']['tmp_name'] ,_ROOT_BASE_PATH."img/featured/".$iname)){
           	 				
           	 				      
           	 				
           	 				$this->data['Featuredproduct']['detail'] = addslashes(htmlspecialchars_decode($this->data['Featuredproduct']['detail'],ENT_QUOTES));//addslashes($this->data['Featuredproduct']['detail']);
           	 				$this->data['Featuredproduct']['pdf_name'] = $iname;
           	 				$this->data['Featuredproduct']['featured_id'] = $id;
           	 				if($this->Featuredproduct->save($this->data,$validate = false)){
           	 					$this->redirect('index');
           	 				}	
           	 				
           	 				
           	 			}
	       		 		
	       		 		
	       		 	}else{
	       		 		$this->data['Featuredproduct']['detail'] = addslashes(htmlspecialchars_decode($this->data['Featuredproduct']['detail'],ENT_QUOTES));//addslashes($this->data['Featuredproduct']['detail']);
           	 			$this->data['Featuredproduct']['featured_id'] = $id;
           	 			$this->data['Featuredproduct']['pdf_name'] = '';
	       		 		if($this->Featuredproduct->save($this->data,$validate = false)){
           	 					$this->redirect('index');
           	 				}	
	       		 		
	       		 		
	       		 	}
	        	 		
	       		 	
	       		
	         }
      }
	
}
/*_______________________________________________________________________________
* 
* Method productactivedeactive
* Purpose: admin can make active deactive   
* Parameter : $id, $page
* 
* _______________________________________________________________________________
*/  
function  productactivedeactive($id=NULL,$page=NULL){
	
		$this->checkadmin();
        	$a=0;
        $this->autoRender=false;
          if($this->RequestHandler->isAjax()){
             Configure::write('debug', 0);
			   	if(!empty($id)){
				    $results=$this->Featuredproduct->find('all',array('conditions'=>array('MD5(Featuredproduct.id)' =>$id)));
				    	if($results[0]['Featuredproduct']['status']=='1'){
				    		$a = 'inactive_'.$id;
							$data=array(
							'status'=>0,
							'id' =>$results[0]['Featuredproduct']['id']);
						}else{
							$a = 'active_'.$id;
							$data=array(
							'status'=>1,
							'id' =>$results[0]['Featuredproduct']['id']);
						}
			   			$query = "update featured_product_details set status = '".$data['status']."' where id = ".$data['id'];
						 $this->Featuredproduct->query($query);
						echo $a;
										
					}

          }	
   	
   	
	
	
}
/*_______________________________________________________________________________
* 
* Method editproduct
* Purpose: admin can make edit for the products   
* Parameter : $id
* 
* _______________________________________________________________________________
*/ 

function editproduct($id=NULL,$comp=NULL){
	
    $this->checkadmin();
	$this->layout = 'admin';
    $this->set('id',$id);
    $this->set('comp',$comp);
	$this->Featuredproduct->id = $id;
       if (empty($this->data)) {
          $this->data = $this->Featuredproduct->read();
           
       }else{
       			 $this->Featuredproduct->set($this->data);
	       		 if($this->Featuredproduct->validates()) {  
	       		 	////////////////////////////////
	       		 if(isset($this->data['Featuredproduct']['pdf_name']['name']) && !empty($this->data['Featuredproduct']['pdf_name']['name'])){
	       		 	$rval = $this->Usercomponent->imageGenerateKey();
           	 			$iname=$rval.'_'.$this->data['Featuredproduct']['pdf_name']['name'];
           	 			if(move_uploaded_file($this->data['Featuredproduct']['pdf_name']['tmp_name'] ,_ROOT_BASE_PATH."img/featured/".$iname)){
           	 				
           	 				        @chmod(_ROOT_BASE_PATH."img/featured/", 0777);
									
									$delIm = $this->Featuredproduct->find('all',array('conditions'=>'id = '.$id ,'fields'=>'pdf_name'));
									
								    @unlink(_ROOT_BASE_PATH."img/featured/" .$delIm[0]['Featuredproduct']['pdf_name']);
           	 				
							$this->data['Featuredproduct']['detail'] = addslashes(htmlspecialchars_decode($this->data['Featuredproduct']['detail'],ENT_QUOTES));//addslashes($this->data['Featuredproduct']['detail']);
							
           	 				$this->data['Featuredproduct']['pdf_name'] = $iname;
           	 				$this->data['Featuredproduct']['featured_id'] = $comp;

           	 				if($this->Featuredproduct->save($this->data,$validate = false)){
           	 					$this->redirect('featuredproduct/'.$comp);
           	 				}	
           	 				
           	 			}
	       		 		
	       		 	}
	       		 	else{
	       		 		
	       		 		$this->Featuredproduct->updateAll( array("Featuredproduct.detail" => "'".addslashes(htmlspecialchars_decode($this->data['Featuredproduct']['detail']))."'"), array( "Featuredproduct.id" => $id ) );
	       		 		$this->Featuredproduct->updateAll( array("Featuredproduct.menulink" => "'".$this->data['Featuredproduct']['menulink']."'"), array( "Featuredproduct.id" => $id ) );
	       		 		
	       		 		$this->redirect('featuredproduct/'.$comp);
           	 		  	
	       		    
	       	      }
	       		 
	       	 }
       	
       }
	
}

/*
* ___________________________________________________________________
* Method finalizequiz
* Purpose: Finalize the quiz
* Parameters : None
*
* _______________________________________________________________________
*/
function finalizequiz(){
        $this->checkuserlogin();
        $userID=$this->Session->read('User.id');
        $data1 = array();
        $data1['qid']= $this->data['Features']['qid'];
        $data1['aid']= $this->data['Features']['answer'];
        $data1['uid']=$userID;
        $check = $this->Featureanswer->find('all',array('fields'=>'result','conditions'=>'id='.$this->data['Features']['answer']));
        if($check[0]['Featureanswer']['result']==1){
         $data1['point']= $this->get_ediv(9);
        }else{
            $data1['point'] = 0;
        }
        $data1['datetime'] = date('Y-m-d H:i:s');
        if($data1['point']!=0){
            $this->ediv_user_masters->query("insert into ediv_user_masters (uid,ediv_type,points,datetime) values (".$data1['uid'].",9,".$data1['point'].",'".$data1['datetime']."')");
        }
        $this->Featureuserattempt->save($data1);
        $this->redirect(array('controller'=>'features','action'=>'sponsorlist/featured'));


}
/*
* ___________________________________________________________________
* Method featuredquizstart
* Purpose: Display Quiz question
* Parameters : None
*
* _______________________________________________________________________
*/
function featuredquizstart($id=NULL){

	$this->checkuserlogin();
	$userID=$this->Session->read('User.id');
        $this->User->id=$userID;
        $userinfo = $this->User->read();
        $this->set('userinfo',$userinfo);
        $resultFeatured = $this->Feature->find('all',array('fields'=>'company_name','conditions'=>'id='.$id,'limit'=>1));
        $this->set('resultFeatured',$resultFeatured);
        $questionFeatured = $this->Featurequestion->find('all',array('conditions'=>'Featurequestion.status=1 and date(Featurequestion.set_q_date)<="'.date('Y-m-d').'" and "'.date('Y-m-d').'"<= date(Featurequestion.end_q_date) and Featurequestion.featured_id='.$id));
        //pr($questionFeatured);
        $this->set('id',$id);
        $this->set('questionFeatured',$questionFeatured);
         if((count($this->params['form'])==$this->data['Features']['count']) && !empty($this->data)){
            $dataUser = array();
             foreach($this->params['form'] as $key=>$val){
                $value = explode('_',$val);
                $dataUser['id']='';
                $dataUser['qid']=$value[0];
                $dataUser['aid']=$value[1];
                $dataUser['uid']=$userID;
                $dataUser['featured_id']=$id;
                $dataUser['datetime'] = date('Y-m-d H:i:s');
                $check = $this->Featureanswer->find('all',array('fields'=>'result','conditions'=>'id='.$dataUser['aid']));
                    if($check[0]['Featureanswer']['result']==1){
                         $dataUser['point']= $this->get_ediv(9);
                         $featurePt[]=1;
                    }else{
                        $dataUser['point'] = 0;
                    }
                 $this->Featureuserattempt->save($dataUser);
            }

            if($this->data['Features']['count'] == count($featurePt)){
              $this->ediv_user_masters->query("insert into ediv_user_masters (uid,ediv_type,points,datetime) values (".$userID.",9,".$this->get_ediv(9).",'".date('Y-m-d H:i:s')."')");
            }
            $this->redirect(array('controller'=>'features','action'=>'sponsorlist/featured/show'));
         }else{
             if(!empty($this->data)){
            $this->set('validation',1);
             }
         }
}

function sponsorlist($id2=NULL,$id3=NULL){
    $resultFeatured = $this->Feature->find('all',array('conditions'=>'status = 1', 'order'=>'index DESC'));
        $this->set('resultFeatured',$resultFeatured);
        if($id2!=''){
            $this->set('id2','featured');
        }
        if($id3!=''){
            $this->set('id3','show');
        }
}



} // End Class
?>