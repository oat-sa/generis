<?php
class Helpers extends Module
{
	public function loading()
	{
		$this->setView('helper_loading.tpl');
	}
	
	public function core()
	{
		$this->setData('data', 'Some useless data');
		$this->setView('helper_core.tpl');
	}
}
?>