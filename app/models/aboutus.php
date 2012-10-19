<?php
class Aboutus extends AppModel
 {
   var $name = 'aboutus_master';
   var $validate = array(
			'tab_name' => array(
                            'required'=>array('rule'=> array('notEmpty'),'message' => 'Please enter the about us tab')
                            ),
               'content' => array(
                            'required'=>array('rule'=> array('notEmpty'),'message' => 'Enter the content')
                            )
                            
			);
			
}
?>