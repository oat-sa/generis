<?php
/**
 * ViewException class
 * TODO ViewException class documentation.
 * 
 * @author J�r�me Bogaerts <jerome.bogaerts@tudor.lu> <jerome.bogaerts@gmail.com>
 */
class ViewException extends common_exception_Error
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