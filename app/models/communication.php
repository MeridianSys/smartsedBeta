<?php
class Communication extends AppModel
 {
   
 	var $name = 'communication';
        var $validate = array(
			'tag' => array(
                            'required'=>array('rule'=> array('notEmpty'),'message' => 'Please enter tag')
                            ),
                       'subject' => array(
                            'required'=>array('rule'=> array('notEmpty'),'message' => 'Enter the subject')
                            ),
                       'message' => array(
                            'required'=>array('rule'=> array('notEmpty'),'message' => 'Please enter the email content')
                            ),
                        'bottom' => array(
                            'required'=>array('rule'=> array('notEmpty'),'message' => 'Please enter the bottom mark')
                            )

			);
}
?>
