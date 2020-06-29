<?php

namespace oat\generis\test\integration\common\persistence\sql\dbal;

use common_persistence_sql_dbal_Driver;
use oat\generis\test\integration\common\persistence\sql\UpdateMultipleTestAbstract;

class UpdateMultipleTest extends UpdateMultipleTestAbstract
{
    /** @var common_persistence_sql_dbal_Driver */
    protected $driver;

    public function setUpDriver()
    {
        if ($this->driver === null) {
            $driver = new common_persistence_sql_dbal_Driver();
            $driver->connect('test_connection', ['connection' => ['url' => 'sqlite:///:memory:']]);
            $this->driver = $driver;
        }
    }
}
