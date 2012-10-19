<?php
class Adminaddbook extends AppModel
 {
   var $name = 'admin_emailaddresse';
    var $validate = array(
			'group_type' => array(
                            'required'=>array('rule'=> array('notEmpty'),'message' => 'Please enter the group type')
                            )
                          
			);

}
?>