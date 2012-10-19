<?php
/* 
#################################################################################
#																				#
#   User Controller 															#
#   file name        	: redeems_controller.php									#
#   Developed        	: Meridian Radiology 									#	
#   Intialised Variables: name , helpers ,components , uses , layout = (admin)  #
#																				#
#																				#
#################################################################################
*/
class RedeemsController extends AppController {

	 var $name = 'Redeems';
	 var $helpers = array('html', 'Form', 'javascript','Ajax','Session','Thickbox','Crypt');
	 var $layout = 'admin';
	 var $conf = array();
	 var $uses = array('Redeem','User', 'Ediv_user_master','Rewardproduct','user_winning_points','Productcode');
	 var $components = array('Image','Usercomponent','Email','Session');
	 
	 
	
/*___________________________________________________________________________________________________
* 
* Method     : index
* Purpose    : making list of the redeem points
* Parameters : None
* 
* ___________________________________________________________________________________________________
*/	 
function index(){
  $this->layout = 'admin';
    $this->checkadmin();
    $string = strtolower(date("F"));
    $show =$this->Redeem->find('all',array('fields'=>'id,redeem_points,uid,redeem_items,qty,DATE_FORMAT(datetime,"%M") as datetime'));
    $this->paginate = array(
            'fields'=>'id,redeem_points,uid,redeem_items,qty,DATE_FORMAT(datetime,"%M") as datetime',
            'limit' => 25,
             'page'=>1,
     );
    $data = $this->paginate('Redeem');
    $this->set('data',$data);
}
/////////////// Redeem XLS download /////////////////
function redemptiondownload(){

    $data = $this->Redeem->find('all',array('fields'=>'id,redeem_points,uid,redeem_items,qty,DATE_FORMAT(datetime,"%M") as datetime'));

    $rval = $this->Usercomponent->imageGenerateKey();

if(count($data)>0){
    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Content-Type: application/force-download");
    header("Content-Type: application/octet-stream");
    header("Content-Type: application/download");;
    header("Content-Disposition: attachment;filename=".$rval.".xls ");
    header("Content-Transfer-Encoding: binary");
?>
<table width="100%" border="1">
     <tr bgcolor="#F4D695"><td colspan="5"  width="100%">
     <table width="250" bgcolor="#E0D484" border="1">
     <tr><td><strong>Organization  :</strong></td><td>Smartsed</td></tr>
     <tr><td><strong>Type :</strong></td><td>Redemption Details</td></tr>
     <tr><td><strong>Date of download :</strong></td><td><?php echo date('Y-m-d');?></td></tr>
     </table>
     </td></tr>
          <tr bgcolor="#F4D695">
            <td valign="top" style="padding-left:30px;"><b>User Name</b></td>
            <td valign="top" style="padding-left:30px;"><b>Redeem Amount ($)</b></td>
            <td valign="top" style="padding-left:30px;"><b>Redeem Items</b></td>
            <td valign="top" style="padding-left:30px;"><b>Quantity</b></td>
                <td valign="top" style="padding-left:30px;"><b>Month</b></td>
          </tr>
      <?php
            for($i=0;$i<count($data);$i++){
                $userInfo = $this->get_user_info($data[$i]['Redeem']['uid'], 'Userprofile.first_name,Userprofile.last_name,User.activationkey',false);
                $userName = ucwords($userInfo['Userprofile']['first_name']).' '.ucwords($userInfo['Userprofile']['last_name']);
            ?>
      <tr><td><?php echo $userName; ?></td><td><?php echo $data[$i]['Redeem']['redeem_points'] ?></td><td><?php echo $this->productname($data[$i]['Redeem']['redeem_items']); ?></td><td><?php echo $data[$i]['Redeem']['qty']; ?></td><td><?php echo $data[$i][0]['datetime'];?></td></tr>
      <?php
            }
       ?>
</table>
<?php
    exit();
}else{
    $this->Session->setFlash('Your required file is unable to create ');
    $this->redirect(array('action' => 'index'));
}
exit();

}


function rewardtouser(){
    $this->layout = 'admin';
    $this->checkadmin();
    $string = strtolower(date("F"));
    $query = 'SELECT SUM(points),uid , DATE_FORMAT(DATETIME,"%M")FROM ediv_user_masters WHERE uid <> "" and DATE_FORMAT(DATETIME,"%M") ="'.$string.'" GROUP BY uid ORDER BY SUM(points) DESC ';
    $maxPoint = $this->Ediv_user_master->query($query);
    $counter = count($maxPoint)*10/100;
    if($counter < 1){
           $val = ceil($counter);
    }else{
            $val = intval($counter);
    }
    $this->set('maxUser',$maxPoint);
    $this->set('limit',$val);
}

function sendrewarduser($key=NULL){
    $info = $this->User->find('all',array('fields'=>'User.email,Userprofile.first_name,User.id','conditions'=>'User.activationkey= "'.$key.'" '));
    if($this->emailtorewarduser($info[0]['User']['email'],$info[0]['User']['first_name'],$key,$info[0]['User']['id'])){
        $this->redirect('rewardtouser');
    }else{
        $this->redirect('rewardtouser');

    }
}

function emailtorewarduser($email=NULL,$fname=NULL,$id=NULL,$uid=NULL){
    $this->Email->to = $email;
    $this->Email->bcc = array('nikesh.kumar@meridiansys.com');
    $this->Email->subject = 'Redeem your points';
    $this->Email->from = 'Smartsed Web App <admin@smartsed.com>';
    $this->Email->template = 'redeem'; // note no '.ctp'
    $this->set('name', $fname);
    $this->set('activation_url' , "<a href=\""._HTTP_PATH."redeems/redeempoint/".$id."\">"._HTTP_PATH."redeems/redeempoint/".$id."</a>");
    $this->Email->sendAs = 'both';
    if($this->Email->send()){
        $dataAmount = array();
        $dataAmount['id']='';
        $dataAmount['uid']=$uid;
        $dataAmount['amount']=$this->pointforediv(7);
        $this->user_winning_points->save($dataAmount);
        $this->Session->setFlash("Email send for the user redeem ");
        return true;
    }else{
        return false;
        $this->Session->setFlash("Email not send for the user to redeem ");
    }
}


function redeempoint($msg=NULL) {
    $userID=$this->Session->Read('User.id');  // Reading the session user Id
     if(empty($userID)){
	$this->checkuserlogin($msg); // Stop the accesing the section //
	}
    else{
    $this->layout='default';
    $redeemTransection = $this->Redeem->query('SELECT  SUM(redeem_points) AS redeemPoints FROM  redeems WHERE uid='.$userID.' GROUP BY uid');
    $winningAmount = $this->user_winning_points->query('SELECT  SUM(amount) AS amount FROM  user_winning_points WHERE uid='.$userID.' GROUP BY uid');

    $countryUser = $this->User->find('all',array('fields'=>'Userprofile.country','conditions'=>'User.id = '.$userID.' '));
    if($countryUser[0]['Userprofile']['country']==40){
        $disValue= CurMul;
    }else{
        $disValue = 1;
    }
    $this->set('disValue',$disValue); $disValue;
    $remainingPoints = intval($winningAmount[0][0]['amount']) - intval($redeemTransection[0][0]['redeemPoints']);
    $this->set('allPoints',$remainingPoints);
    $this->paginate = array(
            'order'=>array('order ASC'),
            'limit' => 9
            );
     $column = array();
    for($i = 1; $i <= 3; $i++){
        $rewardProductQuery = $this->paginate('Rewardproduct', array('Rewardproduct.status=1 AND column_no='.$i.' AND (Rewardproduct.preference=0 OR  Rewardproduct.preference='.$countryUser[0]['Userprofile']['country'].')'));

        //$this->Rewardproduct->find('all',array('conditions'=>'Rewardproduct.status=1 AND column_no='.$i.' AND (Rewardproduct.preference=0 OR  Rewardproduct.preference='.$countryUser[0]['Userprofile']['country'].')','order'=>array('order ASC')));
     
            $this->set('column'.$i,  $rewardProductQuery);
     }

    
    if($msg==2){
         $this->Session->setFlash(__d('redeem','redeem_msg19',true));
    }
    if($msg==3){
        $this->set('flag',3);
        // $this->Session->setFlash('You have successfully redeem your points.');
         
    }if($msg==4){
        $this->set('chkVal','chkVal');
        $this->Session->setFlash(__d('redeem','redeem_msg20',true));

    }
  }
}

function addtocart() {
    $this->checkuserlogin(); 
    $this->layout='default';
     if(!empty($_SESSION['cart'])) {
        $cartCount = count($_SESSION['cart']);
        if(in_array($this->data['Redeem']['prId'],$_SESSION['check']))  {
             $_SESSION['check'][] = $this->data['Redeem']['prId'];
            for($i=0; $i<count($_SESSION['cart']); $i++) {
                if($_SESSION['cart'][$i]['pid']== $this->data['Redeem']['prId']) {
                    $existQty = $_SESSION['cart'][$i]['qty']+$this->data['Redeem']['qty'];
                     $_SESSION['cart'][$i]['qty']=$existQty;
                     $existingPoint = $_SESSION['cart'][$i]['pts']+$this->data['Redeem']['points'];
                     $_SESSION['cart'][$i]['pts']=$existingPoint;
                }
            }
        } else {
            $_SESSION['check'][] = $this->data['Redeem']['prId'];
            $_SESSION['cart'][$cartCount]['pid'] = $this->data['Redeem']['prId'];
            $_SESSION['cart'][$cartCount]['item'] = $this->data['Redeem']['item'];
            $_SESSION['cart'][$cartCount]['qty'] = $this->data['Redeem']['qty'];
            $_SESSION['cart'][$cartCount]['pts'] = $this->data['Redeem']['points'];
        }
        $this->redirect('redeempoint');
    } else {
        $_SESSION['check'][] = $this->data['Redeem']['prId'];
        $_SESSION['cart'][0]['pid'] = $this->data['Redeem']['prId'];
        $_SESSION['cart'][0]['item'] = $this->data['Redeem']['item'];
        $_SESSION['cart'][0]['qty'] = $this->data['Redeem']['qty'];
        $_SESSION['cart'][0]['pts'] = $this->data['Redeem']['points'];
        $this->redirect('redeempoint');
    }
}

function delete_product($id=NULL) {
    if($id!='') {
        for($i=0; $i<count($_SESSION['cart']); $i++) {
            if($_SESSION['cart'][$i]['pid']== $id) {
                for($c=0; $c<count($_SESSION['check']); $c++) {
                    if($_SESSION['check'][$c]==$id){
                        unset($_SESSION['check'][$c]);
                    }
                }
                unset($_SESSION['cart'][$i]);
                $_SESSION['cart'] = array_values($_SESSION['cart']);
            }
        }
        $_SESSION['cart'] = array_values($_SESSION['cart']);
        $_SESSION['check'] = array_values($_SESSION['check']);
    }
    $this->redirect('redeempoint');
}

function update_product() {
    if($this->data['Redeem']['qty']==0) {
        $this->delete_product($this->data['Redeem']['Product_id']);
    } else {
         if(in_array($this->data['Redeem']['Product_id'],$_SESSION['check']))  { 
             $_SESSION['check'][] = $this->data['Redeem']['Product_id'];
            for($i=0; $i<count($_SESSION['cart']); $i++) {
                if($_SESSION['cart'][$i]['pid']== $this->data['Redeem']['Product_id']) {
                     $_SESSION['cart'][$i]['qty']=$this->data['Redeem']['qty'];
                     $_SESSION['cart'][$i]['pts']= $this->data['Redeem']['qty']*$this->data['Redeem']['Point'];

                }
            }
    }
    $this->redirect('redeempoint');
    }

}

function process() {
    $this->checkuserlogin();
    $userID=$this->Session->Read('User.id');  // Reading the session user Id
    if($_SESSION['total'] > $_SESSION['allPoints']) {
         $this->redirect('redeempoint/2');
     } else {
      $_SESSION['pqty']='';
         $chArrayQty = array();
         for($ij=0; $ij<count($_SESSION['cart']); $ij++) {
            $count = $this->Productcode->find('all',array('fields'=>'COUNT(id) AS count','conditions'=>'used = 0 AND pid = '.$_SESSION['cart'][$ij]['pid']));
            if($count[0][0]['count'] < $_SESSION['cart'][$ij]['qty']){
                $chArrayQty[] = $_SESSION['cart'][$ij]['pid'];
            }
         }
         if(empty($chArrayQty)){

           for($i=0; $i<count($_SESSION['cart']); $i++) {
            $arr_redeem['Redeem']['redeem_points'] = $_SESSION['cart'][$i]['pts'];
            $arr_redeem['Redeem']['uid'] = $userID;
            $arr_redeem['Redeem']['redeem_items'] = $_SESSION['cart'][$i]['pid'];
            $proCode = $this->Productcode->find('all',array('fields'=>'product_code','conditions'=>'used = 0 AND pid = '.$_SESSION['cart'][$i]['pid'],'limit'=>$_SESSION['cart'][$i]['qty'],'order'=>'rand()'));
           $redeemQtydata='';
            $codenumber = '';
            for($ji=0;$ji<count($proCode);$ji++){
                $codenumber =$codenumber.''.$proCode[$ji]['Productcode']['product_code'].',';
            }
            $redeemQtydata = substr($codenumber,0,-1);
            $instring = str_replace(",","','",$redeemQtydata);
            $newinstring = "'".$instring."'";
            $this->Productcode->query("UPDATE product_code_masters SET used = '1' , uid = ".$userID." WHERE product_code IN ($newinstring)");
            $arr_redeem['Redeem']['qty'] = $redeemQtydata;
            $arr_redeem['Redeem']['user_status'] = 1;
            $arr_redeem['Redeem']['datetime'] = date('Y-m-d H:i:s');
            $this->Redeem->create();
             // Update quantity //
            $count =$this->Productcode->find('all',array('fields'=>'count(id) as count','conditions'=>'used = "0" and pid='.$_SESSION['cart'][$i]['pid']));
            if(!empty($count)){
                $this->Rewardproduct->updateAll(array('code_file'=>$count[0][0]['count']),array('Rewardproduct.id'=>$_SESSION['cart'][$i]['pid']));
            }else{
                $this->Rewardproduct->updateAll(array('code_file'=>0),array('Rewardproduct.id'=>$_SESSION['cart'][$i]['pid']));
            }
            $this->Redeem->save($arr_redeem);
            $emailPr[]=$this->Redeem->id;
            unset($arr_redeem['Redeem']);
          }
           $_SESSION['cart']='';
           $_SESSION['check']='';
           $_SESSION['total']='';
           $_SESSION['pqty']='';
           $this->redeemProductCodeEmail($userID,$emailPr);
           $this->redirect('redeempoint/3');
         }else{
             $_SESSION['pqty'] = $chArrayQty;
             $this->redirect('redeempoint/4');
         }
     }
}

function productdetail($id=NULL){
$this->layout='';
  $data = $this->Rewardproduct->find('all',array('conditions'=>'Rewardproduct.id='.$id));
  $this->set('data',$data);
}

function redeemProductCodeEmail($id=NULL,$pid=NULL){
    $username = $this->User->find('all',array('fields'=>'User.email,Userprofile.first_name,Userprofile.last_name','conditions'=>'User.id ='.$id));
    $user = ucwords($username[0]['Userprofile']['first_name']).' '.ucwords($username[0]['Userprofile']['last_name']);
    $this->Email->to = $username[0]['User']['email'];
    $this->Email->bcc = array('nikesh.kumar@meridiansys.com');
    $this->Email->subject = 'Redeem Product Information';
    $this->Email->from = 'Smartsed Web App <admin@smartsed.com>';
    $this->Email->template = 'redeemproductcode'; // note no '.ctp'
    $reDeeminfo = $this->Redeem->find('all',array('conditions'=>array('id'=>$pid)));
    $this->set('info', $user);
    $this->set('data', $reDeeminfo);

    $this->Email->sendAs = 'both';
    if($this->Email->send()){
        return true;
    }else{
        return false;
    }

}

}// End Class
?>
