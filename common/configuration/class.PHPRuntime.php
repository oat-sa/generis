<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API - common\configuration\class.PHPRuntime.php
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
 * include common_configuration_BoundableComponent
 *
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 */
require_once('common/configuration/class.BoundableComponent.php');

/* user defined includes */
// section -64--88-56-1--548fa03:1387a8a40e2:-8000:0000000000001ACD-includes begin
// section -64--88-56-1--548fa03:1387a8a40e2:-8000:0000000000001ACD-includes end

/* user defined constants */
// section -64--88-56-1--548fa03:1387a8a40e2:-8000:0000000000001ACD-constants begin
// section -64--88-56-1--548fa03:1387a8a40e2:-8000:0000000000001ACD-constants end

/**
 * Short description of class common_configuration_PHPRuntime
 *
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package common
 * @subpackage configuration
 */
class common_configuration_PHPRuntime
    extends common_configuration_BoundableComponent
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

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

        // section -64--88-56-1--548fa03:1387a8a40e2:-8000:0000000000001AD9 begin
        $validity = null;
        $message = null;
        $min = $this->getMin();
        $max = $this->getMax();
        $current = phpversion();
        
        if (!empty($min) && !empty($max)){
            // min & max are specifed.
            if (version_compare($current, $min, '>=') == 0 && version_compare($current, $max, '<=') == 0){
                $validity = common_configuration_Report::VALID;
                $message = "PHP Version (${current}) is between ${min} and ${max}.";
            }
            else {
                $validity = common_configuration_Report::INVALID;
                $message = "PHP Version (${current} is not between ${min} and ${max}.)";
            }
        }
        else if (!empty($min) && empty($max)){
            if (version_compare($current, $min, '>=') == 0){
                $validity = common_configuration_Report::VALID;
                $message = "PHP Version (${current}) is higher or equal to ${min}.";
            }
            else{
                $validity = common_configuration_Report::INVALID;
                $message = "PHP Version (${current}) is lower than ${min}.";
            } 
        }
        else if (empty($min) && !empty($max)){
            if (version_compare($current, $max, '<=') == 0){
                $validity = common_configuration_Report::VALID;
                $message = "PHP Version (${current}) is lesser than ${max}.";
            }
            else{
                $validity = common_configuration_Report::INVALID;
                $message = "PHP Version (${current}) is greater than ${max}.";
            }
        }
        
        $returnValue = new common_configuration_Report($validity, $message);
        // section -64--88-56-1--548fa03:1387a8a40e2:-8000:0000000000001AD9 end

        return $returnValue;
    }

} /* end of class common_configuration_PHPRuntime */

?>