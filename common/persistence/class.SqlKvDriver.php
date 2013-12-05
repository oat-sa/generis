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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */

/**
 * A key value driver based upon an existing sql persistence
 * 
 */
class common_persistence_SqlKvDriver implements common_persistence_KvDriver
{

    const DEFAULT_GC_PROBABILITY = 1000;
    
    /**
     * @var common_persistence_SqlPersistence
     */
    private $sqlPeristence;
    
    /**
     * Probability of garbage collection to be triggered
     * stores the inverse element
     * 
     * @var int
     */
    private $garbageCollection;
    
    /**
     * (non-PHPdoc)
     * @see common_persistence_Driver::connect()
     */
    function connect($id, array $params)
    {
        if (!isset($params['sqlPersistence'])) {
            throw new common_exception_Error('Missing underlying sql persistence');
        }
        
        $this->sqlPeristence = common_persistence_SqlPersistence::getPersistence($params['sqlPersistence']);
        $this->garbageCollection = isset($params['gc']) ? $params['gc'] : self::DEFAULT_GC_PROBABILITY;
        
        $statement =" CREATE TABLE if not exists sessions(
                        session_id varchar(255) NOT NULL,
                        session_value text NOT NULL,
                        session_time int(11) NOT NULL,
                        PRIMARY KEY (session_id)
                    ) ENGINE=MyIsam DEFAULT CHARSET=utf8;";
        $this->sqlPeristence->exec($statement);
        
        return new common_persistence_KeyValuePersistence($params, $this);
    }
    
    public function set($id, $value, $ttl = null) {
        $returnValue = false;
        try{
            $expire = is_null($ttl) ? 'NULL' : time()+$ttl;
            $statement = 'REPLACE INTO "sessions" ("session_id", "session_value", "session_time") VALUES(\''.$id.'\', \''.$value.'\', '.$expire.')';
            $returnValue = $this->sqlPeristence->exec($statement);
            if ($this->garbageCollection != 0 && rand(0, $this->garbageCollection) == 1) {
                $this->gc();
            } 
        }
        catch (PDOException $e){
            throw new common_Exception("Unable to write the session storage table in the database");
        }
        return (boolean)$returnValue;
    }
    
    public function get($id) {
        try{
            $statement = 'SELECT "session_value" FROM "sessions" WHERE "session_id" = \''.$id.'\' LIMIT 1';
            $sessionValue = $this->sqlPeristence->query($statement);
            while ($row = $sessionValue->fetch()) {
                return $row["session_value"];
            }
        }
        catch (PDOException $e){
            throw new common_Exception("Unable to read session value");
        }
        return false;
    }
    
    public function exists($id) {
        try{
            $statement = 'SELECT "session_value" FROM "sessions" WHERE "session_id" = \''.$id.'\' LIMIT 1';
            $sessionValue = $this->sqlPeristence->query($statement);
            return ($sessionValue->fetch() !== false);
        }
        catch (PDOException $e){
            throw new common_Exception("Unable to read session value");
        }
    }
    
    public function del($id) {
        try{
            $statement = 'DELETE FROM "sessions" WHERE "session_id" = \''.$id.'\'';
            $sessionValue = $this->sqlPeristence->exec($statement);
            return (boolean)$sessionValue;
        }
        catch (PDOException $e){
            throw new common_Exception("Unable to write the session storage table in the database");
        }
        return false;
    }
    
    protected function gc()
    {
        common_Logger::d('SQL key/value storage garbage collection triggered');
        $statement = 'DELETE FROM sessions WHERE session_time < '.time();
        return (bool)$this->sqlPeristence->exec($statement);
    }

}
