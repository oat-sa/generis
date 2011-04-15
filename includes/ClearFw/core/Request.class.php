<?php
/**
 * Request class
 * TODO Request class documentation.
 * 
 * @author J�r�me Bogaerts <jerome.bogaerts@tudor.lu> <jerome.bogaerts@gmail.com>
 */
class Request
{	
	protected $parameters = array();
	protected $method;
	
	public function __construct()
	{
		$this->parameters = array_merge($_GET, $_POST);
		$this->secureParameters();
		
		$this->method = $this->defineMethod();
	}
	
	public function getParameter($name)
	{
		return (isset($this->parameters[$name])) ? $this->parameters[$name] : null;
	}
	
	public function hasParameter($name)
	{
		return isset($this->parameters[$name]);
	}
	
	public function getParameters(){
		return $this->parameters;
	}
	
	public function hasCookie($name)
	{
		return isset($_COOKIE[$name]);
	}
	
	public function getCookie($name)
	{
		return $_COOKIE[$name];
	}
	
	public function getMethod()
	{
		return $this->method;
	}
	
	public function isGet()
	{
		return $this->getMethod() == HTTP_GET;
	}
	
	public function isPost()
	{
		return $this->getMethod() == HTTP_POST;
 	}
 	
 	public function isPut()
 	{
 		return $this->getMethod() == HTTP_PUT;
 	}
 	
 	public function isDelete()
 	{
 		return $this->getMethod() == HTTP_DELETE;
 	}
 	
 	public function isHead()
 	{
 		return $this->getMethod() == HTTP_HEAD;
 	}
 	
 	public function getUserAgent()
 	{
 		return $_SERVER['USER_AGENT'];
 	}
 	
 	public function getQueryString()
 	{
 		return $_SERVER['QUERY_STRING'];
 	}
 	
 	public function getRequestURI()
 	{
 		return $_SERVER['REQUEST_URI'];
 	}
 	
 	private function defineMethod()
 	{	
 		$methodAsString = $_SERVER['REQUEST_METHOD'];
 		
 		switch ($methodAsString)
 		{
 			case 'GET':
 				$method = HTTP_GET;
 				break;
 			
 			case 'POST':
 				$method = HTTP_POST;
 				break;
 			
 			case 'PUT':
 				$method = HTTP_PUT;
 				break;
 			
 			case 'DELETE':
 				$method = HTTP_DELETE;
 				break;
 			
 			case 'HEAD':
 				$method = HTTP_HEAD;
 				break;
 		}
 		
 		return $method;
 	}
 	
 	protected function secureParameters()
 	{
 		$errorManager = Error::getInstance();
 		
 		foreach ($this->parameters as $key => &$param)
 			$param = $errorManager->secure($param, $key);
 	}
}
?>