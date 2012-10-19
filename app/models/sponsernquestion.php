<?php
class Sponsernquestion extends AppModel
 {
   var $name = 'sponsern_question';

   var $hasMany = array('Sponsernanswer' =>
		       array('className' => 'Sponsernanswer',
                      'foreignKey' => 'qid'
                      
 		      )
	);
 
}
?>