<?php
class Sponsern extends AppModel
 {
   var $name = 'sponsern';

   var $validate = array(
			'company_name' => array(
                            'required'=>array('rule'=> array('notEmpty'),'message' => 'Please enter the company name')
                            ),
               'description' => array(
                            'required'=>array('rule'=> array('notEmpty'),'message' => 'Enter the description for company')
                            ),
                 'logo' => array(
                                 'rule' => array('extension',array('jpeg','jpg','png','gif','GIF','JPEG')),'required' => true,'message' => ' use [ jpeg , jpg , png , gif , GIF , JPEG ] file types')
                            
                            
			);
   
   
   var $hasMany = array('Sponserncontent' =>
		       array('className' => 'Sponserncontent',
                      'foreignKey' => 'sponsern_id',
                       
                      )
	);
   
}
?>