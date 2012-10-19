<?php
class Admin extends AppModel
 {
   var $name = 'Admin';

   var $validate = array(
			'name' => 'notEmpty',
			'password'  => 'notEmpty'
			);
  /*
   var $validate = array('firstname'=>array(
   								'notEmpty' =>array(
   											'rule'=>'notEmpty',
   											'message'=>'Firstname should not be empty.')
   										),
   						 'lastname'=>array(
   						 		'notEmpty' =>array(
   											'rule'=>'notEmpty',
   					 						'message'=>'Lastname should not be empty.')
   								),
   						   'email' => array(
						                'email' => array(            
						                    'rule' => 'email',
						                    'message' => 'Email should be a valid email address'
						                ),
						                'notEmpty' =>array(
   											'rule'=>'notEmpty',
   						 					'message'=>'Email should not be empty'
						                )
						            ), 
   						 'username'=>array(
   						 		'notEmpty' =>array(
   												'rule'=>'notEmpty',
   						 						'message'=>'Username should not be empty')
   												),
						
						                
   );
   */


}
?>