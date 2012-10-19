<?php
class Cafeseriesquestion extends AppModel
 {
   var $name = 'cafeseries_question';

   var $hasMany = array('Cafeseriesanswer' =>
		       array('className' => 'Cafeseriesanswer',
                      'foreignKey' => 'qid'
                      
 		      )
	);
 
}
?>