<?php
class Edivuser extends AppModel
 {
   var $name = 'ediv_master';
   
    var $validate = array(
			'ediv_tag' => array(
                            'required'=>array('rule'=> array('notEmpty'),'message' => 'Please enter the Tag for Ediv')
                            ),
               'ediv_point' => array(
                            'required'=>array('rule'=> array('numeric'),'message' => 'Enter the Points in number')
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