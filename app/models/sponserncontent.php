<?php
class Sponserncontent extends AppModel
 {
   var $name = 'sponsern_content';
      
   var $hasMany = array('Sponsernquestion' =>
		       array('className' => 'Sponsernquestion',
                      'foreignKey' => 'sponsern_content_id',
                       
                      )
	);
   
}
?>