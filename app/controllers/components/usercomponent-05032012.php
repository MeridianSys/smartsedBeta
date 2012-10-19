<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
  class UsercomponentComponent extends Object {

      /*
       * The function use to generate the randam string
       * method name : generatekey()
       * Parameters  : None
       *
       */

      function generatekey() {
        return md5(mt_rand()*mt_rand()*rand(2000,3000)*time());
        }

      
      function imageGenerateKey(){
      	return md5(substr(str_shuffle(str_repeat('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789',5)),0,5));
      }




    }


?>
