<?php
/**
 * Renderer class
 * TODO Renderer class documentation.
 * 
 * @author J�r�me Bogaerts <jerome.bogaerts@tudor.lu> <jerome.bogaerts@gmail.com>
 */
class Renderer
{
	/**
	 * @var string base directory containing the views themes
	 */
	private static $viewsBasePath = '';
	
	public function __construct()
	{
		//by default the path is set by configuration
		if(empty(self::$viewsBasePath)){
			self::$viewsBasePath = DIR_VIEWS;
		}
	}
	
	/**
	 * enable you to change dynamically the views base path
	 * @param string $path
	 * @return void
	 */
	public static function setViewsBasePath($path){
		self::$viewsBasePath = $path;
	} 
	
	/**
	 * conveniance method
	 * @return string the view directory for the current theme
	 */
	protected function getViewPath(){
		return self::$viewsBasePath . 'templates/';
	}
	
	/**
	 * Render a view in the current context
	 * @param string $view the view 
	 */
	public function render($view)
	{
		$context = Context::getInstance();
		
		// We sets variables in order to be available from the current scope.
		// View data variables.
		$viewData = $context->getDataCollection();
		foreach ($viewData as $key => $value)
			$$key = $value;

//		//check if there is a specified context first
//		if(count($context->getSpecifiers()) > 0){
//			foreach($context->getSpecifiers() as $specifier){
//			
//				$expectedPath = $this->getViewPath() . $specifier . '/' . $view;
//
//				//if we find the view in the specialized context, we load it  
//				if (file_exists($expectedPath)){
//					include ($expectedPath);
//					return;
//				}
//			}
//		}
//		//if there is none, we look at the global context
		
		$expectedPath = $this->getViewPath() . $view;
		if (file_exists($expectedPath))
			include ($expectedPath);
		else
			throw new ViewException("No view at location {$expectedPath}.",
									$context->getModuleName(),
									$context->getActionName());
	}
}
?>