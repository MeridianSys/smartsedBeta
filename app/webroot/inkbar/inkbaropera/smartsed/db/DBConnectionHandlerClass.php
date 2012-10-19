<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DBConnectionHandlerClass
 *
 * @author root
 */
session_start();

    include('../../config/db_connect.php');
    include('../../config/config.inc.php');
    include('../../config/error_handler.php');


    $isLast=$_REQUEST['isLast'];

    if($isLast == -1) {
            $_SESSION['text'] .= $_REQUEST['text'];

            $annotatedHTML = str_replace("\\", "", $_SESSION['text']);
            $filename = gen_uuid();

            $file=fopen("/var/www/vhosts/drsmarts.com/smartsed.com/smartsed/app/webroot/repo/".$filename.".html","w") or exit("Unable to open file!");
            fwrite($file, $annotatedHTML);
            fclose($file);
            
            if($_SESSION['isSaveShare']=='false'){
                if($_SESSION['categoryID']!=''){
                    $selectSqlCategory = "SELECT id FROM quiz_category_masters WHERE category_name = '".$_SESSION['categoryID']."' LIMIT 1";
                    $resuleCategory = mysql_query($selectSqlCategory);
                    $categoryArray = mysql_fetch_array($resuleCategory, MYSQL_NUM);
                    $categoryID = $categoryArray[0];

                    $insertSql = "insert into dom_annotations (url,title,domname,user_id,tag,category_categoryID,isPrivate) values ('".$_SESSION['url']."' ,'".$_SESSION['title']."' , '".$filename."', ".$_SESSION['id'].",'".$_SESSION['tag']."',".$categoryID.",".$_SESSION['isPrivate']." )";
                }else{
                    $insertSql = "insert into dom_annotations (url,title,domname,user_id,tag,isPrivate) values ('".$_SESSION['url']."' ,'".$_SESSION['title']."' , '".$filename."', ".$_SESSION['id'].",'".$_SESSION['tag']."',".$_SESSION['isPrivate']." )";
                }
                $result = mysql_query($insertSql);
                $lastEnterId= mysql_insert_id();
                if($_SESSION['stickyNotes']!='') {
                    $insertSqlSticky = " insert into sticky_notes ( notes , dom_id) values ('".serialize($_SESSION['stickyNotes'])."', ".$lastEnterId." )";
                    $result2 = mysql_query($insertSqlSticky);
                }
                $_SESSION['tag'] = '';
                $_SESSION['isPrivate'] = '';
                $_SESSION['categoryID'] = '';
            }else{
                $insertSql = " insert into dom_annotations (url,title,domname,user_id) values ('".$_SESSION['url']."' ,'".$_SESSION['title']."' , '".$filename."', ".$_SESSION['id'].")";
                $result = mysql_query($insertSql);
                $lastEnterId= mysql_insert_id();
                sendMail("http://www.smartsed.com/smartsed/repo/".$filename.".html", $_SESSION['recipients'], $_SESSION['about'], $_SESSION['message'], $_SESSION['title']);

                $_SESSION['about']='';
                $_SESSION['recipients']='';
                $_SESSION['message']='';
                $_SESSION['isSaveShare']='';
            }
            
            $_SESSION['userID']=$uid;
            $_SESSION['text']='';
            $_SESSION['id'] = '';
            $_SESSION['title'] = '';
            $_SESSION['url'] = '';
            $_SESSION['stickyNotes'] = '';
            
            header("content-type: text/javascript");

            
            echo $_GET['jsonp_callback'] . '(' . json_encode('success') . ');';
         
    } elseif($isLast == 0) {
         $_SESSION['id'] = $_REQUEST['uid'];
         $_SESSION['title'] = $_REQUEST['title'];
         $_SESSION['url'] = $_REQUEST['url'];
         
         $_SESSION['stickyNotes'] = $_REQUEST['stickyNotes'];
         
         $_SESSION['isSaveShare']=$_REQUEST['isSaveShare'];
         
        if($_SESSION['isSaveShare']=='true'){
            $_SESSION['about']=$_REQUEST['about'];
            $_SESSION['recipients']=$_REQUEST['recipients'];
            $_SESSION['message']=$_REQUEST['message'];
        }else{
            $_SESSION['tag']=$_REQUEST['tag'];
            $_SESSION['isPrivate']=$_REQUEST['isPrivate'];
            if($_REQUEST['categoryID']!='')
                $_SESSION['categoryID'] = $_REQUEST['categoryID'];
            else
                $_SESSION['categoryID']='';
        }
         header("content-type: text/javascript");
        echo $_GET['jsonp_callback'] . '(' . json_encode('1') . ');';

   }elseif($isLast == 1) {
        $_SESSION['text'] .= $_REQUEST['text'];
        header("content-type: text/javascript");
        echo $_GET['jsonp_callback'] . '(' . json_encode('1') . ');';
   }

   function sendMail($link, $recipients, $about, $message, $title){

        $recivers = explode(',',$recipients);

        $msg = $message.'<br>'.$about.'<br><a target="blank" href="'.$link.'">'.$title.'</a>';
        $subject = "DrSmarts Shared Annotation";

        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=iso-8859-1" . "\r\n";
        $headers .= "From: admin@drsmarts.com" . "\r\n";

        if(!empty($recivers)){
            for($i=0;$i<count($recivers);$i++){
                mail(trim($recivers[$i]),$subject,$msg,$headers);
            }
        }else{
            mail(trim($recipients),$subject,$msg,$headers);
        }
    }


    function gen_uuid() {
        $uuid = array(
            'time_low'  => 0,
            'time_mid'  => 0,
            'time_hi'  => 0,
            'clock_seq_hi' => 0,
            'clock_seq_low' => 0,
            'node'   => array()
        );

        $uuid['time_low'] = mt_rand(0, 0xffff) + (mt_rand(0, 0xffff) << 16);
        $uuid['time_mid'] = mt_rand(0, 0xffff);
        $uuid['time_hi'] = (4 << 12) | (mt_rand(0, 0x1000));
        $uuid['clock_seq_hi'] = (1 << 7) | (mt_rand(0, 128));
        $uuid['clock_seq_low'] = mt_rand(0, 255);

        for ($i = 0; $i < 6; $i++) {
            $uuid['node'][$i] = mt_rand(0, 255);
        }

        $uuid = sprintf('%08x-%04x-%04x-%02x%02x-%02x%02x%02x%02x%02x%02x',
            $uuid['time_low'],
            $uuid['time_mid'],
            $uuid['time_hi'],
            $uuid['clock_seq_hi'],
            $uuid['clock_seq_low'],
            $uuid['node'][0],
            $uuid['node'][1],
            $uuid['node'][2],
            $uuid['node'][3],
            $uuid['node'][4],
            $uuid['node'][5]
        );

        return $uuid;
    }
?>
