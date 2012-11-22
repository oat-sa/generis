<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API -
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 19.07.2012, 16:31:45 with ArgoUML PHP module 
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
// section -64--88-56-1--548fa03:1387a8a40e2:-8000:0000000000001AE5-includes begin
// section -64--88-56-1--548fa03:1387a8a40e2:-8000:0000000000001AE5-includes end

/* user defined constants */
// section -64--88-56-1--548fa03:1387a8a40e2:-8000:0000000000001AE5-constants begin
// section -64--88-56-1--548fa03:1387a8a40e2:-8000:0000000000001AE5-constants end

/**
 * Short description of class common_configuration_BoundableComponent
 *
 * @abstract
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package common
 * @subpackage configuration
 */
abstract class common_configuration_BoundableComponent
    extends common_configuration_Component
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute min
     *
     * @access private
     * @var string
     */
    private $min = '';

    /**
     * Short description of attribute max
     *
     * @access private
     * @var string
     */
    private $max = '';

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string min
     * @param  string max
     * @param  string name
     * @param  boolean optional
     * @return mixed
     */
    public function __construct($min, $max, $name = 'unknown', $optional = false)
    {
        // section -64--88-56-1--548fa03:1387a8a40e2:-8000:0000000000001B55 begin
        parent::__construct($name, $optional);
        $this->setMin($min);
        $this->setMax($max);
        // section -64--88-56-1--548fa03:1387a8a40e2:-8000:0000000000001B55 end
    }

    /**
     * Short description of method setMin
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string min
     * @return void
     */
    public function setMin($min)
    {
        // section -64--88-56-1--548fa03:1387a8a40e2:-8000:0000000000001B04 begin
        $this->min = $min;
        // section -64--88-56-1--548fa03:1387a8a40e2:-8000:0000000000001B04 end
    }

    /**
     * Short description of method getMin
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return string
     */
    public function getMin()
    {
        $returnValue = (string) '';

        // section -64--88-56-1--548fa03:1387a8a40e2:-8000:0000000000001B07 begin
        $returnValue = $this->min;
        // section -64--88-56-1--548fa03:1387a8a40e2:-8000:0000000000001B07 end

        return (string) $returnValue;
    }

    /**
     * Short description of method setMax
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string max
     * @return void
     */
    public function setMax($max)
    {
        // section -64--88-56-1--548fa03:1387a8a40e2:-8000:0000000000001B09 begin
    	// Support .x notation.
        $this->max = preg_replace('/x/u', '99999', $max);
        // section -64--88-56-1--548fa03:1387a8a40e2:-8000:0000000000001B09 end
    }

    /**
     * Short description of method getMax
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return string
     */
    public function getMax()
    {
        $returnValue = (string) '';

        // section -64--88-56-1--548fa03:1387a8a40e2:-8000:0000000000001B0C begin
        $returnValue = $this->max;
        // section -64--88-56-1--548fa03:1387a8a40e2:-8000:0000000000001B0C end

        return (string) $returnValue;
    }

    /**
     * Short description of method getValue
     *
     * @abstract
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return string
     */
    public abstract function getValue();

} /* end of abstract class common_configuration_BoundableComponent */

?>