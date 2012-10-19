<?php
class News extends AppModel {
var $name = 'news_master';
 var $validate = array(
                        'title' => array(
                            'required'=>array('rule'=> array('notEmpty'),'message' => 'Enter the title for news')
                         ),
                        'news' => array(
                            'required'=>array('rule'=> array('notEmpty'),'message' => 'Enter the news content')
                         )
       );
}// End Class
?>
