<?php
/**
 * ActionEnforcingException class
 * TODO ActionEnforcingException class documentation.
 * 
 * @author Jérôme Bogaerts <jerome.bogaerts@tudor.lu> <jerome.bogaerts@gmail.com>
 */
class ActionEnforcingException extends Exception
{
	private $moduleName;
	private $actionName;
	
	public function __construct($message, $moduleName, $actionName)
	{
		parent::__construct($message);
		
		$this->moduleName = $moduleName;
		$this->actionName = $actionName;
	}
	
	public function getModuleName()
	{
		return $this->moduleName;
	}
	
	public function getActionName()
	{
		return $this->actionName;
	}
}
?>