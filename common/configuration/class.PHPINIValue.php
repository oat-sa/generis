<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API - common\configuration\class.PHPINIValue.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 12.07.2012, 14:22:10 with ArgoUML PHP module 
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
// section -64--88-56-1--548fa03:1387a8a40e2:-8000:0000000000001ACF-includes begin
// section -64--88-56-1--548fa03:1387a8a40e2:-8000:0000000000001ACF-includes end

/* user defined constants */
// section -64--88-56-1--548fa03:1387a8a40e2:-8000:0000000000001ACF-constants begin
// section -64--88-56-1--548fa03:1387a8a40e2:-8000:0000000000001ACF-constants end

/**
 * Short description of class common_configuration_PHPINIValue
 *
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package common
 * @subpackage configuration
 */
class common_configuration_PHPINIValue
    extends common_configuration_Component
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute expectedValue
     *
     * @access private
     * @var mixed
     */
    private $expectedValue = null;

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  mixed expectedValue
     * @param  string name
     * @param  boolean optional
     * @return mixed
     */
    public function __construct( mixed $expectedValue, $name = 'unknown', $optional = false)
    {
        // section -64--88-56-1--548fa03:1387a8a40e2:-8000:0000000000001B42 begin
        // section -64--88-56-1--548fa03:1387a8a40e2:-8000:0000000000001B42 end
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

        // section -64--88-56-1--548fa03:1387a8a40e2:-8000:0000000000001ADD begin
        // section -64--88-56-1--548fa03:1387a8a40e2:-8000:0000000000001ADD end

        return $returnValue;
    }

} /* end of class common_configuration_PHPINIValue */

?>