<?php


class Feature extends AppModel {
var $name = 'featured_treatments';
 var $validate = array(
			'company_name' => array(
                            'required'=>array('rule'=> array('notEmpty'),'message' => 'Please enter the company name')
                            ),
               'description' => array(
                            'required'=>array('rule'=> array('notEmpty'),'message' => 'Enter the description for company')
                            ),
                 'logo' => array(
                                 'rule' => array('extension',array('jpeg','jpg','png','gif','GIF','JPEG')),'required' => true,'message' => 'Invalid file tyle use [ jpeg , jpg , png , gif , GIF , JPEG ]')
                            
                            
			);
  
}// End Class
?>
