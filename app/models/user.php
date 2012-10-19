<?php
class User extends AppModel
 {
   var $name = 'User';

    var $hasOne = array('Userprofile');

   
  function beforeValidate() {

    $action = Configure::read('contoller')->params['action'];
    App::import('Component', 'SessionComponent');
    $session = new SessionComponent();
    $ret = $session->read('Config.language');
    
    switch($action)  {

        case 'register':
            if($ret=='en-gb') {
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
            } else {
                $this->validate = array('email' => array(
                                    'required'=>array('rule' => 'email', true, 'message' => '请输入一个有效的电子邮件地址'),
                                    'is_unique'=>array('rule' => array('isUnique'), 'message' => '该电子邮件地址已经在我们的数据库')

                                ),
                               'password' => array(
                                   'required' => array('rule'=> array('notEmpty'), 'last'=>true, 'message' => '请输入密码'),
                                   'minlength' => array('rule' => array('minLength', 6), 'message'=>'密码必须至少6个字符')),
                                'cpassword' => array(
                                    'required' => array('rule'=> array('notEmpty'), 'last'=>true, 'message' => '请输入确认密码'),
                                    'confirm' => array( 'rule' => array('confirm' ,'password' ), 'message' => '密码不匹配' )
                                    ),

                                );
            }
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
