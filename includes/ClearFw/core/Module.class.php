<?php
/**
 * Module class
 * TODO Module class documentation.
 * 
 * @author J�r�me Bogaerts <jerome.bogaerts@tudor.lu> <jerome.bogaerts@gmail.com>
 */
class Module extends Actions implements IFlowControl, IViewable
{
	private $selectedView = null;
	
	/**
	 * @var Renderer
	 */
	private $renderer;
	
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
	
	public function getRenderer() {
		if (!isset($this->renderer)) {
			$this->renderer = new Renderer();
		}
		return $this->renderer;
	}
	
	public function setView($identifier)
	{
		$this->getRenderer()->setTemplate($identifier);
	}
	
	public function getView()
	{
		return $this->selectedView;
	}
	
	public function setData($key, $value)
	{
		$this->getRenderer()->setData($key, $value);
	}
	
	public function hasView() {
		return isset($this->renderer) && $this->renderer->hasTemplate();
	}
	
}
?>