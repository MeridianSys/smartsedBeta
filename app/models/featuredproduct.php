<?php


class Featuredproduct extends AppModel {
var $name = 'featured_product_detail';
var $validate = array(
			     'detail' => array(
                              'required'=>array('rule'=> array('notEmpty'),'message' => 'Enter the Detail of product')
                            ),
                  'menulink' => array(
                              'required'=>array('rule'=> array('notEmpty'),'message' => 'Please provide menu link')
                            ),
                 'pdf_name' => array('rule' => 'file_extention','message' => 'Please upload [ pdf ] extention file ') 
                            
                            
			);
function file_extention($value, $required = false){
				$flag = true;
				if(empty($value['pdf_name']['name'])){
					$flag = true;
				}else{
					if(!empty($value['pdf_name']['name'])){
						
						$ext = substr($value['pdf_name']['name'], strrpos($value['pdf_name']['name'], '.') + 1);
 						if($ext =="pdf"){
							$flag =  true;
 						}else{
 							$flag = false;
 						}
						 
					}
					
				}
	return $flag;	
}
   
}// End Class
?>
