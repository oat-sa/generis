<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API - common\cache\class.SessionCache.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 18.01.2013, 15:31:57 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package common
 * @subpackage cache
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * basic interface a cache implementation has to implement
 *
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 */
require_once('common/cache/interface.Cache.php');

/* user defined includes */
// section 10-13-1-85--38a3ebee:13c4cf6d12a:-8000:0000000000001EE3-includes begin
// section 10-13-1-85--38a3ebee:13c4cf6d12a:-8000:0000000000001EE3-includes end

/* user defined constants */
// section 10-13-1-85--38a3ebee:13c4cf6d12a:-8000:0000000000001EE3-constants begin
// section 10-13-1-85--38a3ebee:13c4cf6d12a:-8000:0000000000001EE3-constants end

/**
 * Short description of class common_cache_SessionCache
 *
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package common
 * @subpackage cache
 */
class common_cache_SessionCache
        implements common_cache_Cache
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute items
     *
     * @access public
     * @var array
     */
    public $items = array();

    /**
     * Short description of attribute SESSION_KEY
     *
     * @access public
     * @var string
     */
    const SESSION_KEY = 'cache';

    /**
     * Short description of attribute instance
     *
     * @access private
     * @var SessionCache
     */
    private static $instance = null;

    // --- OPERATIONS ---

    /**
     * puts "something" into the cache,
     *      * If this is an object and implements Serializable,
     *      * we use the serial provided by the object
     *      * else a serial must be provided
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  mixed
     * @param  string serial
     * @return mixed
     */
    public function put($mixed, $serial = null)
    {
        // section 10-13-1-85--38a3ebee:13c4cf6d12a:-8000:0000000000001F34 begin
        if ($mixed instanceof common_Serializable) {
        	if (!is_null($serial) && $serial != $mixed->getSerial()) {
        		throw new common_exception_Error('Serial mismatch for Serializable '.$mixed->getSerial());
        	}
        	$serial = $mixed->getSerial();
        }
        $this->items[$serial] = $mixed;
        // section 10-13-1-85--38a3ebee:13c4cf6d12a:-8000:0000000000001F34 end
    }

    /**
     * gets the entry associted to the serial
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string serial
     * @return common_Serializable
     */
    public function get($serial)
    {
        $returnValue = null;

        // section 10-13-1-85--38a3ebee:13c4cf6d12a:-8000:0000000000001F3C begin
        if (!isset($this->items[$serial])) {
        	if ($this->has($serial)) {
        		$storage = Session::getAttribute(static::SESSION_KEY);
	        	$data = @unserialize($storage[$serial]);
		        
	        	// check if serialize successfull, see http://lu.php.net/manual/en/function.unserialize.php
	        	if ($data === false && $storage[$serial] !== serialize(false)){
	        		throw new common_exception_Error("Unable to unserialize session entry identified by \"".$serial.'"');
	        	}
	        	$this->items[$serial] = $data;
	        } else {
        		throw new common_cache_NotFoundException('Failed to get ('.$serial.')');
        	}
        }
        $returnValue = $this->items[$serial];
        // section 10-13-1-85--38a3ebee:13c4cf6d12a:-8000:0000000000001F3C end

        return $returnValue;
    }

    /**
     * test whenever an entry associted to the serial exists
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string serial
     * @return boolean
     */
    public function has($serial)
    {
        $returnValue = (bool) false;

        // section 10-13-1-85--38a3ebee:13c4cf6d12a:-8000:0000000000001F40 begin
    	if (isset($this->items[$serial])) {
			$returnValue = true;
		} else {
			if (Session::hasAttribute(static::SESSION_KEY)) {
				$storage = Session::getAttribute(static::SESSION_KEY);
				$returnValue = isset($storage[$serial]);
			}
		}
        // section 10-13-1-85--38a3ebee:13c4cf6d12a:-8000:0000000000001F40 end

        return (bool) $returnValue;
    }

    /**
     * removes an entry from the cache
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string serial
     * @return mixed
     */
    public function remove($serial)
    {
        // section 10-13-1-85--38a3ebee:13c4cf6d12a:-8000:0000000000001F44 begin
    	if (isset($this->items[$serial])) {
	        unset($this->items[$serial]);
	        unset($_SESSION[SESSION_NAMESPACE][static::SESSION_KEY][$serial]);
        }
        // section 10-13-1-85--38a3ebee:13c4cf6d12a:-8000:0000000000001F44 end
    }

    /**
     * empties the cache
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return mixed
     */
    public function purge()
    {
        $returnValue = null;

        // section 10-13-1-85--38a3ebee:13c4cf6d12a:-8000:0000000000001F48 begin
        Session::removeAttribute(static::SESSION_KEY);
        $this->items = array();
        // section 10-13-1-85--38a3ebee:13c4cf6d12a:-8000:0000000000001F48 end

        return $returnValue;
    }

    /**
     * Short description of method __destruct
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return mixed
     */
    public function __destruct()
    {
        // section 10-13-1-85--38a3ebee:13c4cf6d12a:-8000:0000000000001F2C begin
    	foreach ($this->items as $key => $value) {
			// not clean put reading the session and then adding data to the session causses concurrency problems
			// therefore this DOES NOT WORK: session::setAttribute(static::SESSION_KEY, $storage)
        	$_SESSION[SESSION_NAMESPACE][static::SESSION_KEY][$key] = serialize($value);
    	}
        // section 10-13-1-85--38a3ebee:13c4cf6d12a:-8000:0000000000001F2C end
    }

    /**
     * Short description of method getAll
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return array
     */
    public function getAll()
    {
        $returnValue = array();

        // section 10-13-1-85--38a3ebee:13c4cf6d12a:-8000:0000000000001F2E begin
        if (Session::hasAttribute(static::SESSION_KEY)) {
    		foreach (Session::getAttribute(static::SESSION_KEY) as $serial => $raw) {
    			if (!isset($this->items[$serial])) {
    				// loads the serial to the item
    				$this->get($serial);
    			}
	        }
    	}
    	$returnValue = $this->items;
        // section 10-13-1-85--38a3ebee:13c4cf6d12a:-8000:0000000000001F2E end

        return (array) $returnValue;
    }

    /**
     * Short description of method contains
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string serial
     * @return boolean
     */
    public function contains($serial)
    {
        $returnValue = (bool) false;

        // section 10-13-1-85--38a3ebee:13c4cf6d12a:-8000:0000000000001F30 begin
    	if (isset($this->items[$serial])) {
        	$returnValue = true;
        } elseif (!empty($serial) && Session::hasAttribute(static::SESSION_KEY)){
        	$storage = Session::getAttribute(static::SESSION_KEY);
        	$returnValue = isset($storage[$serial]);
        }
        // section 10-13-1-85--38a3ebee:13c4cf6d12a:-8000:0000000000001F30 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method singleton
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return common_cache_SessionCache
     */
    public static function singleton()
    {
        $returnValue = null;

        // section 10-13-1-85-73eaea5c:13c4ddaf54d:-8000:0000000000001F32 begin
        if (!isset(self::$instance)){
        	self::$instance = new self();
        }
        
        return self::$instance;
        // section 10-13-1-85-73eaea5c:13c4ddaf54d:-8000:0000000000001F32 end

        return $returnValue;
    }

} /* end of class common_cache_SessionCache */

?>