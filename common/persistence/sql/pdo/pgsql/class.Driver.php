<?php
/*  
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * 
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */
?>
<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API - core\kernel\classes\class.PgsqlDbWrapper.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 07.02.2013, 11:16:20 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Jerome Bogaerts, <jerome@taotesting.com>
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
 * @author Jerome Bogaerts, <jerome@taotesting.com>
 */


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
 * @author Jerome Bogaerts, <jerome@taotesting.com>
 * @package core
 * @subpackage kernel_classes
 */
class common_persistence_sql_pdo_pgsql_Driver
    extends common_persistence_sql_pdo_Driver
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---
    private $platform = null;
    private $schemamanger = null;
    // --- OPERATIONS ---

    /**
     * Short description of method getExtraConfiguration
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @return array
     */
    public function getExtraConfiguration()
    {
        // section 10-13-1-85-4bd695b6:13ad101fca1:-8000:0000000000001BC3 begin
    	$returnValue = array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION);
        // section 10-13-1-85-4bd695b6:13ad101fca1:-8000:0000000000001BC3 end

        return (array) $returnValue;
    }

    /* (non-PHPdoc)
     * @see common_persistence_sql_pdo_Driver::getSchemaManager()
    */
    public function getSchemaManager(){
        if($this->schemamanger == null){
            $this->schemamanger = new common_persistence_sql_pdo_pgsql_SchemaManager($this);
        }
        
    }

    /* (non-PHPdoc)
     * @see common_persistence_sql_pdo_Driver::getSchemaManager()
    */
    public function getPlatform(){
        if($this->platform == null){
            $this->platform = new common_persistence_sql_pdo_Platform(new \Doctrine\DBAL\Platforms\PostgreSqlPlatform());
        }
        return $this->platform;
    }

    /**
     * Short description of method afterConnect
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @return void
     */
    public function afterConnect()
    {
        // section 10-13-1-85-4bd695b6:13ad101fca1:-8000:0000000000001BC9 begin
        $this->exec("SET NAMES 'UTF8'");
        // section 10-13-1-85-4bd695b6:13ad101fca1:-8000:0000000000001BC9 end
    }







    /**
     * Short description of method getDSN
     *
     * @access protected
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @return string
     */
    protected function getDSN()
    {
        $returnValue = (string) '';

        // section 10-13-1-85-1b0119e:13ad126c698:-8000:0000000000001BD9 begin
        $driver = str_replace('pdo_', '', SGBD_DRIVER);
        $dbName = DATABASE_NAME;
        $dbUrl = DATABASE_URL;
        
        $returnValue = $driver . ':dbname=' . $dbName . ';host=' . $dbUrl;
        // section 10-13-1-85-1b0119e:13ad126c698:-8000:0000000000001BD9 end

        return (string) $returnValue;
    }





} /* end of class core_kernel_classes_PgsqlDbWrapper */

?>