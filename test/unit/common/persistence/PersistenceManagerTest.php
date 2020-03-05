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

namespace oat\generis\test\unit\common\persistence;

use oat\generis\test\TestCase;
use oat\generis\persistence\PersistenceManager;
use oat\generis\persistence\sql\SchemaCollection;
use Doctrine\DBAL\Schema\Schema;
use oat\oatbox\log\LoggerService;

class PersistenceManagerTest extends TestCase
{
    /**
     * @var PersistenceManager
     */
    private $pm;

    public function setUp()
    {
        $this->pm = new PersistenceManager([
            PersistenceManager::OPTION_PERSISTENCES => [
                'sql1' => $this->getSqlConfig(),
                'sql2' => $this->getSqlConfig(),
                'notsql' => [
                    'driver' => 'phpfile',
                    'dir' => \tao_helpers_File::createTempDir()
                ]
            ]
        ]);
        $this->pm->setServiceLocator($this->getServiceLocatorMock([
            LoggerService::SERVICE_ID => $this->createMock(LoggerService::class)
        ]));
    }

    public function testGetSchema()
    {
        $sc = $this->pm->getSqlSchemas();
        $this->assertInstanceOf(SchemaCollection::class, $sc);
        $this->assertEquals(['sql1', 'sql2'], array_keys(iterator_to_array($sc)));
        $this->assertInstanceOf(Schema::class, $sc->getSchema('sql1'));
        $this->assertInstanceOf(Schema::class, $sc->getSchema('sql2'));
        return $sc;
    }

    /**
     * @depends testGetSchema
     */
    public function testGetWrongSchema(SchemaCollection $sc)
    {
        $this->expectException(\common_exception_InconsistentData::class);
        $sc->getSchema('notsql');
    }

    /**
     * @depends testGetSchema
     */
    public function testChangeSchema(SchemaCollection $sc)
    {
        $schema = $sc->getSchema('sql1');
        $this->assertFalse($schema->hasTable('sample_table'));
        $this->assertEquals($sc->getOriginalSchema('sql1'), $schema);
        $table = $schema->createTable('sample_table');
        $table->addColumn('sample_column', 'string');
        $this->assertTrue($schema->hasTable('sample_table'));
        $this->assertNotEquals($sc->getOriginalSchema('sql1'), $schema);
        $this->assertNotEquals($sc->getOriginalSchema('sql1'), $sc->getSchema('sql1'));
        $this->assertEquals($sc->getOriginalSchema('sql2'), $sc->getSchema('sql2'));
        return $sc;
    }

    /**
     * @depends testChangeSchema
     */
    public function testApplySchema(SchemaCollection $sc)
    {
        $this->pm->applySchemas($sc);
        $this->assertTrue($this->pm->getSqlSchemas()->getSchema('sql1')->hasTable('sample_table'));
        $this->assertFalse($this->pm->getSqlSchemas()->getSchema('sql2')->hasTable('sample_table'));
    }

    protected function getSqlConfig()
    {
        if (!extension_loaded('pdo_sqlite')) {
            $this->markTestSkipped('sqlite not found, tests skipped.');
        }
        return [
            'driver' => 'dbal',
            'connection' => [
                'url' => 'sqlite:///:memory:'
            ]
        ];
    }
}
