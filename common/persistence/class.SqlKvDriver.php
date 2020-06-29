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
 * Copyright (c) 2013-2020 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */

use Doctrine\DBAL\ParameterType;
use oat\generis\persistence\sql\SchemaProviderInterface;
use oat\generis\persistence\sql\SchemaCollection;

/**
 * A key value driver based upon an existing sql persistence
 *
 * @todo : Refactor driver specific stuff to dedicated implementation
 */
class common_persistence_SqlKvDriver implements common_persistence_KvDriver, SchemaProviderInterface
{
    const DEFAULT_GC_PROBABILITY = 1000;

    const OPTION_PERSISTENCE_SQL = 'sqlPersistence';

    /**
     * Identifier of the sql persitence used
     * @var string
     */
    private $sqlPersistenceId;

    /**
     * @var common_persistence_SqlPersistence
     */
    private $sqlPersistence;

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
        if (!isset($params[self::OPTION_PERSISTENCE_SQL])) {
            throw new common_exception_Error('Missing underlying sql persistence');
        }

        $this->sqlPersistenceId = $params[self::OPTION_PERSISTENCE_SQL];
        $this->sqlPersistence = common_persistence_SqlPersistence::getPersistence($params[self::OPTION_PERSISTENCE_SQL]);
        $this->garbageCollection = isset($params['gc']) ? $params['gc'] : self::DEFAULT_GC_PROBABILITY;


        return new common_persistence_KeyValuePersistence($params, $this);
    }

    /**
     *
     * @author "Lionel Lecaque, <lionel@taotesting.com>"
     * @param string $id
     * @param string $value
     * @param int|null $ttl
     * @param boolean $nx
     * @throws common_Exception
     * @return boolean
     */
    public function set($id, $value, $ttl = null, $nx = false)
    {
        $returnValue = false;
        if ($nx) {
            throw new common_exception_NotImplemented('NX not implemented in ' . __CLASS__);
        }
        try {
            $expire = is_null($ttl) ? 0 : time() + $ttl;

            // we need int to have safe incr and decr methods
            $encoded = is_int($value) ? $value : base64_encode($value);

            $platformName = $this->sqlPersistence->getPlatForm()->getName();
            $params = [':data' => $encoded, ':time' => $expire, ':id' => $id];

            if ($platformName == 'mysql') {
                //query found in Symfony PdoSessionHandler
                $statement = "INSERT INTO kv_store (kv_id, kv_value, kv_time) VALUES (:id, :data, :time) 
                    ON DUPLICATE KEY UPDATE kv_value = VALUES(kv_value), kv_time = VALUES(kv_time)";
                $returnValue = $this->sqlPersistence->exec($statement, $params);
            } elseif ($platformName == 'oracle') {
                $statement = "MERGE INTO kv_store USING DUAL ON(kv_id = :id) 
                    WHEN NOT MATCHED THEN INSERT (kv_id, kv_value, kv_time) VALUES (:id, :data, sysdate) 
                    WHEN MATHED THEN UPDATE SET kv_value = :data WHERE kv_id = :id";
            } else {
                $statement = 'UPDATE kv_store SET kv_value = :data , kv_time = :time WHERE kv_id = :id';
                $returnValue = $this->sqlPersistence->exec($statement, $params, ['data' => ParameterType::STRING, 'time' => ParameterType::INTEGER, 'id' => ParameterType::STRING]);
                if (0 === $returnValue) {
                    $returnValue = $this->sqlPersistence->insert(
                        'kv_store',
                        ['kv_id' => $id, 'kv_time' => $expire, 'kv_value' => $encoded],
                        ['kv_id' => ParameterType::STRING, 'kv_time' => ParameterType::INTEGER, 'kv_value' => ParameterType::STRING]
                    );
                }
            }

            if ($this->garbageCollection != 0 && rand(0, $this->garbageCollection) == 1) {
                $this->gc();
            }
        } catch (Exception $e) {
            throw new common_Exception("Unable to write the key value storage table in the database "  . $e->getMessage());
        }
        return (bool)$returnValue;
    }

    /**
     *
     * @author "Lionel Lecaque, <lionel@taotesting.com>"
     * @param string $id
     * @throws common_Exception
     * @return string|boolean
     */
    public function get($id)
    {
        try {
            $statement = 'SELECT kv_value, kv_time FROM kv_store WHERE kv_id = ?';
            $statement = $this->sqlPersistence->getPlatForm()->limitStatement($statement, 1);
            $sessionValue = $this->sqlPersistence->query($statement, [$id]);
            while ($row = $sessionValue->fetch()) {
                if ($row["kv_time"] == 0 || $row["kv_time"] >= time()) {
                    return (filter_var($row['kv_value'], FILTER_VALIDATE_INT) !== false)
                        ? (int)$row['kv_value']
                        : base64_decode($row['kv_value']);
                }
            }
        } catch (Exception $e) {
            throw new common_Exception("Unable to read value from key value storage");
        }
        return false;
    }

    /**
     *
     * @author "Lionel Lecaque, <lionel@taotesting.com>"
     * @param string $id
     * @throws common_Exception
     * @return boolean
     */
    public function exists($id)
    {
        try {
            $statement = 'SELECT kv_value FROM kv_store WHERE kv_id = ?';
            $statement = $this->sqlPersistence->getPlatForm()->limitStatement($statement, 1);
            $sessionValue = $this->sqlPersistence->query($statement, [$id]);
            return ($sessionValue->fetch() !== false);
        } catch (Exception $e) {
            throw new common_Exception("Unable to read value from key value storage");
        }
    }

    /**
     *
     * @author "Lionel Lecaque, <lionel@taotesting.com>"
     * @param string $id
     * @throws common_Exception
     * @return boolean
     */
    public function del($id)
    {
        try {
            $statement = 'DELETE FROM kv_store WHERE kv_id = ?';
            $sessionValue = $this->sqlPersistence->exec($statement, [$id]);
            return (bool)$sessionValue;
        } catch (Exception $e) {
            throw new common_Exception("Unable to write the key value table in the database " . $e->getMessage());
        }
        return false;
    }

    /**
     * Increment existing value
     * @param string $id
     * @return int The number of affected rows.
     */
    public function incr($id)
    {
        switch ($this->sqlPersistence->getPlatForm()->getName()) {
            case 'postgresql':
                $statement = 'UPDATE kv_store SET kv_value = kv_value::integer + 1 WHERE kv_id = :id';
                break;
            case 'gcp-spanner':
                $statement = 'UPDATE kv_store SET kv_value = CAST(CAST(kv_value as INT64) + 1 as string) WHERE kv_id = :id';
                break;
            default:
                $statement = 'UPDATE kv_store SET kv_value = kv_value + 1 WHERE kv_id = :id';
        }
        $params = [':id' => $id];
        return $this->sqlPersistence->exec($statement, $params);
    }

    /**
     * Decrement existing value
     * @param $id
     * @return int The number of affected rows.
     */
    public function decr($id)
    {
        switch ($this->sqlPersistence->getPlatForm()->getName()) {
            case 'postgresql':
                $statement = 'UPDATE kv_store SET kv_value = kv_value::integer - 1 WHERE kv_id = :id';
                break;
            case 'gcp-spanner':
                $statement = 'UPDATE kv_store SET kv_value = CAST(CAST(kv_value as INT64) - 1 as string) WHERE kv_id = :id';
                break;
            default:
                $statement = 'UPDATE kv_store SET kv_value = kv_value - 1 WHERE kv_id = :id';
        }
        $params = [':id' => $id];
        return $this->sqlPersistence->exec($statement, $params);
    }

    /**
     * Should be moved to another interface (session handler) than the persistence,
     * this class implementing only the persistence side and another class implementing
     * the handler interface and relying on the persitence.
     */
    protected function gc()
    {
        $statement = 'DELETE FROM kv_store WHERE kv_time > 0 AND kv_time <  ? ';
        return (bool)$this->sqlPersistence->exec($statement, [time()]);
    }

    /**
     * @inheritdoc
     *
     * @throws common_exception_InconsistentData
     */
    public function provideSchema(SchemaCollection $schemaCollection)
    {
        $schema = $schemaCollection->getSchema($this->sqlPersistenceId);
        $table = $schema->createTable("kv_store");
        $table->addColumn('kv_id', "string", ["notnull" => null,"length" => 255]);
        $table->addColumn('kv_value', "text", ["notnull" => null]);
        $table->addColumn('kv_time', "integer", ["notnull" => null,"length" => 30]);
        $table->setPrimaryKey(["kv_id"]);
        $table->addOption('engine', 'MyISAM');
    }
}
