<?php
class SqlSrvDBConnection extends DBConnection{

    protected function getExtraConfiguration(){
        return array();
    }

    protected function afterConnect(){
        $this->exec("SET NAMES 'UTF8'");
    }

    protected function getExtraDSN(){
        return '';
    }
}
?>