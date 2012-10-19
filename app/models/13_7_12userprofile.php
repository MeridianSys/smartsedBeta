<?php
class Userprofile extends AppModel
 {
   var $name = 'Userprofile';
   var $belongsTo = array( 'User');

  function beforeValidate() {
    $action = Configure::read('contoller')->params['action'];
    App::import('Component', 'SessionComponent');
    $session = new SessionComponent();
    $ret = $session->read('Config.language');
   
    switch($action)  {
         case 'register':
            
             if($ret=='en-gb') {
               $fvalid = array( 'first_name'=>array(
                                                    'required' => array('rule'=> array('notEmpty'), 'last'=>true,'message' => 'Please enter first name.')
                                                    
                                                    ),
                                      'last_name'=>array(
                                                    'required' => array('rule'=> array('notEmpty'), 'last'=>true,'message' => 'Please enter last name.')
                                                    
                                                     ),
                                      'nativelanguage'=>array(
                                                                'required' => array('rule'=> array('notEmpty'), 'last'=>true,'message' => 'Please select native language.')
                                                              ),
                                      'fluentlanguage'=>array(
                                                                'required' => array('rule'=> array('notEmpty'), 'last'=>true,'message' => 'Please select fluent language.')
                                                              ),
                                      'learnlanguage'=>array(
                                                                'required' => array('rule'=> array('notEmpty'), 'last'=>true,'message' => 'Please select learning language.')
                                                              ),
                                      'address'=>array(
                                                                'required' => array('rule'=> array('notEmpty'), 'last'=>true,'message' => 'Please enter address.')
                                                              ),
                                      'country'=>array(
                                                                'required' => array('rule'=> array('notEmpty'), 'last'=>true,'message' => 'Please select country.')
                                          ),
                                      'secondaryemail'=>array(
                                                                'is_unique'=>array('rule' => array('isUnique'), 'message' => 'That email address is already in our database')
                                          ),
                                         'agree' => array(
                                               'rule' => array('comparison', '!=', 0),
                                               'required' => true,
                                               'message' => 'You must agree to the terms of use',
                                               'on' => 'create'
                                      )

                   );


              if($this->data['Userprofile']['country']=='40'){
                          $svalid = array('state'=>array(
                                                                'required' => array('rule'=> array('notEmpty'), 'last'=>true,'message' => 'Please select state.')
                                                              ),
                                      'city'=>array(
                                                                'required' => array('rule'=> array('notEmpty'), 'last'=>true,'message' => 'Please select city.')
                                                              ),
                                    );
              } elseif($this->data['Userprofile']['country']!='40'){
                         $svalid =     array(
						'otherstate'=>array(
                                                                'required' => array('rule'=> array('notEmpty'), 'last'=>true,'message' => 'Please enter state.')
                                                              ),
                                      'othercity'=>array(
                                                                'required' => array('rule'=> array('notEmpty'), 'last'=>true,'message' => 'Please enter city.')
                                                              ),
						);
                
              }
              
             } else {
                 $fvalid = array( 'first_name'=>array(
                                                    'required' => array('rule'=> array('notEmpty'), 'last'=>true,'message' => '请输入名字。')

                                                    ),
                                      'last_name'=>array(
                                                    'required' => array('rule'=> array('notEmpty'), 'last'=>true,'message' => '请输入姓氏。')

                                                     ),
                                      'nativelanguage'=>array(
                                                                'required' => array('rule'=> array('notEmpty'), 'last'=>true,'message' => '请选择母语。')
                                                              ),
                                      'fluentlanguage'=>array(
                                                                'required' => array('rule'=> array('notEmpty'), 'last'=>true,'message' => '请选择语言流畅。')
                                                              ),
                                      'learnlanguage'=>array(
                                                                'required' => array('rule'=> array('notEmpty'), 'last'=>true,'message' => '请选择学习语言。')
                                                              ),
                                      'address'=>array(
                                                                'required' => array('rule'=> array('notEmpty'), 'last'=>true,'message' => '请输入地址。')
                                                              ),
                                      'country'=>array(
                                                                'required' => array('rule'=> array('notEmpty'), 'last'=>true,'message' => '请选择国家。')
                                          ),
                                      'secondaryemail'=>array(
                                                                'is_unique'=>array('rule' => array('isUnique'), 'message' => '该电子邮件地址已经在我们的数据库')
                                          ),
                                         'agree' => array(
                                               'rule' => array('comparison', '!=', 0),
                                               'required' => true,
                                               'message' => '你必须同意使用条款',
                                               'on' => 'create'
                                      )

                   );


              if($this->data['Userprofile']['country']=='40'){
                          $svalid = array('state'=>array(
                                                                'required' => array('rule'=> array('notEmpty'), 'last'=>true,'message' => '请选择状态。')
                                                              ),
                                      'city'=>array(
                                                                'required' => array('rule'=> array('notEmpty'), 'last'=>true,'message' => '请选择城市。')
                                                              ),
                                    );
              } elseif($this->data['Userprofile']['country']!='40'){
                         $svalid =     array(
						'otherstate'=>array(
                                                                'required' => array('rule'=> array('notEmpty'), 'last'=>true,'message' => '请输入状态。')
                                                              ),
                                      'othercity'=>array(
                                                                'required' => array('rule'=> array('notEmpty'), 'last'=>true,'message' => '请进入城市。')
                                                              ),
						);

              }

             }
                     
             $this->validate = array_merge($fvalid , $svalid);
               
             break;
             
             default:
                    $this->validate = array();
             break;
    }

 }

}
?>