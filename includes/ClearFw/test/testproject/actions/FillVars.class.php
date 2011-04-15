<?php
class FillVars extends Module
{
	public function index()
	{
		$this->setData('str_val', 'A string appears');
		$this->setData('int_val', 10);
		$this->setData('array_val', array('banana', 'apple', 'orange'));
		
		$this->setView('fillVarsIndex.tpl');
	}
	
	public function get($a, $b, $c)
	{
		$this->setData('get_val_1', $a);
		$this->setData('get_val_2', $b);
		$this->setData('get_val_3', $c);
		
		$this->setView('fillVarsGet.tpl');
	}
}
?>