<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API -
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 12.07.2012, 14:24:53 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package common
 * @subpackage configuration
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include common_configuration_Component
 *
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 */
require_once('common/configuration/class.Component.php');

/* user defined includes */
// section -64--88-56-1--548fa03:1387a8a40e2:-8000:0000000000001AD3-includes begin
// section -64--88-56-1--548fa03:1387a8a40e2:-8000:0000000000001AD3-includes end

/* user defined constants */
// section -64--88-56-1--548fa03:1387a8a40e2:-8000:0000000000001AD3-constants begin
// section -64--88-56-1--548fa03:1387a8a40e2:-8000:0000000000001AD3-constants end

/**
 * Short description of class common_configuration_FileSystemComponent
 *
 * @abstract
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package common
 * @subpackage configuration
 */
abstract class common_configuration_FileSystemComponent
    extends common_configuration_Component
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute location
     *
     * @access private
     * @var string
     */
    private $location = '';

    /**
     * Short description of attribute expectedRights
     *
     * @access private
     * @var string
     */
    private $expectedRights = '';

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string location
     * @param  string expectedRights
     * @param  string name
     * @param  boolean optional
     * @return mixed
     */
    public function __construct($location, $expectedRights, $name = 'unknown', $optional = false)
    {
        // section -64--88-56-1--548fa03:1387a8a40e2:-8000:0000000000001B11 begin
        parent::__construct($name, $optional);
        $this->setExpectedRights($expectedRights);
        $this->setLocation($location);
        // section -64--88-56-1--548fa03:1387a8a40e2:-8000:0000000000001B11 end
    }

    /**
     * Short description of method getLocation
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return string
     */
    public function getLocation()
    {
        $returnValue = (string) '';

        // section -64--88-56-1--548fa03:1387a8a40e2:-8000:0000000000001B1D begin
        $returnValue = $this->location;
        // section -64--88-56-1--548fa03:1387a8a40e2:-8000:0000000000001B1D end

        return (string) $returnValue;
    }

    /**
     * Short description of method setLocation
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string location
     * @return void
     */
    public function setLocation($location)
    {
        // section -64--88-56-1--548fa03:1387a8a40e2:-8000:0000000000001B1F begin
        $this->location = $location;
        // section -64--88-56-1--548fa03:1387a8a40e2:-8000:0000000000001B1F end
    }

    /**
     * Short description of method exists
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return boolean
     */
    public function exists()
    {
        $returnValue = (bool) false;

        // section -64--88-56-1--548fa03:1387a8a40e2:-8000:0000000000001B22 begin
        $returnValue = @file_exists($this->getLocation());
        // section -64--88-56-1--548fa03:1387a8a40e2:-8000:0000000000001B22 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method getExpectedRights
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return string
     */
    public function getExpectedRights()
    {
        $returnValue = (string) '';

        // section -64--88-56-1--548fa03:1387a8a40e2:-8000:0000000000001B27 begin
        $returnValue = $this->expectedRights;
        // section -64--88-56-1--548fa03:1387a8a40e2:-8000:0000000000001B27 end

        return (string) $returnValue;
    }

    /**
     * Short description of method setExpectedRights
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string expectedRights
     * @return void
     */
    public function setExpectedRights($expectedRights)
    {
        // section -64--88-56-1--548fa03:1387a8a40e2:-8000:0000000000001B29 begin
        $this->expectedRights = $expectedRights;
        // section -64--88-56-1--548fa03:1387a8a40e2:-8000:0000000000001B29 end
    }

    /**
     * Short description of method check
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return common_configuration_Report
     */
    public function check()
    {
        $returnValue = null;

        // section -64--88-56-1--548fa03:1387a8a40e2:-8000:0000000000001B95 begin
        // section -64--88-56-1--548fa03:1387a8a40e2:-8000:0000000000001B95 end

        return $returnValue;
    }

} /* end of abstract class common_configuration_FileSystemComponent */

?>