<?php
/*
#################################################################################
#																				#
#   User Controller 															#
#   file name        	: users_controller.php									#
#   Developed        	: Meridian Radiology 									#
#   Intialised Variables: name , helpers ,components , uses , layout = (default)#
#																				#
#																				#
#################################################################################
*/


class InkbarsController extends AppController {

	var $name = 'Inkbars';
        var $uses = array('User','Userprofile', 'dom_annotation');
        var $components = array('Usercomponent', 'Session','Image','Email','RequestHandler');
        var $helpers = array('Html', 'Form','Javascript','Ajax');


        function downloads($uid=NULL) {
            $sql= "select users.id, users.email from users where md5(users.id) = '".$uid."' ";
            $uii = $this->User->query($sql);
            $this->set('ui',$uii[0]['users']['id']);
        }

        function mylibrary($uid=NULL) {
//            $this->checkuserlog();
            $sql="select d.id,d.url,d.title,d.domname,d.datetime,u.email from dom_annotations as d LEFT JOIN users as u ON d.user_id=u.id where d.user_id = ".$uid;
            $result=$this->dom_annotation->query($sql);
            $this->set('result1',$result);
            
        }
        function show_annotaion_page($dom=NULL) {
            $this->layout=null;
             $sql="select * from dom_annotations where id = ".$dom;
                $result2=$this->dom_annotation->query($sql);
                $this->set('result', $result2);
                 
        }



// End Of the function for users_controller Class
}

?>
