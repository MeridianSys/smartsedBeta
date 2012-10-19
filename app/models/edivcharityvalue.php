<?php
class Edivcharityvalue extends AppModel
 {
   var $name = 'ediv_charity_value';
   
    var $validate = array(
			   'ediv_charity_amount' => array(
                            'required'=>array('rule'=> array('notEmpty'),'message' => 'Please enter the amount')
                            )
               );
   
   
   
  /*
   function paginateCount($conditions = null, $recursive = 0, $extra = array()) {
           $parameters = compact('conditions');
           $this->recursive = $recursive;

           $count = $this->find('count', array_merge($parameters, $extra));

           if (isset($extra['group'])) {
               $count = $this->getAffectedRows();
           }

           return $count;
       }
   */
 
}
?>