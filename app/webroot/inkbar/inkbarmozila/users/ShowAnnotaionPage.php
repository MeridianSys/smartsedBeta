<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
include('../config/db_connect.php');
include('../config/config.inc.php');
include('../config/error_handler.php');


if(isset($_REQUEST['dom']) && !empty ($_REQUEST['dom'])){
        $sql="select * from dom_annotation where  id = ".$_REQUEST['dom'];
        $result2=mysql_query($sql) or die(mysql_error());
        $showDom=mysql_fetch_array($result2);
        echo ($showDom['domname']!='')?$showDom['domname']:'No Record';
}




?>