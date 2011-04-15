<?php
/**
 * InterruptedActionException class
 * TODO InterruptedActionException class documentation.
 * 
 * @author Jérôme Bogaerts <jerome.bogaerts@tudor.lu> <jerome.bogaerts@gmail.com>
 */
class InterruptedActionException extends Exception
{
	private $actionName;
	private $moduleName;
	
	public function __construct($message, $moduleName, $actionName)
	{
		parent::__construct($message);
		
		$this->moduleName = $moduleName;
		$this->actionName = $actionName;
	}
	
	public function getActionName()
	{
		return $this->actionName;
	}
	
	public function getModuleName()
	{
		return $this->moduleName;
	}
}
?>