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
class common_persistence_sql_dbal_Platform{
    
    private $dbalPlatform;
    
    
    public function __construct($dbalPlatform){
        $this->dbalPlatform = $dbalPlatform;
    }
    
    
    
    /**
     * Appends the correct LIMIT statement depending on the implementation of
     * wrapper. For instance, limiting results in SQL statements are different
     * mySQL and postgres.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @param  string statement The statement to limit
     * @param  int limit Limit lower bound.
     * @param  int offset Limit upper bound.
     * @return string
     */
    public function limitStatement($statement, $limit, $offset = 0){
        return $this->dbalPlatform->modifyLimitQuery($statement, $limit, $offset);
    }
    
    
    
    public function quoteIdentifier($parameter){
        return $this->dbalPlatform->quoteIdentifier($parameter);
    }
    
    public function schemaToSql($schema){
        return $schema->toSql($this->dbalPlatform);
    }
    
    public function getMigrateSchemaSql($fromSchema,$toSchema){
        return $fromSchema->getMigrateToSql($toSchema,$this->dbalPlatform);     
    }
    
    
}