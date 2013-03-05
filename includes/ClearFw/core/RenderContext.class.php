<?php
/**
 * Renderer class
 * 
 * @author J�r�me Bogaerts <jerome.bogaerts@tudor.lu> <jerome.bogaerts@gmail.com>
 */
class RenderContext
{

	private static $stack = array();
	
	public static function pushContext($variables) {
		$context = new self($variables);
		array_unshift(self::$stack, $context);
	}
	
	public static function popContext() {
		if (empty(self::$stack)) {
			throw new common_exception_Error('Called '.__FUNCTION__.' on an empty stack');
		}
		array_shift(self::$stack);
	}
	
	/**
	 * 
	 * @throws common_exception_Error
	 * @return RenderContext
	 */
	public static function getCurrentContext() {
		if (empty(self::$stack)) {
			throw new common_exception_Error('Called '.__FUNCTION__.' on an empty stack');
		}
		return reset(self::$stack);
	}
	
	/**
	 * @var array associtaiv array of variables that will be replaced in the template
	 */
	private $variables = array();
	
	/**
	 * Creates a new context
	 * 
	 * @param array $variables
	 */
	private function __construct($variables)
	{
		$this->variables	= $variables;
	}

    /**
     * Gets data for the specified key
     * 
     * @param string $key
     * @return mixed associated data
     */
	public function getData($key)
    {
        return isset($this->variables[$key]) ? $this->variables[$key] : null;
    }
	
    /**
     * Returns whenever or not a variable with the specified key is defined
     * 
     * @param string $key
     * @return boolean
     */
	public function hasData($key)
    {
        return isset($this->variables[$key]);
    }
}
?>