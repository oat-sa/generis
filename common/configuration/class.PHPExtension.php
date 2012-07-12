<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API - common\configuration\class.PHPExtension.php
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
// section -64--88-56-1--548fa03:1387a8a40e2:-8000:0000000000001AD1-includes begin
// section -64--88-56-1--548fa03:1387a8a40e2:-8000:0000000000001AD1-includes end

/* user defined constants */
// section -64--88-56-1--548fa03:1387a8a40e2:-8000:0000000000001AD1-constants begin
// section -64--88-56-1--548fa03:1387a8a40e2:-8000:0000000000001AD1-constants end

/**
 * Short description of class common_configuration_PHPExtension
 *
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package common
 * @subpackage configuration
 */
class common_configuration_PHPExtension
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

        // section -64--88-56-1--548fa03:1387a8a40e2:-8000:0000000000001ADB begin
        $name = $this->getName();
        $min = $this->getMin();
        $max = $this->getMax();
        $validity = null;
        $message = null;
        
        if (extension_loaded($name)){
            $current = phpversion($name);
            
            if (!empty($min) && !empty($max)){
                // Both min and max are specified.
                if (version_compare($current, $min, '>=') == 0 && version_compare($current, $max, '<=')){
                    $validity = common_configuration_Report::VALID;
                    $message = "PHP Extension '${name}' version (${current}) is between ${min} and ${max}.";
                }
                else{
                    $validity = common_configuration_Report::INVALID;
                    $message = "PHP Extension '${name}' version (${current}) is not between ${min} and ${max}.";
                }
            }
            else if (!empty($min) && empty($max)){
                // Only min is specified.
                if (version_compare($current, $min, '>=') == 0){
                    $validity = common_configuration_Report::VALID;
                    $message = "PHP Extension '${name}' version (${current}) is greater or equal to ${min}.";
                }
                else{
                    $validity = common_configuration_Report::INVALID;
                    $message = "PHP Extension '${name}' version (${current}) is lesser than ${min}.";
                }
            }
            else if (empty($min) && !empty($max)){
                // Only max is specified.
                if (version_compare($current, $max, '<=') == 0){
                    $validity = common_configuration_Report::VALID;
                    $message = "PHP Extension '${name}' version (${current}) is lesser or equal to ${max}.";
                }
                else{
                    $validity = common_configuration_Report::INVALID;
                    $message = "PHP Extension '${name}' version (${current}) is greater than ${max}.";
                }
            }
        }
        else{
            $validity = common_configuration_Report::UNKNOWN;
            $message = "PHP Extension '${name}' could not be found.";
        }

        $returnValue = new common_configuration_Report($validity, $message);
        // section -64--88-56-1--548fa03:1387a8a40e2:-8000:0000000000001ADB end

        return $returnValue;
    }

} /* end of class common_configuration_PHPExtension */

?>