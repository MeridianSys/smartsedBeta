<?php
class Charityuser extends AppModel
 {
   var $name = 'charity_user';

  
   function paginateCount($conditions = null, $recursive = 0, $extra = array()) {
           $parameters = compact('conditions');
           $this->recursive = $recursive;

           $count = $this->find('count', array_merge($parameters, $extra));

           if (isset($extra['group'])) {
               $count = $this->getAffectedRows();
           }

           return $count;
       }
   
 
}
?>