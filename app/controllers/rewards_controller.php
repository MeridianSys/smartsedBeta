<?php
/* 
#################################################################################
#																				#
#   User Controller 															#
#   file name        	: admins_controller.php									#
#   Developed        	: Meridian Radiology 									#	
#   Intialised Variables: name , helpers ,components , uses , layout = (admin)  #
#																				#
#																				#
#################################################################################
*/
class RewardsController extends AppController {

	 var $name = 'Rewards';
	 var $layout = 'admin';
	 var $conf = array();
	 var $uses = array('Reward','Rewardproduct','Country_master','Productcode');
	 var $components = array('Image','Usercomponent','RequestHandler');
	 var $helpers = array('html', 'Form', 'javascript','Ajax','session','Thickbox');
	 
function index(){
 $this->layout = 'admin';
    $this->checkadmin();
    $this->Reward->find('all');
    $this->paginate = array(
             'limit' => 25,
             'page'=>1,

                     );

    $data = $this->paginate('Reward');
    $this->set('data',$data);
}
/*___________________________________________________________________________________________________
* 
* Method     : newreward
* Purpose    : New Reward 
* Parameters : None
* 
* ___________________________________________________________________________________________________
*/	 
function newreward() {
$this->layout = 'admin';
$this->checkadmin();
    if(!empty($this->data['Reward'])){
        $this->Reward->set($this->data);
        if($this->Reward->validates()) {
            $rval = $this->Usercomponent->imageGenerateKey();
                $ext = explode('.',$this->data['Reward']['image_name']['name']);
                $iname=$rval.'_'.time().'.'.$ext[1];
                if(move_uploaded_file($this->data['Reward']['image_name']['tmp_name'] ,_ROOT_BASE_PATH."img/reward/".$iname)){
                        $this->data['Reward']['image_name'] = $iname;
                        $this->Reward->save($this->data);
                        $this->redirect('index');
                }
          }
    }
}
    
/*
_______________________________________________________________________________
* Method rewardactivedeactive
* Purpose: admin can make deactive the user  
* Parameter : None
* 
* _______________________________________________________________________________
*/
function rewardactivedeactive($id=NULL,$page=NULL){
    $this->checkadmin();
    $a=0;
    $this->autoRender=false;
      if($this->RequestHandler->isAjax()){
       Configure::write('debug', 0);
        if(!empty($id)){
        $results=$this->Reward->find('all',array('conditions'=>array('MD5(Reward.id)' =>$id)));
            if($results[0]['Reward']['status']=='1'){
                $a = 'inactive_'.$id;
                        $data=array(
                        'status'=>0,
                        'id' =>$results[0]['Reward']['id']);
                }else{
                        $a = 'active_'.$id;
                        $data=array(
                        'status'=>1,
                        'id' =>$results[0]['Reward']['id']);
                }

            $this->Reward->query("update rewards set status = '".$data['status']."' where id = ".$data['id']);
            echo $a;
         }
    }	
}	
/*
_______________________________________________________________________________
* Method editreward
* Purpose: admin can edit the featured content  
* Parameter : None
* 
* _______________________________________________________________________________
*/	
function editreward($id=NULL){
  $this->layout = 'admin';
     $this->checkadmin();
     $this->Reward->id = $id;
   if (empty($this->data)) {
      $this->data = $this->Reward->read();
      $this->set('id',$id);
   }else{
       $data = array(
            'id'=>$this->data['Reward']['id'],
            'provider_name'=>$this->data['Reward']['provider_name'],
            'description'=>$this->data['Reward']['description']
       );
        $this->Reward->save($data,$validate = false);
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
function editrewardimage($id=NULL){
    $this->checkadmin();
     if(isset($_FILES['data']['name']['Reward']['image_name']) && $_FILES['data']['name']['Reward']['image_name']!=''){
       $ext = explode('/', $_FILES['data']['type']['Reward']['image_name']);
        if($ext[1]=='jpeg' || $ext[1]=='jpeg' || $ext[1]=='JPEG' || $ext[1]=='png' || $ext[1]=='JPG' || $ext[1]=='jpg' || $ext[1]=='PNG' || $ext[1]=='GIF' || $ext[1]=='gif'){
            $rval = $this->Usercomponent->imageGenerateKey();
            $ext = explode('.',$_FILES['data']['name']['Reward']['image_name']);
            $iname=$rval.'_'.time().'.'.$ext[1];
            list($width, $height, $type, $attr) = getimagesize($this->data['Reward']['image_name']['tmp_name']);
                if(move_uploaded_file($_FILES['data']['tmp_name']['Reward']['image_name'] ,_ROOT_BASE_PATH."img/reward/" . $iname)){
                    @chmod(_ROOT_BASE_PATH."img/reward/", 0777);
                    @chmod(_ROOT_BASE_PATH."img/reward/" .$imName, 0777);
                    $delIm = $this->Reward->find('all',array('conditions'=>$id,'fields'=>'image_name'));
                    @unlink(_ROOT_BASE_PATH."img/reward/" .$delIm[0]['Reward']['image_name']);
                    $this->Reward->updateAll(array('Reward.image_name' =>' "' .$iname.' " '), array('Reward.id' => $id));
                    $this->Session->setFlash('Logo updated successfully');
                    $this->redirect('editreward/'.$id);
                }
         }else{
            $this->Session->setFlash('Invalid file type');
            $this->redirect('editreward/'.$id);
	 }
    } else{
	$this->layout = '';
    	$results=$this->Reward->find('all',array('conditions'=>array('id' =>$id)));
    	$this->set('result',$results[0]);
    }
}
/*_______________________________________________________________________________
* 
* Method rewardproduct
* Purpose: admin can make new product for provider   
* Parameter : $id 
* 
* _______________________________________________________________________________
*/    
function rewardproduct($id=NULL){
$this->layout = 'admin';
$this->checkadmin();
$this->Reward->find('all');
$this->paginate = array(
         'limit' => 25,
         'page'=>1,
         'conditions'=>'provider_id='.$id
                 );
$data = $this->paginate('Rewardproduct');
$name = $this->Reward->find('all',array('fields'=>'provider_name', 'conditions'=>'id = '.$id));
$this->set('name',$name[0]['Reward']['provider_name']);
$this->set('data',$data);
$this->set('id',$id);
}
/*_______________________________________________________________________________
* 
* Method rewardproduct
* Purpose: admin can make new product for provider   
* Parameter : $id 
* 
* _______________________________________________________________________________
*/  
function newproduct($id=NULL){
    $this->layout = 'admin';
    $this->checkadmin();
    $this->set('id',$id);
    $name = $this->Reward->find('all',array('conditions'=>'id = '.$id));
    $this->set('name',$name[0]['Reward']['provider_name']);
    $countries = $this->Country_master->find('all',array('fields' => 'Country_master.id,Country_master.country_name','conditions'=>'Country_master.status =1'));
    $this->set('countryList',$countries);
     if(!empty($this->data['Rewardproduct'])){
        $this->Rewardproduct->set($this->data);
             if($this->Rewardproduct->validates()) {
    //list($width, $height, $type, $attr) = getimagesize($this->data['Reward']['logo']['tmp_name']);
        $rval = $this->Usercomponent->imageGenerateKey();
            $ext = explode('.',$this->data['Rewardproduct']['product_image']['name']);
            $iname=$rval.'_'.time().'.'.$ext[1];
            if(move_uploaded_file($this->data['Rewardproduct']['product_image']['tmp_name'] ,_ROOT_BASE_PATH."img/reward/product/".$iname)){
                    $this->data['Rewardproduct']['product_image'] = $iname;
                    if($this->params['form']['preference']==1){
                        $this->data['Rewardproduct']['preference']=$this->params['form']['coutryid'];
                    }else{
                        $this->data['Rewardproduct']['preference']=0;
                    }
                    // Code for the code_file // for product //
                     $code_val = $this->Usercomponent->imageGenerateKey();
                     $code_iname=$code_val.'_'.$this->data['Rewardproduct']['code_file']['name'];
                    if(move_uploaded_file($this->data['Rewardproduct']['code_file']['tmp_name'] ,_ROOT_BASE_PATH."img/reward/product/code/".$code_iname)){
                         $this->data['Rewardproduct']['code_file'] = $code_iname;

                         if (($handle = fopen(_ROOT_BASE_PATH."img/reward/product/code/".$code_iname, "r")) !== FALSE) {
                         $this->Rewardproduct->save($this->data);// save the product data
                         $code_pro = array();
                         $countproduct = 0;
                         while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                            if(!empty($data[0])){
                                    $code_pro = array(
                                    'id'=>'',
                                    'cat_id'=>$this->data['Rewardproduct']['provider_id'],
                                    'pid'=>$this->Rewardproduct->id,
                                    'product_code'=>$data[0]
                                );
                            $this->Productcode->save($code_pro);
                            }
                          $countproduct++;
                          }
                             fclose($handle);
                             @unlink(_ROOT_BASE_PATH."img/reward/product/code/" .$code_iname);
                             $this->Rewardproduct->updateAll(array('code_file'=>$countproduct),array('Rewardproduct.id'=>$this->Rewardproduct->id));
                             $this->redirect('rewardproduct/'.$id);
                         }
                   }
              }
         }
    }
}
/*
_______________________________________________________________________________
* Method productactivedeactive
* Purpose: admin can make deactive the product  
* Parameter : None
* 
* _______________________________________________________________________________
*/
function productactivedeactive($id=NULL,$page=NULL){
$this->checkadmin();
$a=0;
$this->autoRender=false;
if($this->RequestHandler->isAjax()){
Configure::write('debug', 0);
if(!empty($id)){
$results=$this->Rewardproduct->find('all',array('fields'=>'id,status', 'conditions'=>array('MD5(Rewardproduct.id)' =>$id)));
    if($results[0]['Rewardproduct']['status']=='1'){
        $a = 'inactive_'.$id;
                $data=array(
                'status'=>0,
                'id' =>$results[0]['Rewardproduct']['id']);
        }else{
                $a = 'active_'.$id;
                $data=array(
                'status'=>1,
                'id' =>$results[0]['Rewardproduct']['id']);
        }
    $this->Rewardproduct->query("update reward_products set status = '".$data['status']."' where id = ".$data['id']);
    echo $a;
    }
  }	
}	

/*
_______________________________________________________________________________
* Method editreward
* Purpose: admin can edit the featured content  
* Parameter : None
* 
* _______________________________________________________________________________
*/	
function editproduct($id=NULL,$pid=NULL){
   $this->layout = 'admin';  // Check admin athontication //
   $this->checkadmin();
   $name = $this->Reward->find('all',array('conditions'=>'id = '.$pid));
         $this->set('name',$name[0]['Reward']['provider_name']);
         $countries = $this->Country_master->find('all',array('fields' => 'Country_master.id,Country_master.country_name','conditions'=>'Country_master.status =1'));
         $this->set('countryList',$countries);
         $countPro = $this->Productcode->query("SELECT COUNT(pid) as num FROM product_code_masters WHERE pid = ".$id." GROUP BY pid");
	 if(!empty($countPro)){
            $countnum = $countPro[0][0]['num'];
         }else{
             $countnum = 0;
         }
           $this->set('countnum',$countnum);
	   $this->Rewardproduct->id = $id;
       if (empty($this->data)) {
          $this->data = $this->Rewardproduct->read();
          $this->set('id',$id);
       }else{
          $data = array('id'=>$id,
                    'product_name'=>$this->data['Rewardproduct']['product_name'],
                    'product_description'=>$this->data['Rewardproduct']['product_description'],
                    'point'=>$this->data['Rewardproduct']['point'],
                    'provider_id'=>$this->data['Rewardproduct']['provider_id'],
                    'column_no'=>$this->data['Rewardproduct']['column_no'],
                    'preference'=>($this->params['form']['preference']==1)?$this->params['form']['coutryid']:0 );


                $code_val = $this->Usercomponent->imageGenerateKey();
                $code_iname=$code_val.'_'.$this->data['Rewardproduct']['code_file']['name'];
               if(move_uploaded_file($this->data['Rewardproduct']['code_file']['tmp_name'] ,_ROOT_BASE_PATH."img/reward/product/code/".$code_iname)){
             $data['code_file'] = '';
             if (($handle = fopen(_ROOT_BASE_PATH."img/reward/product/code/".$code_iname, "r")) !== FALSE) {
             $this->Rewardproduct->save($data,$validate = false);// save the product data
             $code_pro = array();
             while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                if(!empty($data[0])){
                    $code_pro = array(
                                    'id'=>'',
                                    'cat_id'=>$this->data['Rewardproduct']['provider_id'],
                                    'pid'=>$id,
                                    'product_code'=>$data[0]
                                    );
                   $this->Productcode->save($code_pro);
                }
              }
         fclose($handle);
         @unlink(_ROOT_BASE_PATH."img/reward/product/code/" .$code_iname);
         $count =$this->Productcode->find('all',array('fields'=>'count(pid) as count','conditions'=>'pid='.$id));
         $this->Rewardproduct->updateAll(array('code_file'=>$count[0][0]['count']),array('Rewardproduct.id'=>$id));
        }
       }else{
           $this->Rewardproduct->save($data,$validate = false);// save the product data
       }
    $this->Session->setFlash('Your post has been updated.');
    $this->redirect('rewardproduct/'.$this->data['Rewardproduct']['provider_id']);
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
function editproductimage($id=NULL,$pid=NULL){
$this->checkadmin();
if(isset($_FILES['data']['name']['Rewardproduct']['product_image']) && $_FILES['data']['name']['Rewardproduct']['product_image']!=''){
$ext = explode('/', $_FILES['data']['type']['Rewardproduct']['product_image']);
    if($ext[1]=='jpeg' || $ext[1]=='jpeg' || $ext[1]=='JPEG' || $ext[1]=='png' || $ext[1]=='JPG' || $ext[1]=='jpg' || $ext[1]=='PNG' || $ext[1]=='GIF' || $ext[1]=='gif'){
        $rval = $this->Usercomponent->imageGenerateKey();
        $ext = explode('.',$_FILES['data']['name']['Rewardproduct']['product_image']);
        $iname=$rval.'_'.time().'.'.$ext[1];
        list($width, $height, $type, $attr) = getimagesize($this->data['Rewardproduct']['product_image']['tmp_name']);
            if(move_uploaded_file($_FILES['data']['tmp_name']['Rewardproduct']['product_image'] ,_ROOT_BASE_PATH."img/reward/product/" . $iname)){
                    @chmod(_ROOT_BASE_PATH."img/reward/product", 0777);
                    @chmod(_ROOT_BASE_PATH."img/reward/product/" .$imName, 0777);
                    $delIm = $this->Rewardproduct->find('all',array('conditions'=>$id,'fields'=>'product_image'));
                    @unlink(_ROOT_BASE_PATH."img/reward/product/" .$delIm[0]['Rewardproduct']['product_image']);
                    $this->Rewardproduct->updateAll(array('Rewardproduct.product_image' =>' "' .$iname.' " '), array('Rewardproduct.id' => $id));                                     $this->Session->setFlash('Logo updated successfully');
                    $this->redirect('editproduct/'.$id.'/'.$pid);
            }
        }else{
            $this->Session->setFlash('Invalid file type');
            $this->redirect('editproduct/'.$id.'/'.$pid);
        }
    }else{
        $this->layout = '';
        $results=$this->Rewardproduct->find('all',array('conditions'=>array('id' =>$id)));
        $this->set('result',$results[0]);
    }
}

function productindex(){
    $this->checkadmin();
//     $this->paginate = array(
//            'order'=>array('order ASC'),
//            'limit' => 2
//            );
    $column = array();
    for($i = 1; $i <= 3; $i++){
      //$productQueryOne = $this->paginate('Rewardproduct', array('status'=>1, 'column_no'=>$i));

     $productQueryOne =    $this->Rewardproduct->find('all', array(
                                                            'conditions' => array('Rewardproduct.status'=>1, 'column_no'=>$i),
                                                            'order' => array('order ASC')));
            $this->set('column'.$i,  $productQueryOne);
     }
      
 }

function product_item(){
    $this->autoRender=false;
      if($this->RequestHandler->isAjax()){
        Configure::write('debug', 2);
        $data = $_POST;
    foreach($data['items'] as $d){
       $item_no = split("_",$d['id']); //split item_3 to get 3 and that is our item table id
       $d['Rewardproduct']['id'] = $item_no[1];
       $col_no = split("_", $d['column_no']); //split
       $d['Rewardproduct']['column_no'] = $col_no[1];
       $this->Rewardproduct->updateAll(array('column_no'=>$col_no[1],'order'=>$d['order']),array('id'=>$item_no[1]));
    }
   }
 }
}// End Class
?>
