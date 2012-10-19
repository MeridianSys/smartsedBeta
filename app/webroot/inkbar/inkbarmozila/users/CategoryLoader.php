<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
header("Access-Control-Allow-Origin: *");

include('../config/db_connect.php');
include('../config/config.inc.php');
include('../config/error_handler.php');

$insertSql2 = "SELECT DISTINCT category_name FROM quiz_category_masters WHERE parent_id = 0";
$result2 = mysql_query($insertSql2);?>
<select style="width:145px;"  name="category" id="categoryId">
<option>Select Category</option>
<? while($row=mysql_fetch_array($result2)) { ?>
   <option value=<?=$row['category_name']?>><?=$row['category_name']?></option>
<? } ?>
</select>
