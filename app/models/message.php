<?php


class Message extends AppModel {

/**
 * Name of the model.
 *
 * @var string
 * @access public
 */

   var $name = 'Message';

/**
 * Custom database table name.
 *
 * @var string
 * @access public
 */

   var $useTable = false;


   function beforeValidate() {

		$action = Configure::read('contoller')->params['action'];

		switch ($action) {

			case 'compose':
                          //  pr(Configure::read('contoller')->params);
                            if($this->data['Message']['rad']== 'group') {
                                $this->validate = array(
                                        'groupid' => array(
                                                      'rule' => array('multiple', array('min' => 1)), 'last'=> true,'message' => 'Please select atleast one checkbox'                                        ),
					'subject' => array(
						'required' => array('rule'=> array('notEmpty'), 'last'=>true, 'message' => 'Please enter subject')
					),
					'message' => array(
						'required' => array('rule'=> array('notEmpty'), 'last'=>true, 'message' => 'Please enter message')
					),
				);
                            } else {
				$this->validate = array(
					'to' => array(
						'required' => array('rule'=> array('notEmpty'), 'last'=>true, 'message' => 'Please select recepients')
					),
					'subject' => array(
						'required' => array('rule'=> array('notEmpty'), 'last'=>true, 'message' => 'Please enter subject')
					),
					'message' => array(
						'required' => array('rule'=> array('notEmpty'), 'last'=>true, 'message' => 'Please enter message')
					),
				);
                            }
                          // pr($this->validate);
			break;


			case 'reply':
				$this->validate = array(
					'subject' => array(
						'required' => array('rule'=> array('notEmpty'), 'last'=>true, 'message' => 'Please enter subject')
					),
					'message' => array(
						'required' => array('rule'=> array('notEmpty'), 'last'=>true, 'message' => 'Please enter message')
					),
				);
			break;


			case 'report_spam':
				$this->validate = array(
					'spam_reason' => array(
						'required' => array('rule'=> array('notEmpty'), 'last'=>true)
					)
				);
			break;


			default:
				$this->validate = array();
			break;
		}
	}

}
?>
