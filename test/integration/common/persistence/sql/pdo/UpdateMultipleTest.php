<?php

namespace oat\generis\test\integration\common\persistence\sql\pdo;

use common_persistence_sql_pdo_sqlite_Driver;
use oat\generis\test\integration\common\persistence\sql\UpdateMultipleTestAbstract;

class UpdateMultipleTest extends UpdateMultipleTestAbstract
{
    /** @var common_persistence_sql_pdo_sqlite_Driver */
    protected $driver;

    public function setUpDriver()
    {
        if ($this->driver === null) {
            $driver = new \common_persistence_sql_pdo_sqlite_Driver();
            $driver->connect('test_connection', [
                'driver' => 'pdo_sqlite',
                'user' => null,
                'password' => null,
                'host' => null,
                'dbname' => ':memory:',
            ]);
            $this->driver = $driver;
        }
    }
}
