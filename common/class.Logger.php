<?php

error_reporting(E_ALL);

/**
 * Abstraction for the System Logger
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package common
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/* user defined includes */
// section 127-0-1-1--ff2797c:12dc23d98f0:-8000:0000000000003EC7-includes begin
// section 127-0-1-1--ff2797c:12dc23d98f0:-8000:0000000000003EC7-includes end

/* user defined constants */
// section 127-0-1-1--ff2797c:12dc23d98f0:-8000:0000000000003EC7-constants begin
// section 127-0-1-1--ff2797c:12dc23d98f0:-8000:0000000000003EC7-constants end

/**
 * Abstraction for the System Logger
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package common
 */
class common_Logger
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * whenever or not the Logger is enabled
     *
     * @access private
     * @var boolean
     */
    private $enabled = true;

    /**
     * a history of past states, to allow a restoration of the previous state
     *
     * @access public
     * @var array
     */
    public $stateStack = array();

    /**
     * instance of the class Logger, to implement the singleton pattern
     *
     * @access public
     * @var Logger
     */
    public static $instance = null;

    /**
     * The implementation of the Logger
     *
     * @access private
     * @var Appender
     */
    private $implementor = null;

    /**
     * the lowest level of events representing the finest-grained processes
     *
     * @access public
     * @var int
     */
    const TRACE_LEVEL = 0;

    /**
     * the level of events representing fine grained informations for debugging
     *
     * @access public
     * @var int
     */
    const DEBUG_LEVEL = 1;

    /**
     * the level of information events that represent high level system events
     *
     * @access public
     * @var int
     */
    const INFO_LEVEL = 2;

    /**
     * the level of warning events that represent potential problems
     *
     * @access public
     * @var int
     */
    const WARNING_LEVEL = 3;

    /**
     * the level of error events that allow the system to continue
     *
     * @access public
     * @var int
     */
    const ERROR_LEVEL = 4;

    /**
     * the level of very severe error events that prevent the system to continue
     *
     * @access public
     * @var int
     */
    const FATAL_LEVEL = 5;

    // --- OPERATIONS ---

    /**
     * returns the existing Logger instance or instantiates a new one
     *
     * @access private
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return common_Logger
     */
    private static function singleton()
    {
        $returnValue = null;

        // section 127-0-1-1--5509896f:133feddcac3:-8000:0000000000004328 begin
        if (is_null(self::$instance))
        	self::$instance = new self();
        $returnValue = self::$instance;
        // section 127-0-1-1--5509896f:133feddcac3:-8000:0000000000004328 end

        return $returnValue;
    }

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return mixed
     */
    public function __construct()
    {
        // section 127-0-1-1--5509896f:133feddcac3:-8000:0000000000004362 begin
        $this->implementor = LogManager::getInstance();
        // section 127-0-1-1--5509896f:133feddcac3:-8000:0000000000004362 end
    }

    /**
     * Short description of method log
     *
     * @access private
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  int level
     * @param  string message
     * @param  array tags
     * @return mixed
     */
    private function log($level, $message, $tags)
    {
        // section 127-0-1-1--5509896f:133feddcac3:-8000:000000000000432A begin
    	if ($this->enabled) {
    		$stack = debug_backtrace();
    		array_shift($stack);
    		$caller = array_shift($stack);
    		$this->implementor->log(new LogItem('', time(), $message, $level, $caller['file'], $caller['line']));
    	};
        // section 127-0-1-1--5509896f:133feddcac3:-8000:000000000000432A end
    }

    /**
     * enables the logger, should not be used to restore a previous logger state
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return mixed
     */
    public static function enable()
    {
        // section 127-0-1-1--5509896f:133feddcac3:-8000:000000000000432F begin
        self::singleton()->stateStack[] = self::singleton()->enabled;
        self::singleton()->enabled = true;
        // section 127-0-1-1--5509896f:133feddcac3:-8000:000000000000432F end
    }

    /**
     * disables the logger, should not be used to restore a previous logger
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return mixed
     */
    public static function disable()
    {
        // section 127-0-1-1--5509896f:133feddcac3:-8000:0000000000004331 begin
    	self::singleton()->stateStack[] = self::singleton()->enabled;
    	self::singleton()->enabled = false;
        // section 127-0-1-1--5509896f:133feddcac3:-8000:0000000000004331 end
    }

    /**
     * restores the logger after its state was modified by enable() or disable()
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return mixed
     */
    public static function restore()
    {
        // section 127-0-1-1--5509896f:133feddcac3:-8000:0000000000004333 begin
    	if (count(self::singleton()->stateStack) > 0) {
    		self::singleton()->enabled = array_pop(self::singleton()->stateStack);
    	} else {
    		self::e("Tried to restore Log state that was never changed");
    	}
        // section 127-0-1-1--5509896f:133feddcac3:-8000:0000000000004333 end
    }

    /**
     * trace logs finest-grained processes informations
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  string message
     * @param  array tags
     * @return mixed
     */
    public static function t($message, $tags = array())
    {
        // section 127-0-1-1--5509896f:133feddcac3:-8000:0000000000004335 begin
    	self::singleton()->log(self::TRACE_LEVEL, $message, $tags);
        // section 127-0-1-1--5509896f:133feddcac3:-8000:0000000000004335 end
    }

    /**
     * debug logs fine grained informations for debugging
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  string message
     * @param  array tags
     * @return mixed
     */
    public static function d($message, $tags = array())
    {
        // section 127-0-1-1--5509896f:133feddcac3:-8000:0000000000004337 begin
    	self::singleton()->log(self::DEBUG_LEVEL, $message, $tags);
        // section 127-0-1-1--5509896f:133feddcac3:-8000:0000000000004337 end
    }

    /**
     * info logs high level system events
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  string message
     * @param  array tags
     * @return mixed
     */
    public static function i($message, $tags = array())
    {
        // section 127-0-1-1--5509896f:133feddcac3:-8000:000000000000433D begin
    	self::singleton()->log(self::INFO_LEVEL, $message, $tags);
        // section 127-0-1-1--5509896f:133feddcac3:-8000:000000000000433D end
    }

    /**
     * warning logs events that represent potential problems
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  string message
     * @param  array tags
     * @return mixed
     */
    public static function w($message, $tags = array())
    {
        // section 127-0-1-1--5509896f:133feddcac3:-8000:000000000000433F begin
    	self::singleton()->log(self::WARNING_LEVEL, $message, $tags);
        // section 127-0-1-1--5509896f:133feddcac3:-8000:000000000000433F end
    }

    /**
     * error logs events that allow the system to continue
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  string message
     * @param  array tags
     * @return mixed
     */
    public static function e($message, $tags = array())
    {
        // section 127-0-1-1--5509896f:133feddcac3:-8000:0000000000004341 begin
    	self::singleton()->log(self::ERROR_LEVEL, $message, $tags);
        // section 127-0-1-1--5509896f:133feddcac3:-8000:0000000000004341 end
    }

    /**
     * fatal logs very severe error events that prevent the system to continue
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  string message
     * @param  array tags
     * @return mixed
     */
    public static function f($message, $tags = array()
)
    {
        // section 127-0-1-1--5509896f:133feddcac3:-8000:00000000000043A1 begin
    	self::singleton()->log(self::FATAL_LEVEL, $message, $tags);
        // section 127-0-1-1--5509896f:133feddcac3:-8000:00000000000043A1 end
    }

} /* end of class common_Logger */

?>