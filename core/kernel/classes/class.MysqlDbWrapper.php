<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API - core\kernel\classes\class.MysqlDbWrapper.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 30.10.2012, 16:50:02 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package core
 * @subpackage kernel_classes
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * Simple utility class that allow you to wrap the database connector.
 * You can retrieve an instance evreywhere using the singleton method.
 *
 * This database wrapper uses PDO.
 *
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 */
require_once('core/kernel/classes/class.DbWrapper.php');

/* user defined includes */
// section 10-13-1-85-523dfafc:13ab1680fba:-8000:0000000000001B86-includes begin
// section 10-13-1-85-523dfafc:13ab1680fba:-8000:0000000000001B86-includes end

/* user defined constants */
// section 10-13-1-85-523dfafc:13ab1680fba:-8000:0000000000001B86-constants begin
// section 10-13-1-85-523dfafc:13ab1680fba:-8000:0000000000001B86-constants end

/**
 * Short description of class core_kernel_classes_MysqlDbWrapper
 *
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package core
 * @subpackage kernel_classes
 */
class core_kernel_classes_MysqlDbWrapper
    extends core_kernel_classes_DbWrapper
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method getExtraConfiguration
     *
     * @access protected
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return array
     */
    protected function getExtraConfiguration()
    {
        $returnValue = array();

        // section 10-13-1-85-523dfafc:13ab1680fba:-8000:0000000000001BE8 begin
        return array();
        // section 10-13-1-85-523dfafc:13ab1680fba:-8000:0000000000001BE8 end

        return (array) $returnValue;
    }

    /**
     * Short description of method getTableNotFoundErrorCode
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return string
     */
    public function getTableNotFoundErrorCode()
    {
        $returnValue = (string) '';

        // section 10-13-1-85-523dfafc:13ab1680fba:-8000:0000000000001BEA begin
        $returnValue = '42S02';
        // section 10-13-1-85-523dfafc:13ab1680fba:-8000:0000000000001BEA end

        return (string) $returnValue;
    }

    /**
     * Short description of method getColumnNotFoundErrorCode
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return string
     */
    public function getColumnNotFoundErrorCode()
    {
        $returnValue = (string) '';

        // section 10-13-1-85-523dfafc:13ab1680fba:-8000:0000000000001BEC begin
        $returnValue = '42S22';
        // section 10-13-1-85-523dfafc:13ab1680fba:-8000:0000000000001BEC end

        return (string) $returnValue;
    }

    /**
     * Short description of method afterConnect
     *
     * @access protected
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return void
     */
    protected function afterConnect()
    {
        // section 10-13-1-85-523dfafc:13ab1680fba:-8000:0000000000001BEE begin
        $this->exec('SET SESSION SQL_MODE=\'ANSI_QUOTES\';');
        // section 10-13-1-85-523dfafc:13ab1680fba:-8000:0000000000001BEE end
    }

    /**
     * Short description of method getTables
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return array
     */
    public function getTables()
    {
        $returnValue = array();

        // section 10-13-1-85-523dfafc:13ab1680fba:-8000:0000000000001BF0 begin
    	$result = $this->query('SHOW TABLES');
    	$returnValue = array();
    	while ($row = $result->fetch(PDO::FETCH_NUM)){
    		$returnValue[] = $row[0];
    	}
        // section 10-13-1-85-523dfafc:13ab1680fba:-8000:0000000000001BF0 end

        return (array) $returnValue;
    }

    /**
     * Short description of method limitStatement
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string statement
     * @param  int limit
     * @param  int offset
     * @return string
     */
    public function limitStatement($statement, $limit, $offset = 0)
    {
        $returnValue = (string) '';

        // section 10-13-1-85-523dfafc:13ab1680fba:-8000:0000000000001BF2 begin
        $statement .= " LIMIT ${limit} OFFSET ${offset}";
        $returnValue = $statement;
        // section 10-13-1-85-523dfafc:13ab1680fba:-8000:0000000000001BF2 end

        return (string) $returnValue;
    }

} /* end of class core_kernel_classes_MysqlDbWrapper */

?>