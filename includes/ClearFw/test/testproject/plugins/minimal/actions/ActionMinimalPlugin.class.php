<?php

class ActionMinimalPlugin extends Module {

    static function index() {
    	echo '<br/>Plugin Controller ...ok';
    	echo '<br/>Plugin Model ...' . ModelMinimalPlugin::getStatus();;

    }
}
?>