<?php
class Cafeseriescontent extends AppModel
 {
   var $name = 'cafeseries_content';
      
   var $hasMany = array('Cafeseriesquestion' =>
		       array('className' => 'Cafeseriesquestion',
                      'foreignKey' => 'cafeseries_id',
                       
                      )
	);
   
}
?>