<?php

namespace oat\generis\test\integration\common\persistence\sql;

use common_persistence_sql_dbal_Driver;
use common_persistence_sql_pdo_sqlite_Driver;
use oat\generis\test\TestCase;

abstract class UpdateMultipleTestAbstract extends TestCase
{
    /** @var common_persistence_sql_dbal_Driver|common_persistence_sql_pdo_sqlite_Driver */
    protected $driver;

    public function setUp()
    {
        parent::setUp();
        $this->setUpDriver();

        $command = 'CREATE TABLE IF NOT EXISTS test_table (
                    column_1  VARCHAR (255),
                    column_2  VARCHAR (255),
                    column_3  VARCHAR (255),
                    column_4  VARCHAR (255),
                    column_5  VARCHAR (255),
                    column_6  VARCHAR (255)
        )';

        $this->driver->exec($command);

        $sql = 'INSERT INTO test_table(column_1, column_2, column_3, column_4, column_5, column_6)
              VALUES(:value_1, :value_2, :value_3, :value_4, :value_5, :value_6)';
        $this->driver->query($sql, [
            ':value_1' => 'value_1',
            ':value_2' => 'value_2',
            ':value_3' => 'value_3',
            ':value_4' => 'value_4',
            ':value_5' => 'value_5',
            ':value_6' => 'value_6',
        ]);

        $sql = 'INSERT INTO test_table(column_1, column_2, column_3, column_4, column_5, column_6)
              VALUES(:value_1, :value_2, :value_3, :value_4, :value_5, :value_6)';
        $this->driver->query($sql, [
            ':value_1' => 'value_1',
            ':value_2' => 'value_2',
            ':value_3' => 'value_3_2',
            ':value_4' => 'value_4_2',
            ':value_5' => 'value_5_2',
            ':value_6' => 'value_6_2',
        ]);

        $sql = 'INSERT INTO test_table(column_1, column_2, column_3, column_4, column_5, column_6)
              VALUES(:value_1, :value_2, :value_3, :value_4, :value_5, :value_6)';
        $this->driver->query($sql, [
            ':value_1' => 'value_1_3',
            ':value_2' => 'value_2_3',
            ':value_3' => 'value_3_3',
            ':value_4' => 'value_4_3',
            ':value_5' => 'value_5_3',
            ':value_6' => 'value_6_3',
        ]);

        return $this->driver->lastInsertId();
    }

    public function tearDown()
    {
        parent::tearDown();
        $this->setUpDriver();
        $command = 'DROP TABLE test_table ';
        $this->driver->exec($command);
    }

    /**
     * @throws \Exception
     */
    public function testUpdateMultiple()
    {
        $this->setUpDriver();
        $this->driver->updateMultiple('test_table', [
            [
                'conditions' => [
                    'column_1' => 'value_1',
                    'column_2' => 'value_2',
                ],
                'updateValues' => [
                    'column_3' => 'update value 3',
                    'column_4' => 'update value 4',
                ],
            ],
            [
                'conditions' => [
                    'column_5' => 'value_5_2',
                ],
                'updateValues' => [
                    'column_5' => 'update value 5',
                    'column_6' => 'update value 6',
                ],
            ],
        ]);

        $all = $this->driver->query('SELECT * FROM test_table')->fetchAll();
        $this->assertEquals('value_1', $all[0]['column_1']);
        $this->assertEquals('value_2', $all[0]['column_2']);
        $this->assertEquals('update value 3', $all[0]['column_3']);
        $this->assertEquals('update value 4', $all[0]['column_4']);
        $this->assertEquals('value_5', $all[0]['column_5']);
        $this->assertEquals('value_6', $all[0]['column_6']);

        $this->assertEquals('value_1', $all[1]['column_1']);
        $this->assertEquals('value_2', $all[1]['column_2']);
        $this->assertEquals('update value 3', $all[1]['column_3']);
        $this->assertEquals('update value 4', $all[1]['column_4']);
        $this->assertEquals('update value 5', $all[1]['column_5']);
        $this->assertEquals('update value 6', $all[1]['column_6']);

        $this->assertEquals('value_1_3', $all[2]['column_1']);
        $this->assertEquals('value_2_3', $all[2]['column_2']);
        $this->assertEquals('value_3_3', $all[2]['column_3']);
        $this->assertEquals('value_4_3', $all[2]['column_4']);
        $this->assertEquals('value_5_3', $all[2]['column_5']);
        $this->assertEquals('value_6_3', $all[2]['column_6']);
    }

    abstract public function setUpDriver();
}
