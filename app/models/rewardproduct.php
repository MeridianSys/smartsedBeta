<?php
class Rewardproduct extends AppModel
 {
   
 	var $name = 'reward_product';
        var $validate = array(
            
            'product_name' => array(
                            'required'=>array('rule'=> array('notEmpty'),'message' => 'Enter the Product Name')
                            ),
			'point' => array(
                            'required'=>array('rule' => 'numeric','message' => 'Please supply points in numbers')
                            ),
             'product_description' => array(
                            'required'=>array('rule'=> array('notEmpty'),'message' => 'Enter the description for company')
                            ),
             'code_file' => array(
                            'rule' => array('extension',array('csv')),'required' => true,'message' => 'Invalid file tyle use [ csv ] for product code'
                            ),
             'product_image' => array(
                                 'rule' => array('extension',array('jpeg','jpg','png','gif','GIF','JPEG')),'required' => true,'message' => 'Invalid file tyle use [ jpeg , jpg , png , gif , GIF , JPEG ]')
                            
                            
			);
    
}
?>
