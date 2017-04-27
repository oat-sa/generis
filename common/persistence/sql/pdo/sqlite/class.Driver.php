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
 * Copyright (c) 2017 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 */


class common_persistence_sql_pdo_sqlite_Driver extends \common_persistence_sql_pdo_Driver
{
    private $platform = null;
    private $schemamanger = null;

    /**
     * @return array
     */
    public function getExtraConfiguration()
    {
    	$returnValue = array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION);
        return (array) $returnValue;
    }

    /**
     * @return common_persistence_sql_pdo_sqlite_SchemaManager
     */
    public function getSchemaManager()
    {
        if($this->schemamanger == null){
            $this->schemamanger = new \common_persistence_sql_pdo_sqlite_SchemaManager($this);
        }
        return $this->schemamanger;
    }

    /**
     * @return \common_persistence_sql_Platform
     */
    public function getPlatform()
    {
        if ($this->platform == null) {
            $this->platform = new \common_persistence_sql_Platform($this->getDbalConnection());
        }
        return $this->platform;
    }

    public function afterConnect()
    {
        //$this->exec("SET NAMES 'UTF8'");
    }

    /**
     * @return string
     */
    protected function getDSN()
    {
        $params = $this->getParams();
        $driver = str_replace('pdo_', '', $params['driver']);
        $dbName = $params['dbname'];
        return $driver . ':' . $dbName;
    }

}