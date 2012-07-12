<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API - common\configuration\class.Report.php
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

/* user defined includes */
// section -64--88-56-1--548fa03:1387a8a40e2:-8000:0000000000001AA0-includes begin
// section -64--88-56-1--548fa03:1387a8a40e2:-8000:0000000000001AA0-includes end

/* user defined constants */
// section -64--88-56-1--548fa03:1387a8a40e2:-8000:0000000000001AA0-constants begin
// section -64--88-56-1--548fa03:1387a8a40e2:-8000:0000000000001AA0-constants end

/**
 * Short description of class common_configuration_Report
 *
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package common
 * @subpackage configuration
 */
class common_configuration_Report
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute status
     *
     * @access private
     * @var int
     */
    private $status = 0;

    /**
     * Short description of attribute VALID
     *
     * @access public
     * @var int
     */
    const VALID = 0;

    /**
     * Short description of attribute INVALID
     *
     * @access public
     * @var int
     */
    const INVALID = 1;

    /**
     * Short description of attribute UNKNOWN
     *
     * @access public
     * @var int
     */
    const UNKNOWN = 2;

    /**
     * Short description of attribute message
     *
     * @access private
     * @var string
     */
    private $message = '';

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  int status
     * @param  string message
     * @return mixed
     */
    public function __construct($status, $message)
    {
        // section -64--88-56-1--548fa03:1387a8a40e2:-8000:0000000000001ABF begin
        $this->setStatus($status);
        $this->setMessage($message);
        // section -64--88-56-1--548fa03:1387a8a40e2:-8000:0000000000001ABF end
    }

    /**
     * Short description of method getStatus
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return int
     */
    public function getStatus()
    {
        $returnValue = (int) 0;

        // section -64--88-56-1--548fa03:1387a8a40e2:-8000:0000000000001AC3 begin
        $returnValue = $this->status;
        // section -64--88-56-1--548fa03:1387a8a40e2:-8000:0000000000001AC3 end

        return (int) $returnValue;
    }

    /**
     * Short description of method setStatus
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  int status
     * @return void
     */
    public function setStatus($status)
    {
        // section -64--88-56-1--548fa03:1387a8a40e2:-8000:0000000000001AC5 begin
        $this->status = $status;
        // section -64--88-56-1--548fa03:1387a8a40e2:-8000:0000000000001AC5 end
    }

    /**
     * Short description of method getMessage
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return string
     */
    public function getMessage()
    {
        $returnValue = (string) '';

        // section -64--88-56-1--548fa03:1387a8a40e2:-8000:0000000000001AC8 begin
        $returnValue = $this->message;
        // section -64--88-56-1--548fa03:1387a8a40e2:-8000:0000000000001AC8 end

        return (string) $returnValue;
    }

    /**
     * Short description of method setMessage
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string message
     * @return void
     */
    public function setMessage($message)
    {
        // section -64--88-56-1--548fa03:1387a8a40e2:-8000:0000000000001ACA begin
        $this->message = $message;
        // section -64--88-56-1--548fa03:1387a8a40e2:-8000:0000000000001ACA end
    }

} /* end of class common_configuration_Report */

?>