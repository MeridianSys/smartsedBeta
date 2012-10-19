<?php


class Poll extends AppModel {
var $name = 'polls';

   var $validate = array(
			 'poll_questions' => array(
                            'required'=>array('rule'=> array('notEmpty'),'message' => 'Please enter the Question for Poll')
                            )
               
                            
			);
			
			
	 var $hasMany = array(
				'Pollanswer' => array(
		 		'className' => 'Pollanswer',
		 		'foreignKey' => 'poll_qid',
		 		'dependent'=> true
 				),
 				
 		);
 		
  
}// End Class
?>
