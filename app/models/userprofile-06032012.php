<?php
class Userprofile extends AppModel
 {
   var $name = 'Userprofile';

   var $belongsTo = array( 'User');

  function beforeValidate() {

    $action = Configure::read('contoller')->params['action'];

    switch($action)  {
         case 'register':
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
              
               
                                      
             $this->validate = array_merge($fvalid , $svalid);
                
             break;
             
             default:
                    $this->validate = array();
             break;
    }

 }

}
?>