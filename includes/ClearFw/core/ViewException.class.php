<?php
/**
 * ViewException class
 * TODO ViewException class documentation.
 * 
 * @author Jérôme Bogaerts <jerome.bogaerts@tudor.lu> <jerome.bogaerts@gmail.com>
 */
class ViewException extends Exception
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