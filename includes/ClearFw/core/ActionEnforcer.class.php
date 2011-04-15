<?php
/**
 * ActionEnforcer class
 * TODO ActionEnforcer class documentation.
 * 
 * @author J�r�me Bogaerts <jerome.bogaerts@tudor.lu> <jerome.bogaerts@gmail.com>
 */
class ActionEnforcer implements IExecutable
{
	
	public function __construct()
	{

	}
	
	public function execute()
	{
		$context = Context::getInstance();
		$module = $context->getModuleName();
		$action = $context->getActionName();
		
		if (!$module && !$action) 
		{
			# Configure default module
    		$action = DEFAULT_ACTION_NAME;
    		$module = DEFAULT_MODULE_NAME;
    	}
    	else if (!$module)
    	{
    		throw new ActionEnforcingException("No module specified in request");	
    	}
    	else if (!$action)
    	{
    		$action = DEFAULT_ACTION_NAME;
    		$module = Camelizer::firstToLower($module);
    	}
    	else
    	{
    		# Configure expected module
			$action = Camelizer::firstToLower($action);
			$module = Camelizer::firstToUpper($module);
    	}
    	
    	// if module exist include the class
    	if ($module !== null) {
    		
    		//check if there is a specified context first
			$isSpecificContext = false;
    		if(count($context->getSpecifiers()) > 0){
				foreach($context->getSpecifiers() as $specifier){
				
					$expectedPath = DIR_ACTIONS . $specifier . '/class.' . $module . '.php';
					
					//if we find the view in the specialized context, we load it  
					if (file_exists($expectedPath)){
						require_once ($expectedPath);
						$isSpecificContext = true;
						break;
					}
				}
			}
			
			//if there is none, we look at the global context	
			if(!$isSpecificContext){	
	    		$exptectedPath = DIR_ACTIONS . 'class.'. $module . '.php';
	    		
	    		if (file_exists($exptectedPath)) {
	    			require_once $exptectedPath;
	    		} else {
	    			throw new ActionEnforcingException("Module '" . Camelizer::firstToUpper($module) . "' does not exist in $exptectedPath.",
												   	   $context->getModuleName(),
												       $context->getActionName());
	    		}
			}
			
			if(defined('ROOT_PATH')){
				$root = realpath(ROOT_PATH);
			}
			else{
				$root = realpath($_SERVER['DOCUMENT_ROOT']);
			}
			if(preg_match("/^\//", $root) && !preg_match("/\/$/", $root)){
				$root .= '/';
			}
			else if(!preg_match("/\\$/", $root)){
				$root .= '\\';
			}
			
			$relPath = str_replace($root, '', realpath(dirname($exptectedPath)));
			$relPath = str_replace('/', '_', $relPath);
			$relPath = str_replace('\\', '_', $relPath);
			
			$className = $relPath . '_' . $module;
			if(!class_exists($className)){
				throw new ActionEnforcingException("Unable to load  $className in $exptectedPath",
												   	   $context->getModuleName(),
												       $context->getActionName());
			}
			
    		// File gracefully loaded.
    		$context->setModuleName($module);
    		$context->setActionName($action);
    		
    		$moduleInstance	= new $className();
    		
    	} else {
    		throw new ActionEnforcingException("No Module file matching requested module.",
											   $context->getModuleName(),
											   $context->getActionName());
    	}

    	// if the method related to the specified action exists, call it
    	if (method_exists($moduleInstance, $action)) {
    		// search parameters method
    		$reflect	= new ReflectionMethod($className, $action);
    		$parameters	= $reflect->getParameters();

    		$tabParam 	= array();
    		foreach($parameters as $param)
    			$tabParam[$param->getName()] = $context->getRequest()->getParameter($param->getName());

    		// Action method is invoked, passing request parameters as
    		// method parameters.
    		call_user_func_array(array($moduleInstance, $action), $tabParam);
    		
    		// Render the view if selected.
    		if ($view = $moduleInstance->getView())
    		{
    			$renderer = new Renderer();
    			$renderer->render($view);
    		}
    	} 
    	else {
    		throw new ActionEnforcingException("Unable to find the appropriate action for Module '${module}'.",
											   $context->getModuleName(),
											   $context->getActionName());
    	}
	}
}
?>