<?php


class Featurequestion extends AppModel {
var $name = 'featured_questions';

//add the following $hasMany definition
    var $hasMany = array('Featureanswer' =>
		array('className' => 'Featureanswer',
                      'foreignKey' => 'qid'
		      )
	);

}// End Class
?>
