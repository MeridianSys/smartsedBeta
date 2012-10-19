<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
include('../config/db_connect.php');
include('../config/config.inc.php');
include('../config/error_handler.php');

$insertSql2 = "SELECT DISTINCT category_name FROM quiz_category_masters WHERE parent_id = 0";
$result2 = mysql_query($insertSql2);?>

<?php

$str = '<select style="width:145px;" name="category" id="categoryId"><option>Select Category</option>';
while($row=mysql_fetch_array($result2)) {
    $catName = $row['category_name'];

   $str .=  '<option value="'.$catName.'">'.$row['category_name'].'</option>';

}
$str .= '</select>';


header("content-type: text/javascript");
echo $_GET['jsonp_callback'] . '(' . json_encode($str) . ');';


?>





