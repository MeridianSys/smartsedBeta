<?php
class Charity extends AppModel
 {
   var $name = 'charities';

   var $validate = array(
			'title' => array(
                            'required'=>array('rule'=> array('notEmpty'),'message' => 'Please enter the Title for charity')
                            ),
               'definition' => array(
                            'required'=>array('rule'=> array('notEmpty'),'message' => 'Enter the definition of Charity')
                            ),
                'detail' => array(
                            'required'=>array('rule'=> array('notEmpty'),'message' => 'Enter the detail of Charity')
                            ),
                 'logo' => array(
                                 'rule' => array('extension',array('jpeg','jpg','png','gif','GIF','JPEG')),'required' => true,'message' => 'Invalid file tyle')
                            
                            
			);
			
			
      //pr($this->params);
}
?>