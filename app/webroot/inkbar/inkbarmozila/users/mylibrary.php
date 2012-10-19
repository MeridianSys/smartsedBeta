<?php include('NewHeader.php'); ?>
<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
//    //include('../../config/db_connect.php');
//
//	//Establish connection to database
//	mysql_connect('localhost', 'admin', 'Learn2give!') OR die('Could not connect to MySQL: '.mysql_error());
//	//echo "db user";
//	//Select database
//	mysql_select_db('drsmarts_annotate') OR die('Could not select the database: '.mysql_error());

include('../config/db_connect.php');
include('../config/config.inc.php');
include('../config/error_handler.php');

        
    ?>
    <div>
        <table width="100%">
            <tr>
                <td align="center">
                    <table width="50%" cellpadding="5" border="1">
                        <tr>
                            <th colspan="3">My Library</th>
                        </tr>
                        <tr>
                            <td><b>S. No.</b></td><td><b>URL</b></td><td><b>Title</b></td>
                        </tr>
                        <?php
                            $count = 1;
                            if($_REQUEST['u']!=''){
                                $sql="select * from dom_annotation where  user_id = ".$_REQUEST['u'];
                                $result1=mysql_query($sql) or die(mysql_error());

                                if(mysql_num_rows($result1)>0){
                                    while($show=mysql_fetch_array($result1))
                                    {
                       ?>
                                        <tr>
                                            <td><?php echo $count; ?></td><td><?php echo $show['url']; ?></td><td><a target="_blank" href= "/drsmartstoolbar/users/ShowAnnotaionPage.php?dom=<?php echo $show['id']; ?>"><?php echo $show['title']; ?></a></td>
                                        </tr>

                                            <?php $count++;
                                     }
                                }

                            }
                        ?>

                    </table>
                </td>
            </tr>
        </table>
    </div>
    
<?php include('NewFooter.php'); ?>