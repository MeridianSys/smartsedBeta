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
             if($this->data['Userprofile']['title']=='Dr.'){
                          $title = array('professional_track'=>array(
                                                                'required' => array('rule'=> array('notEmpty'), 'last'=>true,'message' => 'Please select professional track.')
                                                              ),
                                       'institute'=>array(
                                                                'required' => array('rule'=> array('notEmpty'), 'last'=>true,'message' => 'Please select School/Hospital')
                                                              ),
										'passing_year'=>array(
                                                                'required' => array('rule'=> array('notEmpty'), 'last'=>true,'message' => 'Please select Graduation year')
                                                              )
                                    );
              }else{
              	
              			  $title=array();
              }
              
             } else {
                 $fvalid = array( 'first_name'=>array(
                                                    'required' => array('rule'=> array('notEmpty'), 'last'=>true,'message' => 'è¯·è¾“å…¥å��å­—ã€‚')

                                                    ),
                                      'last_name'=>array(
                                                    'required' => array('rule'=> array('notEmpty'), 'last'=>true,'message' => 'è¯·è¾“å…¥å§“æ°�ã€‚')

                                                     ),
                                      'nativelanguage'=>array(
                                                                'required' => array('rule'=> array('notEmpty'), 'last'=>true,'message' => 'è¯·é€‰æ‹©æ¯�è¯­ã€‚')
                                                              ),
                                      'fluentlanguage'=>array(
                                                                'required' => array('rule'=> array('notEmpty'), 'last'=>true,'message' => 'è¯·é€‰æ‹©è¯­è¨€æµ�ç•…ã€‚')
                                                              ),
                                      'learnlanguage'=>array(
                                                                'required' => array('rule'=> array('notEmpty'), 'last'=>true,'message' => 'è¯·é€‰æ‹©å­¦ä¹ è¯­è¨€ã€‚')
                                                              ),
                                      'address'=>array(
                                                                'required' => array('rule'=> array('notEmpty'), 'last'=>true,'message' => 'è¯·è¾“å…¥åœ°å�€ã€‚')
                                                              ),
                                      'country'=>array(
                                                                'required' => array('rule'=> array('notEmpty'), 'last'=>true,'message' => 'è¯·é€‰æ‹©å›½å®¶ã€‚')
                                          ),
                                      'secondaryemail'=>array(
                                                                'is_unique'=>array('rule' => array('isUnique'), 'message' => 'è¯¥ç”µå­�é‚®ä»¶åœ°å�€å·²ç»�åœ¨æˆ‘ä»¬çš„æ•°æ�®åº“')
                                          ),
                                         'agree' => array(
                                               'rule' => array('comparison', '!=', 0),
                                               'required' => true,
                                               'message' => 'ä½ å¿…é¡»å�Œæ„�ä½¿ç”¨æ�¡æ¬¾',
                                               'on' => 'create'
                                      )

                   );


              if($this->data['Userprofile']['country']=='40'){
                          $svalid = array('state'=>array(
                                                                'required' => array('rule'=> array('notEmpty'), 'last'=>true,'message' => 'è¯·é€‰æ‹©çŠ¶æ€�ã€‚')
                                                              ),
                                      'city'=>array(
                                                                'required' => array('rule'=> array('notEmpty'), 'last'=>true,'message' => 'è¯·é€‰æ‹©åŸŽå¸‚ã€‚')
                                                              ),
                                    );
              } elseif($this->data['Userprofile']['country']!='40'){
                         $svalid =     array(
						'otherstate'=>array(
                                                                'required' => array('rule'=> array('notEmpty'), 'last'=>true,'message' => 'è¯·è¾“å…¥çŠ¶æ€�ã€‚')
                                                              ),
                                      'othercity'=>array(
                                                                'required' => array('rule'=> array('notEmpty'), 'last'=>true,'message' => 'è¯·è¿›å…¥åŸŽå¸‚ã€‚')
                                                              ),
						);

              }
             if($this->data['Userprofile']['title']=='Dr.'){
                          $title = array('professional_track'=>array(
                                                                'required' => array('rule'=> array('notEmpty'), 'last'=>true,'message' => 'Please select professional track.')
                                                              ),
                                       'institute'=>array(
                                                                'required' => array('rule'=> array('notEmpty'), 'last'=>true,'message' => 'Please select School/Hospital')
                                                              ),
										'passing_year'=>array(
                                                                'required' => array('rule'=> array('notEmpty'), 'last'=>true,'message' => 'Please select Graduation year')
                                                              )	                                                              
                                    );
              }else{
              	
              			  $title=array();
              }

             }
                     
             $this->validate = array_merge($fvalid , $svalid,$title);
               
             break;
             
             default:
                    $this->validate = array();
             break;
    }

 }

}
?>