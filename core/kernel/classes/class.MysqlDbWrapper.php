<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API - core\kernel\classes\class.MysqlDbWrapper.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 11.12.2012, 15:36:44 with ArgoUML PHP module 
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

    /**
     * Short description of method getIndexAlreadyExistsErrorCode
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return string
     */
    public function getIndexAlreadyExistsErrorCode()
    {
        $returnValue = (string) '';

        // section 10-13-1-85-189595a3:13ad0e0e3f2:-8000:0000000000001BBF begin
        $returnValue = '42000';
        // section 10-13-1-85-189595a3:13ad0e0e3f2:-8000:0000000000001BBF end

        return (string) $returnValue;
    }

    /**
     * Short description of method getExtraDSN
     *
     * @access protected
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return string
     */
    protected function getExtraDSN()
    {
        $returnValue = (string) '';

        // section 10-13-1-85-1b0119e:13ad126c698:-8000:0000000000001BD7 begin
        $returnValue = ';charset=utf8';
        // section 10-13-1-85-1b0119e:13ad126c698:-8000:0000000000001BD7 end

        return (string) $returnValue;
    }

    /**
     * Short description of method createIndex
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string indexName
     * @param  string tableName
     * @param  array columns
     * @return void
     */
    public function createIndex($indexName, $tableName, $columns)
    {
        // section 10-13-1-85-69bd0289:13adae4f080:-8000:0000000000001BED begin
        $sql = 'CREATE INDEX "' . $indexName . '" ON "' . $tableName . '" (';
        $colsSql = array();
        foreach ($columns as $n => $l){
        	// $n = name, $l = length
        	if (!empty($l)){
        		$colsSql[] = '"' . $n . '"(' . $l . ')'; 
        	}
        	else{
        		$colsSql[] = '"' . $n . '"';
        	}
        }
        
        $colsSql = implode(',', $colsSql);
        $sql .= $colsSql . ')';
        
        $this->exec($sql);
        // section 10-13-1-85-69bd0289:13adae4f080:-8000:0000000000001BED end
    }

    /**
     * Short description of method rebuildIndexes
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string tableName
     * @return void
     */
    public function rebuildIndexes($tableName)
    {
        // section 10-13-1-85-69bd0289:13adae4f080:-8000:0000000000001BF1 begin
        $this->query('OPTIMIZE TABLE "' . $tableName . '"');
        // section 10-13-1-85-69bd0289:13adae4f080:-8000:0000000000001BF1 end
    }

    /**
     * Short description of method flush
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string tableName
     * @return void
     */
    public function flush($tableName)
    {
        // section 10-13-1-85-69bd0289:13adae4f080:-8000:0000000000001BF4 begin
        $this->query('FLUSH TABLE "' . $tableName . '"');
        // section 10-13-1-85-69bd0289:13adae4f080:-8000:0000000000001BF4 end
    }

    /**
     * Short description of method getColumnNames
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string table
     * @return array
     */
    public function getColumnNames($table)
    {
        $returnValue = array();

        // section 10-13-1-85--7fbbcc7e:13b8a608d82:-8000:0000000000001DCC begin
        $result = $this->query('SHOW COLUMNS FROM "' . $table . '"');
        while ($col = $result->fetchColumn()){
        	$returnValue[] = $col;	
        }
        // section 10-13-1-85--7fbbcc7e:13b8a608d82:-8000:0000000000001DCC end

        return (array) $returnValue;
    }

} /* end of class core_kernel_classes_MysqlDbWrapper */

?>