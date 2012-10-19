<?php
class Reward extends AppModel
 {
   
 	var $name = 'reward';
     var $validate = array(
			'provider_name' => array(
                            'required'=>array('rule'=> array('notEmpty'),'message' => 'Please enter the company name')
                            ),
             'description' => array(
                            'required'=>array('rule'=> array('notEmpty'),'message' => 'Enter the description for company')
                            ),
             'image_name' => array(
                                 'rule' => array('extension',array('jpeg','jpg','png','gif','GIF','JPEG')),'required' => true,'message' => 'Invalid file tyle use [ jpeg , jpg , png , gif , GIF , JPEG ]')
                            
                            
			);
    
}
?>