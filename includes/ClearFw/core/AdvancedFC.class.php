<?php
/**
 * AdvancedFC class
 * TODO AdvancedFC class documentation.
 * 
 * @author Jérôme Bogaerts <jerome.bogaerts@tudor.lu> <jerome.bogaerts@gmail.com>
 */
class AdvancedFC extends DefaultFC
{
	public function __construct(HttpRequest $request)
	{
		parent::__construct($request);
	}
	
	public function loadModule()
	{
		$resolver = new Resolver();
		$action	= $resolver->getAction();
    	$module	= $resolver->getModule();
    	
    	$context = Context::getInstance();
    	$context->setModuleName($module);
    	$context->setActionName($action);
    	
    	$enforcer = new ActionEnforcer();
    	
    	try
    	{
    		$enforcer->execute();
    	}
    	catch (InterruptedActionException $iE)
    	{
    		// Nothing to do here.
    	}
	}
	
	public static function getView($pView)
	{
		throw new VersionException('FrontController::getView is deprecated since ' .
								   'PHPFramework Evolution 1.');
	}
	
	static function redirection($pRedirection = "", $pSauvegarde = true) {
		throw new VersionException('FrontController:redirection is deprecated since ' .
								   'PHPFramework Evolution 1.');
    }
}
?>