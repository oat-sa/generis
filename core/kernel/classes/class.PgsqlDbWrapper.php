<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API - core\kernel\classes\class.PgsqlDbWrapper.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 05.11.2012, 15:44:49 with ArgoUML PHP module 
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
// section 10-13-1-85-4bd695b6:13ad101fca1:-8000:0000000000001BC1-includes begin
// section 10-13-1-85-4bd695b6:13ad101fca1:-8000:0000000000001BC1-includes end

/* user defined constants */
// section 10-13-1-85-4bd695b6:13ad101fca1:-8000:0000000000001BC1-constants begin
// section 10-13-1-85-4bd695b6:13ad101fca1:-8000:0000000000001BC1-constants end

/**
 * Short description of class core_kernel_classes_PgsqlDbWrapper
 *
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package core
 * @subpackage kernel_classes
 */
class core_kernel_classes_PgsqlDbWrapper
    extends core_kernel_classes_DbWrapper
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method getExtraConfiguration
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return array
     */
    public function getExtraConfiguration()
    {
        $returnValue = array();

        // section 10-13-1-85-4bd695b6:13ad101fca1:-8000:0000000000001BC3 begin
        return array();
        // section 10-13-1-85-4bd695b6:13ad101fca1:-8000:0000000000001BC3 end

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

        // section 10-13-1-85-4bd695b6:13ad101fca1:-8000:0000000000001BC5 begin
        $returnValue = '42P01';
        // section 10-13-1-85-4bd695b6:13ad101fca1:-8000:0000000000001BC5 end

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

        // section 10-13-1-85-4bd695b6:13ad101fca1:-8000:0000000000001BC7 begin
        $returnValue = '42703';
        // section 10-13-1-85-4bd695b6:13ad101fca1:-8000:0000000000001BC7 end

        return (string) $returnValue;
    }

    /**
     * Short description of method afterConnect
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return void
     */
    public function afterConnect()
    {
        // section 10-13-1-85-4bd695b6:13ad101fca1:-8000:0000000000001BC9 begin
        $this->dbConnector->exec("SET NAMES 'UTF8'");
        // section 10-13-1-85-4bd695b6:13ad101fca1:-8000:0000000000001BC9 end
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

        // section 10-13-1-85-4bd695b6:13ad101fca1:-8000:0000000000001BCE begin
        $catalog = $this->dbConnector->quote(DATABASE_NAME);
        $schema = $this->dbConnector->quote('public');
        $type = $this->dbConnector->quote('BASE TABLE');
        $sql = 'SELECT "table_name" FROM "information_schema"."tables" WHERE '
        	 . '"table_catalog" = ' . $catalog . ' AND table_schema = ' . $schema . ' '
        	 . 'AND "table_type" = ' . $type;
        	 
        $result = $this->query($sql);
        while($table = (string)$result->fetchColumn(0)){
        	$returnValue[] = $table;
        }
        // section 10-13-1-85-4bd695b6:13ad101fca1:-8000:0000000000001BCE end

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

        // section 10-13-1-85-4bd695b6:13ad101fca1:-8000:0000000000001BD0 begin
        $statement .= " LIMIT ${limit} OFFSET ${offset}";
        $returnValue = $statement;
        // section 10-13-1-85-4bd695b6:13ad101fca1:-8000:0000000000001BD0 end

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

        // section 10-13-1-85-4bd695b6:13ad101fca1:-8000:0000000000001BD6 begin
        $returnValue = '42P07';
        // section 10-13-1-85-4bd695b6:13ad101fca1:-8000:0000000000001BD6 end

        return (string) $returnValue;
    }

} /* end of class core_kernel_classes_PgsqlDbWrapper */

?>