<?php
class MysqlDBConnection extends DBConnection{
	
	public function getExtraConfiguration(){
		return array();
	}
	
	public function afterConnect(){
		$this->exec("SET SESSION SQL_MODE='ANSI_QUOTES'");
	}
}
?>