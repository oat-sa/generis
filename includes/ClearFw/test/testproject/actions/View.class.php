<?php
class View extends Module
{
	public function select()
	{
		$this->setView('view.tpl');
	}
	
	public function badSelect()
	{
		$this->setView('unknown.tpl');
	}
}
?>