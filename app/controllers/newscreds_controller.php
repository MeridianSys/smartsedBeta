<?php
/*
#################################################################################
#										#
#   Newscreds Controller 								#
#   file name        	: nnwscreds_controller.php					#
#   Developed        	: Meridian Radiology 					#
#   Intialised Variables: name , helpers ,components , uses , layout = (admin)  #
#										#
#										#
#################################################################################
*/
class NewscredsController extends AppController {

	 var $name = 'Newscreds';
	 var $helpers = array('Html', 'Form', 'javascript','Ajax','session','Thickbox','Fck');
	 var $layout = 'admin';
	 var $conf = array();
	 var $uses = array('User','Userprofile','Staticloungevalue');
	 var $components = array('Usercomponent');

function index(){
   $this->layout = 'default';
   $this->checkuserlog();
   $staticLoungeArray = $this->Staticloungevalue->find('all');
   $staticLongeContent=array();
    for($i=0;$i<count($staticLoungeArray);$i++){
    if($this->Session->read('Config.language')=='en-gb'){
     $staticLongeContent[$staticLoungeArray[$i]['Staticloungevalue']['key']]=$staticLoungeArray[$i]['Staticloungevalue']['value'];
    }elseif($this->Session->read('Config.language')=='zh-cn'){
     $staticLongeContent[$staticLoungeArray[$i]['Staticloungevalue']['key']]=$staticLoungeArray[$i]['Staticloungevalue']['value_chn'];
    }
    }
    $this->set('staticLongeContent',$staticLongeContent);
}

function article($searchkey=NULL){
   $this->layout = 'default';
   $this->checkuserlog();
   App::import('Vendor', 'newscred');
   $access_key = '88a963cf54d0e2b8ca08e00f08cafbb3';
   if(!isset($_POST['data']['newscreds']['key'])){
        $searchkey = 'barack obama';
   }else{
        $searchkey = $_POST['data']['newscreds']['key'];
   }
    try {
            $articles 		= NewsCredArticle::search($access_key , $searchkey);
            $related_articles 	= NewsCredArticle::search($access_key, $articles[0]->title);
            $related_topics   	= NewsCredTopic::extract( $access_key, $articles[0]->title,
              array (
                    'topic_classifications' => array('Person','Event'),
                    'topic_subclassifications' => array('Politician')
                    )
            );
            $related_images = $articles[0]->getRelatedImages(array('safe_search' => True));
            $this->set('articles',$articles);
            $this->set('related_articles',$related_articles);
            $this->set('related_topics',$related_topics);
            $this->set('related_images',$related_images);
    }
    catch(Exception $e) {

            die($e->getMessage());
    }
   }

function topic_directory($searchkey=NULL){
   $this->layout = 'default';
   $this->checkuserlog();
   App::import('Vendor', 'newscred');
   $access_key = '88a963cf54d0e2b8ca08e00f08cafbb3';

    try {
        $theme_topics = NewsCredTopic::extract($access_key, '', array('topic_classifications' => array('theme'),
                                                                      'pagesize' => 24));

        $people_topics = NewsCredTopic::extract($access_key, '', array('topic_classifications' => array('person'),
                                                                       'pagesize' => 24));

        $events_topics = NewsCredTopic::extract($access_key, '', array('topic_classifications' => array('event'),
                                                                       'pagesize' => 24));

        $company_topics = NewsCredTopic::extract($access_key, '', array('topic_classifications' => array('company'),
                                                                        'pagesize' => 24));

        $organization_topics = NewsCredTopic::extract($access_key, '', array('topic_classifications' => array('organization'),
                                                                             'pagesize' => 24));
        $this->set('theme_topics',$theme_topics);
        $this->set('people_topics',$people_topics);
        $this->set('events_topics',$events_topics);
        $this->set('company_topics',$company_topics);
        $this->set('organization_topics',$organization_topics);

    } catch (NewsCredException $e) {
        die($e->getMessage());
    }

}

function topic_directory_more($category=NULL){
   $this->layout = 'default';
   $this->checkuserlog();
   App::import('Vendor', 'newscred');
   $access_key = '88a963cf54d0e2b8ca08e00f08cafbb3';
   $category = array($category);
   
    try {
        $topics = NewsCredTopic::extract($access_key, '', array('topic_classifications' => $category,
                                                                'pagesize' => 100));
        $this->set('category',$category[0]);
        $this->set('topics',$topics);
    }
    catch (NewsCredException $e) {
        die($e->getMessage());
    }
}

function topicpage($searchkey=NULL){
   $this->layout = 'default';
   $this->checkuserlog();
   App::import('Vendor', 'newscred');
    $access_key = '88a963cf54d0e2b8ca08e00f08cafbb3';
    $query = isset($searchkey) ? $searchkey : 'Barack Obama';
    try
    {
        $topics  = NewsCredTopic::search($access_key, $query);

            if(empty($topics)) die('No Topic found.');

        $related_topics = $topics[0]->getRelatedTopics(array(
                                                                 'topic_classifications'    => array('Person'),
                                                                 'topic_subclassifications' => array('Lawyer', 'Politician'),
                                                                )
                                                          );
        $related_articles = $topics[0]->getRelatedArticles(array(
                                                                      'media_types' => array('Newspaper', 'Blog')
                                                                    )
                                                              );
        $related_images = $topics[0]->getRelatedImages(array('safe_search' => True));
        $related_videos = $topics[0]->getRelatedVideos(array('pagesize' => 3));
        $related_tweets = $topics[0]->getRelatedTweets(array('pagesize' => 5));

        $this->set('topics',$topics);
        $this->set('related_topics',$related_topics);
        $this->set('related_articles',$related_articles);
        $this->set('related_images',$related_images);
        $this->set('related_videos',$related_videos);
        $this->set('related_tweets',$related_tweets);
    }
    catch(Exception $e)
    {
            die ($e->getMessage());
    }
}

}
?>