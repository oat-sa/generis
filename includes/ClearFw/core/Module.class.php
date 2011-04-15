<?php
/**
 * Module class
 * TODO Module class documentation.
 * 
 * @author Jérôme Bogaerts <jerome.bogaerts@tudor.lu> <jerome.bogaerts@gmail.com>
 */
class Module extends Actions implements IFlowControl, IViewable
{
	private $selectedView = null;
	
	public function forward($moduleName, $actionName)
	{
		$flowController = new FlowController();
		$flowController->forward($moduleName, $actionName);
	}
	
	public function redirect($url)
	{
		$flowController = new FlowController();
		$flowController->redirect($url);
	}
	
	public function setView($identifier)
	{
		$this->selectedView = $identifier;
	}
	
	public function getView()
	{
		return $this->selectedView;
	}
	
	public function setData($key, $value)
	{
		Context::getInstance()->setData($key, $value);
	}
	
	public function getData($key)
	{
		Context::getInstance()->getData($key);
	}
}
?>