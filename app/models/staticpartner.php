<?php
class Staticpartner extends AppModel
 {
   var $name = 'static_partner';
   var $validate = array(
			'name' => array(
                            'required'=>array('rule'=> array('notEmpty'),'message' => 'Please enter the partner name')
                            ),
                        'link' => array(
                            'required'=>array('rule'=> array('notEmpty'),'message' => 'Enter the link of the partner')
                            ),
                        'position' => array(
                            'required'=>array('rule'=> array('notEmpty'),'message' => 'Enter the logo position of the partner')
                            ),
                        'logo' => array(
                                 'rule' => array('extension',array('jpeg','jpg','png','gif','GIF','JPEG')),'required' => true,'message' => ' use [ jpeg , jpg , png , gif , GIF , JPEG ] file types')
			);
}
?>