<?php
class User extends AppModel
 {
   var $name = 'User';

    var $hasOne = array('Userprofile');

   
  function beforeValidate() {

    $action = Configure::read('contoller')->params['action'];
    
    switch($action)  {

        case 'register':
            $this->validate = array('email' => array(
                                    'required'=>array('rule' => 'email', true, 'message' => 'Please enter a valid email address'),
                                    'is_unique'=>array('rule' => array('isUnique'), 'message' => 'That email address is already in our database')

                                ),
                               'password' => array(
                                   'required' => array('rule'=> array('notEmpty'), 'last'=>true, 'message' => 'Please enter password'),
                                   'minlength' => array('rule' => array('minLength', 6), 'message'=>'Password must be of atleast 6 characters')),
                                'cpassword' => array(
                                    'required' => array('rule'=> array('notEmpty'), 'last'=>true, 'message' => 'Please enter confirm password'),
                                    'confirm' => array( 'rule' => array('confirm' ,'password' ), 'message' => 'Password not matched' )
                                    ),
                             
                                );
          Break;

          case 'login':
              $this->validate = array('email' => array(
                                            'required' => array('rule'=> array('notEmpty'), 'last'=>true),
                                    	),
					'password' => array(
						'required' => array('rule'=> array('notEmpty'), 'last'=>true)
					)
                                    );
	 break;
    }

  }


   


function confirm( $field=array(), $compare=null) {
        foreach( $field as $key => $value ){
            $firstValue = $value;
            $secondValue = $this->data[$this->name][$compare];
           // echo $firstValue .'!=='. $secondValue .'<br />';
            if($firstValue !== $secondValue) {
                return false;
            } else {
                continue;
            }
        }
        return true;
    }

   

}
?>
