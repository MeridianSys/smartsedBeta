<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
  class UsercomponentComponent extends Object {

      var $components = array('Session', 'Cookie');

      function generatekey() {
        return md5(mt_rand()*mt_rand()*rand(2000,3000)*time());
        }

      
      function imageGenerateKey(){
      	return md5(substr(str_shuffle(str_repeat('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789',5)),0,5));
      }


     function startup() {
        if (!$this->Session->check('Config.language')) {
            $this->change(($this->Cookie->read('lang') ? $this->Cookie->read('lang') : DEFAULT_LANGUAGE));
        }
     }

     function change($lang = null) {
        if (!empty($lang)) {
            $this->Session->write('Config.language', $lang);
            $this->Cookie->write('lang', $lang, null, '+350 day');
        }
     }

    }


?>
