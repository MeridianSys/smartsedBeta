<?php
class News extends AppModel {
var $name = 'news_master';
 var $validate = array(
                        'title' => array(
                            'required'=>array('rule'=> array('notEmpty'),'message' => 'Enter title for english news')
                         ),
                        'title_chn' => array(
                            'required'=>array('rule'=> array('notEmpty'),'message' => 'Enter title for chinese news')
                         ),
                        'news' => array(
                            'required'=>array('rule'=> array('notEmpty'),'message' => 'Enter the news english content')
                         ),
                         'news_chn' => array(
                            'required'=>array('rule'=> array('notEmpty'),'message' => 'Enter the news chinese content')
                         )
       );
}// End Class
?>
