<?php
class Staticcontent extends AppModel
 {
   var $name = 'static_content';
   var $validate = array(
			'tab_name' => array(
                            'required'=>array('rule'=> array('notEmpty'),'message' => 'Please enter english title')
                            ),
                        'tab_name_chn' => array(
                            'required'=>array('rule'=> array('notEmpty'),'message' => 'Please enter chinese title')
                            ),
                        'content' => array(
                            'required'=>array('rule'=> array('notEmpty'),'message' => 'Please enter english content')
                            ),
                        'content_chn' => array(
                            'required'=>array('rule'=> array('notEmpty'),'message' => 'Please enter chinese content')
                            )

			);

}
?>