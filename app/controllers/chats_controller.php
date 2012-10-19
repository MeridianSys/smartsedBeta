<?php
class ChatsController extends AppController {

    var $name = 'Chat';
    var $uses = array('Chat','Userprofile','User','Group','UserGroup');
    var $components = array('Session','RequestHandler');
    var $helpers = array('Html', 'Form','Javascript','Ajax');


function chatheartbeat() {
 $this->autoRender=false;
            if($this->RequestHandler->isAjax()){
                Configure::write('debug', 0);

$uid =$this->Session->read('User.id');

$query = $this->Chat->find('all', array(
                                    'conditions' => array('Chat.to' => $uid, 'Chat.recd'=>0),
                                    'order' => 'Chat.id ASC',
                                ));

$items = '';

$chatBoxes = array();

for($i=0; $i<=count($query); $i++) {

$senderNameQuery = $this->User->find('first',
                            array('conditions' => array('User.id' => $query[$i]['Chat']['from']),
                                'fields' => array('Userprofile.first_name', 'Userprofile.last_name','User.id'),
                            ));

if(!empty($senderNameQuery)) {
$senderName = $senderNameQuery['Userprofile']['first_name'].'-'.$senderNameQuery['Userprofile']['last_name'];
$sender_id = $senderNameQuery['User']['id'];
} else {
$senderName='';
}

$chat['from'] = $senderName;
$chat['id'] = $sender_id;
$chat['message'] = $query[$i]['Chat']['message'];
$chat['sent'] = $query[$i]['Chat']['sent'];

if (!isset($_SESSION['openChatBoxes'][$chat['from']]) && !isset($_SESSION['chatHistory'][$chat['from']])) {
      $items = $_SESSION['chatHistory'][$chat['from']];
}

$chat['message'] = $this->sanitize($chat['message']);

$items .= <<<EOD
{
"s": "0",
"f": "{$chat['from']}",
"g": "{$chat['id']}",
"m": "{$chat['message']}"
},
EOD;


if (!isset($_SESSION['chatHistory'][$chat['from']])) {
        $_SESSION['chatHistory'][$chat['from']] = '';
}

$_SESSION['chatHistory'][$chat['from']] .= <<<EOD
						   {
			"s": "0",
			"f": "{$chat['from']}",
                        "g": "{$chat['id']}",
			"m": "{$chat['message']}"
	   },
EOD;

unset($_SESSION['tsChatBoxes'][$chat['from']]);
$_SESSION['openChatBoxes'][$chat['from']] = $chat['sent'];

}


if (!empty($_SESSION['openChatBoxes'])) {
   
foreach ($_SESSION['openChatBoxes'] as $chatbox => $time) {
if (!isset($_SESSION['tsChatBoxes'][$chatbox])) {
    $now = time()-strtotime($time);
    $time = date('g:iA M dS', strtotime($time));

    $message = "Sent at $time";
    if ($now > 180) {
            $items .= <<<EOD
{
"s": "2",
"f": "$chatbox",
"m": "{$message}"
},
EOD;

	if (!isset($_SESSION['chatHistory'][$chatbox])) {
		$_SESSION['chatHistory'][$chatbox] = '';
	}

	$_SESSION['chatHistory'][$chatbox] .= <<<EOD
		{
"s": "2",
"f": "$chatbox",
"m": "{$message}"
},
EOD;
			$_SESSION['tsChatBoxes'][$chatbox] = 1;
		}
		}
	}
}

$updateQry = $this->Chat->updateAll(array('recd' => 1),array('to'=>$this->Session->read('User.id')));



if ($items != '') {
$items = substr($items, 0, -1);
}

header('Content-type: application/json');
?>
{
"sid": "<?php echo $sender_id;?>",
"items": [
<?php echo $items;?>
]
}
<?php
exit(0);
   }

}

function chatBoxSession($chatbox) {

	$items = '';

	if (isset($_SESSION['chatHistory'][$chatbox])) {
		$items = $_SESSION['chatHistory'][$chatbox];
	}

	return $items;
}

function startchatsession() {
    
     $this->autoRender=false;
            if($this->RequestHandler->isAjax()){
                Configure::write('debug', 0);
                $items = '';

                if (!empty($_SESSION['openChatBoxes'])) {
                    
		foreach ($_SESSION['openChatBoxes'] as $chatbox => $void) {
			$items .= $this->chatBoxSession($chatbox);
		}
                }

                if ($items != '') {
                        $items = substr($items, 0, -1);
                }
        
        header('Content-type: application/json');
?>
{
		"username": "me",
                "uid": "<?php echo $this->Session->read('User.id');?>",
		"items": [
			<?php echo $items;?>
        ]
}

<?php 
	exit(0);
    }
}

function getSender(){
    $this->autoRender=false;
            if($this->RequestHandler->isAjax()){
                Configure::write('debug', 0);
                $senderid = $_POST['sid'];
    $query = $this->User->find('first',array('conditions' => array('User.id' => $senderid),
                                'fields' => array('Userprofile.first_name', 'Userprofile.last_name'),
                                ));
               
                $sname = $query['Userprofile']['first_name'].' '.$query['Userprofile']['last_name'];
                header('Content-type: application/json');
?>
{
                "sid": "<?php echo $senderid;?>",
		"sname": "<?php echo $sname;?>"
}
<?php
            exit(0);
            }
}

function sendchat() {
        
$this->autoRender=false;
    if($this->RequestHandler->isAjax()){
        Configure::write('debug', 0);
            $from = $this->Session->read('User.id');
           $to = $_POST['to'];
           $sename = $_POST['sename'];
           $message = $_POST['message'];

           $smilies = array(':-)' => '<img src="/smartsed/css/emotions/happy.jpg" />',
                            ':-D' => '<img src="/smartsed/css/emotions/biggrin.jpg" />',
                            ';-)' => '<img src="/smartsed/css/emotions/wink.jpg" />',
                            '^_^' => '<img src="/smartsed/css/emotions/happyeyes.gif" />',
                            '>:o' => '<img src="/smartsed/css/emotions/laughingeyes.gif" />',
                            ':3' => '<img src="/smartsed/css/emotions/catsmile.jpg" />',
                            '>:-(' => '<img src="/smartsed/css/emotions/grumpy.gif" />',
                            ':-(' => '<img src="/smartsed/css/emotions/sad.gif" />',
                            ':-o' => '<img src="/smartsed/css/emotions/shocked.jpg" />',
                            ':-)' => '<img src="/smartsed/css/emotions/happy.jpg" />',
                            '8-)' => '<img src="/smartsed/css/emotions/glasses.jpg" />',
                            '8-|' => '<img src="/smartsed/css/emotions/coolshades.gif" />',
                            ':-P' => '<img src="/smartsed/css/emotions/tongue.gif" />',
                            'O.o' => '<img src="/smartsed/css/emotions/woot.gif" />',
                            '-_-' => '<img src="/smartsed/css/emotions/dork.jpg" />',
                            ':/' => '<img src="/smartsed/css/emotions/uncertain.jpg" />',
                            '3:' => '<img src="/smartsed/css/emotions/devil.gif" />',
                            'O:)' => '<img src="/smartsed/css/emotions/angel.gif" />',
                            ':-*' => '<img src="/smartsed/css/emotions/kiss.jpg" />',
                            '<3' => '<img src="/smartsed/css/emotions/love.jpg" />',
                            ':v' => '<img src="/smartsed/css/emotions/pacman.jpg" />',
                            ':|]' => '<img src="/smartsed/css/emotions/robot.jpg" />',
                            ':putnam:' => '<img src="/smartsed/css/emotions/guyface.jpg" />'
                            );

           foreach($smilies as $img => $imghtml)
            {
               $message = str_replace($img, $imghtml, $message);
            }
            
            $_SESSION['openChatBoxes'][$_POST['sename']] = date('Y-m-d H:i:s', time());

            $messagesan = $this->sanitize($message);

            if (!isset($_SESSION['chatHistory'][$_POST['sename']])) {
                    $_SESSION['chatHistory'][$_POST['sename']] = '';
            }

$_SESSION['chatHistory'][$_POST['sename']] .= <<<EOD
                                   {
                "s": "1",
                "f": "{$sename}",
                "g": "{$to}",
                "m": "{$messagesan}"
   },
EOD;


unset($_SESSION['tsChatBoxes'][$_POST['sename']]);

//$sql = "insert into chat (chat.from,chat.to,message,sent) values ('".mysql_real_escape_string($from)."', '".mysql_real_escape_string($to)."','".mysql_real_escape_string($message)."',NOW())";
//$query = mysql_query($sql);
$chat['Chat']['from'] = $from;
$chat['Chat']['to'] = $to;
$chat['Chat']['message'] = $message;
$chat['Chat']['sent'] = date('Y-m-d H:i:s');
$chat['Chat']['recd'] = 0;
$this->Chat->create();
$this->Chat->save($chat);

echo "1";
exit(0);
     }
}

function closechat() {

	unset($_SESSION['openChatBoxes'][$_POST['chatbox']]);

	echo "1";
	exit(0);
}

function sanitize($text) {
	$text = htmlspecialchars($text, ENT_QUOTES);
	$text = str_replace("\n\r","\n",$text);
	$text = str_replace("\r\n","\n",$text);
	$text = str_replace("\n","<br>",$text);
	return $text;


}

 /* GET USER LISTING FOR CHAT WINDOW  */

function chatUser() {
    $this->autoRender=true;
    if($this->RequestHandler->isAjax()){
        Configure::write('debug', 0);
        $userID = $this->Session->read('User.id');

        $chatGroupList = $this->Group->find('all',array('fields'=>'id,uid,groupname','conditions'=>'uid='.$userID));

        if(!empty($chatGroupList)) {
               for($j=0;$j<count($chatGroupList);$j++){
                   $gL[] = $this->UserGroup->find('all', array('conditions'=>array('group_id'=>$chatGroupList[$j]['Group']['id']),'fields'=>array('group_id','user_gid')));
               }

        }

        $this->set('chatGroupList',$chatGroupList);
        $this->set('gL',$gL);

        foreach($gL as $k => $v) {
            foreach($v as $u){
                $usr[] = $u['UserGroup']['user_gid'];
            }
           
        }
        $unique = array_map("unserialize", array_unique(array_map("serialize", $usr)));

        $chatUserListNotInGroup = $this->User->find('all', array(
                                        'conditions' => array('User.login_status'=>1, 'User.id !=' => $userID, 'User.id NOT IN ('. implode(',',$unique).')'),
                                        'fields' => array('Userprofile.first_name', 'Userprofile.last_name', 'Userprofile.user_id','User.login_status'),
                                        'order' => 'User.id ASC',
                                    ));
       
        $this->set('otherLooged',$chatUserListNotInGroup);
     
}

}
}