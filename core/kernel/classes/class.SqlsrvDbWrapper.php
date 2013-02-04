<?php

error_reporting(E_ALL);

/**
 * Microsoft SQL Server Database Wrapper
 *
 * @author firstname and lastname of author, <author@example.org>
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
 * @author firstname and lastname of author, <author@example.org>
 */
require_once('core/kernel/classes/class.DbWrapper.php');

/* user defined includes */
// section 10-13-2-29--9182fea:13ca61699b4:-8000:0000000000001F7E-includes begin
// section 10-13-2-29--9182fea:13ca61699b4:-8000:0000000000001F7E-includes end

/* user defined constants */
// section 10-13-2-29--9182fea:13ca61699b4:-8000:0000000000001F7E-constants begin
// section 10-13-2-29--9182fea:13ca61699b4:-8000:0000000000001F7E-constants end

/**
 * Microsoft SQL Server Database Wrapper
 *
 * @access public
 * @author firstname and lastname of author, <author@example.org>
 * @package core
 * @subpackage kernel_classes
 */
class core_kernel_classes_SqlsrvDbWrapper
    extends core_kernel_classes_DbWrapper
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method getExtraConfiguration
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return string
     */
    public function getExtraConfiguration()
    {
        $returnValue = (string) '';

        // section 10-13-2-29--9182fea:13ca61699b4:-8000:0000000000001F83 begin
        // section 10-13-2-29--9182fea:13ca61699b4:-8000:0000000000001F83 end

        return (string) $returnValue;
    }

    /**
     * Short description of method getTableNotFoundErrorCode
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return string
     */
    public function getTableNotFoundErrorCode()
    {
        $returnValue = (string) '';

        // section 10-13-2-29--9182fea:13ca61699b4:-8000:0000000000001F89 begin
        // section 10-13-2-29--9182fea:13ca61699b4:-8000:0000000000001F89 end

        return (string) $returnValue;
    }

    /**
     * Short description of method getColumnNotFoundErrorCode
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return string
     */
    public function getColumnNotFoundErrorCode()
    {
        $returnValue = (string) '';

        // section 10-13-2-29--9182fea:13ca61699b4:-8000:0000000000001F8B begin
        // section 10-13-2-29--9182fea:13ca61699b4:-8000:0000000000001F8B end

        return (string) $returnValue;
    }

    /**
     * Short description of method afterConnect
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return void
     */
    public function afterConnect()
    {
        // section 10-13-2-29--9182fea:13ca61699b4:-8000:0000000000001F8D begin
        // section 10-13-2-29--9182fea:13ca61699b4:-8000:0000000000001F8D end
    }

    /**
     * Short description of method getTables
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return array
     */
    public function getTables()
    {
        $returnValue = array();

        // section 10-13-2-29--9182fea:13ca61699b4:-8000:0000000000001F8F begin
        // section 10-13-2-29--9182fea:13ca61699b4:-8000:0000000000001F8F end

        return (array) $returnValue;
    }

    /**
     * Short description of method getIndexAlreadyExistsErrorCode
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return string
     */
    public function getIndexAlreadyExistsErrorCode()
    {
        $returnValue = (string) '';

        // section 10-13-2-29--9182fea:13ca61699b4:-8000:0000000000001F97 begin
        // section 10-13-2-29--9182fea:13ca61699b4:-8000:0000000000001F97 end

        return (string) $returnValue;
    }

    /**
     * Short description of method limitStatement
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  string statement
     * @param  int limit
     * @param  int offset
     * @return string
     */
    public function limitStatement($statement, $limit, $offset = 0)
    {
        $returnValue = (string) '';

        // section 10-13-2-29--9182fea:13ca61699b4:-8000:0000000000001F99 begin
        // section 10-13-2-29--9182fea:13ca61699b4:-8000:0000000000001F99 end

        return (string) $returnValue;
    }

    /**
     * Short description of method getExtraDSN
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return string
     */
    public function getExtraDSN()
    {
        $returnValue = (string) '';

        // section 10-13-2-29--9182fea:13ca61699b4:-8000:0000000000001FA1 begin
        // section 10-13-2-29--9182fea:13ca61699b4:-8000:0000000000001FA1 end

        return (string) $returnValue;
    }

    /**
     * Short description of method createIndex
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  string indexName
     * @param  string tableName
     * @param  array columns
     * @return void
     */
    public function createIndex($indexName, $tableName, $columns)
    {
        // section 10-13-2-29--9182fea:13ca61699b4:-8000:0000000000001FA3 begin
        // section 10-13-2-29--9182fea:13ca61699b4:-8000:0000000000001FA3 end
    }

    /**
     * Short description of method rebuildIndexes
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  string tableName
     * @return void
     */
    public function rebuildIndexes($tableName)
    {
        // section 10-13-2-29--9182fea:13ca61699b4:-8000:0000000000001FA9 begin
        // section 10-13-2-29--9182fea:13ca61699b4:-8000:0000000000001FA9 end
    }

    /**
     * Short description of method flush
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  string tableName
     * @return void
     */
    public function flush($tableName)
    {
        // section 10-13-2-29--9182fea:13ca61699b4:-8000:0000000000001FAB begin
        // section 10-13-2-29--9182fea:13ca61699b4:-8000:0000000000001FAB end
    }

    /**
     * Short description of method getColumnNames
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  string table
     * @return array
     */
    public function getColumnNames($table)
    {
        $returnValue = array();

        // section 10-13-2-29--9182fea:13ca61699b4:-8000:0000000000001FAE begin
        // section 10-13-2-29--9182fea:13ca61699b4:-8000:0000000000001FAE end

        return (array) $returnValue;
    }

} /* end of class core_kernel_classes_SqlsrvDbWrapper */

?>