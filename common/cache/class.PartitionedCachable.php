<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API - common\cache\class.PartitionedCachable.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 18.01.2013, 11:34:19 with ArgoUML PHP module 
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
 * Classes that implement this class claims their instances are serializable and
 * be identified by a unique serial string.
 *
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 */
require_once('common/interface.Serializable.php');

/* user defined includes */
// section 10-13-1-85--38a3ebee:13c4cf6d12a:-8000:0000000000001EDF-includes begin
// section 10-13-1-85--38a3ebee:13c4cf6d12a:-8000:0000000000001EDF-includes end

/* user defined constants */
// section 10-13-1-85--38a3ebee:13c4cf6d12a:-8000:0000000000001EDF-constants begin
// section 10-13-1-85--38a3ebee:13c4cf6d12a:-8000:0000000000001EDF-constants end

/**
 * Short description of class common_cache_PartitionedCachable
 *
 * @abstract
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package common
 * @subpackage cache
 */
abstract class common_cache_PartitionedCachable
        implements common_Serializable
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute serial
     *
     * @access protected
     * @var string
     */
    protected $serial = '';

    /**
     * Short description of attribute serializedProperties
     *
     * @access protected
     * @var array
     */
    protected $serializedProperties = array();

    // --- OPERATIONS ---

    /**
     * Obtain a serial for the instance of the class that implements the
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return string
     */
    public function getSerial()
    {
        $returnValue = (string) '';

        // section 10-13-1-85--38a3ebee:13c4cf6d12a:-8000:0000000000001ECB begin
        // section 10-13-1-85--38a3ebee:13c4cf6d12a:-8000:0000000000001ECB end

        return (string) $returnValue;
    }

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return mixed
     */
    public function __construct()
    {
        // section 10-13-1-85--38a3ebee:13c4cf6d12a:-8000:0000000000001EFD begin
        // section 10-13-1-85--38a3ebee:13c4cf6d12a:-8000:0000000000001EFD end
    }

    /**
     * Gives the list of attributes to serialize by reflection.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return array
     */
    public function __sleep()
    {
        $returnValue = array();

        // section 10-13-1-85--38a3ebee:13c4cf6d12a:-8000:0000000000001F05 begin
        // section 10-13-1-85--38a3ebee:13c4cf6d12a:-8000:0000000000001F05 end

        return (array) $returnValue;
    }

    /**
     * Short description of method __wakeup
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return mixed
     */
    public function __wakeup()
    {
        $returnValue = null;

        // section 10-13-1-85--38a3ebee:13c4cf6d12a:-8000:0000000000001F08 begin
        // section 10-13-1-85--38a3ebee:13c4cf6d12a:-8000:0000000000001F08 end

        return $returnValue;
    }

    /**
     * Short description of method _remove
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return mixed
     */
    public function _remove()
    {
        $returnValue = null;

        // section 10-13-1-85--38a3ebee:13c4cf6d12a:-8000:0000000000001F0A begin
        // section 10-13-1-85--38a3ebee:13c4cf6d12a:-8000:0000000000001F0A end

        return $returnValue;
    }

    /**
     * Short description of method getSuccessors
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return array
     */
    public function getSuccessors()
    {
        $returnValue = array();

        // section 10-13-1-85--38a3ebee:13c4cf6d12a:-8000:0000000000001F0C begin
        // section 10-13-1-85--38a3ebee:13c4cf6d12a:-8000:0000000000001F0C end

        return (array) $returnValue;
    }

    /**
     * Short description of method getPredecessors
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string classFilter
     * @return array
     */
    public function getPredecessors($classFilter = null)
    {
        $returnValue = array();

        // section 10-13-1-85--38a3ebee:13c4cf6d12a:-8000:0000000000001F0E begin
        // section 10-13-1-85--38a3ebee:13c4cf6d12a:-8000:0000000000001F0E end

        return (array) $returnValue;
    }

    /**
     * Short description of method buildSerial
     *
     * @abstract
     * @access protected
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return string
     */
    protected abstract function buildSerial();

    /**
     * Short description of method getCache
     *
     * @abstract
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return common_cache_Cache
     */
    public abstract function getCache();

} /* end of abstract class common_cache_PartitionedCachable */

?>