<?php
/*
#################################################################################
#										#
#   News Controller 								#
#   file name        	: news_controller.php					#
#   Developed        	: Meridian Radiology 					#
#   Intialised Variables: name , helpers ,components , uses , layout = (admin)  #
#										#
#										#
#################################################################################
*/
class NewsController extends AppController {

	 var $name = 'News';
	 var $helpers = array('Html', 'Form', 'javascript','Ajax','session','Thickbox','Fck');
	 var $layout = 'admin';
	 var $conf = array();
	 var $uses = array('News','User','Userprofile');
	 var $components = array('Usercomponent');

/*_______________________________________________________________________________
* Method index
* Purpose: admin list images
* Parameter : None
*______________________________________________________________________________
*/
function index(){

            $this->layout = 'admin';
            // Check admin athontication //
            $this->checkadmin();
            $this->News->find('all');
            $this->paginate = array(
                     'limit' => 25,
                     'page'=>1,

                             );
           $data = $this->paginate('News');
           $this->set('data',$data);
}
/*_______________________________________________________________________________
* Method newadd
* Purpose: admin can add news
* Parameter : None
*______________________________________________________________________________
*/
function newsadd(){
  	$this->layout = 'admin';
   	 // Check admin athontication //
		$this->checkadmin();
    		  if(!empty($this->data)){
    				$this->data['News']['status'] = 1;
    				if($this->News->save($this->data)){
    		  			$this->Session->setFlash("News Content added ");
    		  			$this->redirect('index');
    	 		}

            }
  }
/*_______________________________________________________________________________
* Method newedit
* Purpose: admin can edit news
* Parameter : id=null
*______________________________________________________________________________
*/
function newsedit($id=NULL){

// Check admin athontication //
   $this->checkadmin();
   $this->News->id = $id;
    if(empty($this->data)) {
      $this->data = $this->News->read();
      $this->set('id',$id);
        }else{
              if($this->News->save($this->data)) {
                        $this->Session->setFlash('Your news has been updated.');
                        $this->redirect(array('action' => 'index'));
            }
        $this->set('id',$id);
    }
   
}
/*_______________________________________________________________________________
* Method newsdelete
* Purpose: admin can delete record
* Parameter : id=none
*
* ______________________________________________________________________________
*/
function newsdelete($id=NULL){

	$this->News->delete($id);
 	$this->Session->setFlash('Your news deleted successfully.');
 	$this->redirect(array('action' => 'index'));
 }
/*
* Method newsactivedeactive
* Purpose: admin can make active / deactive News
* Parameter : None
*
* _______________________________________________________________________________
*/

   function newsactivedeactive($id=NULL){
        	$this->checkadmin();
        	$a=0;
             $this->autoRender=false;
             if($this->RequestHandler->isAjax()){
             Configure::write('debug', 0);
                if(!empty($id)){
                    $results=$this->News->find('all',array('conditions'=>array('MD5(News.id)' =>$id)));

                    if($results[0]['News']['status']=='1'){
                                $a = 'inactive_'.$id;
                                        $data=array(
                                        'status'=>0,
                                        'id' =>$results[0]['News']['id']);
                                }else{
                                        $a = 'active_'.$id;
                                        $data=array(
                                        'status'=>1,
                                        'id' =>$results[0]['News']['id']);
                                }
                                $qq = "update news_masters set status = '".$data['status']."' where news_masters.id = ".$data['id'];
                                $this->News->query($qq);
                                echo $a;

                        }

          }


   }
}
?>