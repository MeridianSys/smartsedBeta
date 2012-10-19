<?php 
class P28nController extends AppController {
    var $name = 'P28n';
    var $uses = null;
    var $components = array('Usercomponent');

    function change($lang = null) {
        $this->Usercomponent->change($lang);

        $this->redirect($this->referer(null, true));
    }

    function shuntRequest() {
        $this->Usercomponent->change($this->params['lang']);

        $args = func_get_args();
        $this->redirect("/" . implode("/", $args));
    }
}
?> 