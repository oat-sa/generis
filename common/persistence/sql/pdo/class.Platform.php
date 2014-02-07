<?php
/**
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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 * @author "Lionel Lecaque, <lionel@taotesting.com>"
 * @license GPLv2
 * @package package_name
 * @subpackage 
 *
 */
class common_persistence_sql_pdo_Platform{
    
    private $dbalPlatform;
    
    
    public function __construct($platform){
        $this->dbalPlatform = $platform;

    }
    
      
    /**
     * Appends the correct LIMIT statement depending on the implementation of
     * wrapper. For instance, limiting results in SQL statements are different
     * mySQL and postgres.
     *
     * @abstract
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @param  string statement The statement to limit
     * @param  int limit Limit lower bound.
     * @param  int offset Limit upper bound.
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

    public function schemaToSql($schema){
        common_Logger::d('Legacy mode,  schema to sql use dbal');    
        return $schema->toSql($this->dbalPlatform);
    }
    
    public function getMigrateSchemaSql($fromSchema,$toSchema){
        return $fromSchema->getMigrateToSql($toSchema,$this->dbalPlatform);
    }
    
    public function quoteIdentifier($parameter){
        return "${parameter}";
    }
    
}