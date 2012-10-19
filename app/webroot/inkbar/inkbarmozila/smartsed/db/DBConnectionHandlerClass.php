<?php
header("Access-Control-Allow-Origin: *");
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DBConnectionHandlerClass
 *
 * @author root
 */

    include('../../config/db_connect.php');
    include('../../config/config.inc.php');
    include('../../config/error_handler.php');

    $uid=$_REQUEST['uid'];
    $title=$_REQUEST['title'];
    $url=$_REQUEST['url'];
    $text=$_REQUEST['text'];

    
    
    
    $isSaveShare=$_REQUEST['isSaveShare'];

    if($isSaveShare=='true'){
        $about=$_REQUEST['about'];
        $recipients=$_REQUEST['recipients'];
        $message=$_REQUEST['message'];
    }else if($isSaveShare=='false'){
        $tag=$_REQUEST['tag'];
        $isPrivate=$_REQUEST['isPrivate'];
        $categoryName=$_REQUEST['categoryID'];
    }
    
    if($categoryName !=''){
        $selectSqlCategory = "SELECT id FROM quiz_category_masters WHERE category_name = '".$categoryName."' LIMIT 1";
        $resuleCategory = mysql_query($selectSqlCategory);
        $categoryArray = mysql_fetch_array($resuleCategory, MYSQL_NUM);
        $categoryID = $categoryArray[0];
    } else {
        $categoryID = '';
    }
    
    if($uid != '' && $title != '' && $url != '' && $text != ''){
        $annotatedHTML = str_replace("\\", "", $text);
        $filename = gen_uuid();

        $file=fopen("/var/www/vhosts/drsmarts.com/smartsed.com/smartsed/app/webroot/repo/".$filename.".html","w") or exit("Unable to open file!");
        fwrite($file, $annotatedHTML);
        fclose($file);

        if($categoryID!='') {
            $insertSql = " insert into dom_annotations (url,title,domname,user_id,tag,category_categoryID,isPrivate) values ('".$url."' ,'".$title."' , '".$filename."', ".$uid.",'".$tag."',".$categoryID.",".$isPrivate." )";
        } else if($categoryID =='' && !$isSaveShare) {
            $insertSql = " insert into dom_annotations (url,title,domname,user_id,tag,isPrivate) values ('".$url."' ,'".$title."' , '".$filename."', ".$uid.",'".$tag."',".$isPrivate." )";
        }else if($isSaveShare){
            $insertSql = " insert into dom_annotations (url,title,domname,user_id) values ('".$url."' ,'".$title."' , '".$filename."', ".$uid.")";
        }
        $result = mysql_query($insertSql);
        $lastEnterId= mysql_insert_id();
        if($_REQUEST['stickyNotes']!=''){
            $insertSqlSticky = " insert into sticky_notes ( notes , dom_id) values ('".serialize($_REQUEST['stickyNotes'])."', ".$lastEnterId." )";
            $result2 = mysql_query($insertSqlSticky);
            $_SESSION['userID']=$uid;
            
            echo 0;
        }
        if($isSaveShare=='true'){
            sendMail("http://www.smartsed.com/smartsed/repo/".$filename.".html", $recipients, $about, $message, $title);
        }
    }


    function sendMail($link, $recipients, $about, $message, $title){

        $recivers = explode(',',$recipients);

        $msg = $message.'<br>'.$about.'<br><a target="blank" href="'.$link.'">'.$title.'</a>';
        $subject = "Smartsed Shared Annotation";
        
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=iso-8859-1" . "\r\n";
        $headers .= "From: admin@smartsed.com" . "\r\n";
        
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
