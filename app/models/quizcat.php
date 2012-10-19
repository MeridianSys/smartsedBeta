<?php
class Quizcat extends AppModel
 {
   var $name = 'quiz_category_master';
   var $validate = array(
			   'category_name' => array(
                            'required'=>array('rule'=> array('notEmpty'),'message' => 'Please enter the category name')
                            )
               );

    
}
?>