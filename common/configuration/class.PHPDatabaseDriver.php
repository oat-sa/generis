<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API -
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 25.07.2012, 16:04:33 with ArgoUML PHP module 
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
 * include common_configuration_PHPExtension
 *
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 */
require_once('common/configuration/class.PHPExtension.php');

/* user defined includes */
// section -64--88-56-1-1221447:138be641ba1:-8000:0000000000001B32-includes begin
// section -64--88-56-1-1221447:138be641ba1:-8000:0000000000001B32-includes end

/* user defined constants */
// section -64--88-56-1-1221447:138be641ba1:-8000:0000000000001B32-constants begin
// section -64--88-56-1-1221447:138be641ba1:-8000:0000000000001B32-constants end

/**
 * Short description of class common_configuration_PHPDatabaseDriver
 *
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package common
 * @subpackage configuration
 */
class common_configuration_PHPDatabaseDriver
    extends common_configuration_PHPExtension
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

        // section -64--88-56-1-26e9c305:138be6ac63b:-8000:0000000000001B34 begin
        $report = parent::check();
        $status = $report->getStatus();
        $name = $this->getName();
        if ($status == common_configuration_Report::INVALID || $status == common_configuration_Report::UNKNOWN){
            $report->setMessage("Database driver '${name}' cannot be found.");
        }
        else{
            $report->setMessage("Databouse driver '${name}' found.");
        }

        $returnValue = $report; 
        // section -64--88-56-1-26e9c305:138be6ac63b:-8000:0000000000001B34 end

        return $returnValue;
    }

} /* end of class common_configuration_PHPDatabaseDriver */

?>