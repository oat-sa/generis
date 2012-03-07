<?php
/**
 * FlowController class
 * TODO FlowController class documentation.
 * 
 * @author Jérôme Bogaerts <jerome.bogaerts@tudor.lu> <jerome.bogaerts@gmail.com>
 */
class FlowController
{
	public function __construct()
	{

	}
	
	public function forward($moduleName, $actionName)
	{
		$context = Context::getInstance();
		
		$tempModuleName = $context->getModuleName();
		$tempActionName = $context->getActionName();
		
		$context->setModuleName($moduleName);
		$context->setActionName($actionName);
		
		$enforcer = new ActionEnforcer($context);
		$enforcer->execute();
		
		throw new InterruptedActionException('Interrupted action after a forward',
											 $tempModuleName,
											 $tempActionName);
	}
	
	// HTTP 303 : The response to the request can be found under a different URI
	public function redirect($url, $statusCode = 302)
	{
		$context = Context::getInstance();
		
		header(HTTPToolkit::statusCodeHeader($statusCode));
		header(HTTPToolkit::locationHeader($url));
		
		throw new InterruptedActionException('Interrupted action after a redirection', 
											 $context->getModuleName(),
											 $context->getActionName());
	}
}
?>