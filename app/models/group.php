<?php
class Group extends AppModel
 {
   var $name = 'Group';

   var $hasMany = array('UserGroup' => 
                                    array('className' => 'UserGroup',

                                     'dependent'=> true
                                         ));
}
?>