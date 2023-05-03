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
 * Copyright (c) 2017-2020 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 */

declare(strict_types=1);

namespace oat\generis\test\unit\common\persistence;

use common_exception_InconsistentData;
use Doctrine\DBAL\Schema\Schema;
use helpers_File;
use oat\generis\persistence\DriverConfigurationFeeder;
use oat\generis\persistence\PersistenceManager;
use oat\generis\persistence\sql\SchemaCollection;
use oat\generis\persistence\sql\SchemaProviderInterface;
use oat\generis\test\TestCase;
use oat\oatbox\log\LoggerService;
use PHPUnit\Framework\MockObject\MockObject;

class PersistenceManagerTest extends TestCase
{
    /** @var PersistenceManager */
    private $pm;

    /** @var DriverConfigurationFeeder|MockObject */
    private $driverConfigurationFeeder;

    /** @var string */
    private $path;

    public function setUp(): void
    {
        $this->path = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'generis_unittest_' . mt_rand() . DIRECTORY_SEPARATOR;
        $this->driverConfigurationFeeder = $this->createMock(DriverConfigurationFeeder::class);
        $this->pm = new PersistenceManager(
            [
                PersistenceManager::OPTION_PERSISTENCES => [
                    'sql1' => $this->getSqlConfig(),
                    'sql2' => $this->getSqlConfig(),
                    'notsql' => [
                        'driver' => 'phpfile',
                        'dir' => $this->path,
                    ],
                ],
            ]
        );
        $this->pm->setServiceLocator(
            $this->getServiceLocatorMock(
                [
                    LoggerService::SERVICE_ID => $this->createMock(LoggerService::class),
                    DriverConfigurationFeeder::SERVICE_ID => $this->driverConfigurationFeeder,
                ]
            )
        );

        $this->driverConfigurationFeeder
            ->method('feed')
            ->willReturnArgument(0);
    }

    public function tearDown(): void
    {
        // path is only created if persistence was used
        if (file_exists($this->path)) {
            helpers_File::remove($this->path);
        }
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
        $this->expectException(common_exception_InconsistentData::class);
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

    public function testApplySchemaProvider()
    {
        $serviceClass = new class () implements SchemaProviderInterface {
            public function provideSchema(SchemaCollection $schemaCollection)
            {
                $table = $schemaCollection->getSchema('sql1')->createTable('serviceTable');
                $table->addColumn('sample', 'text');
            }
        };
        $this->assertFalse($this->pm->getSqlSchemas()->getSchema('sql1')->hasTable('serviceTable'));
        $this->pm->applySchemaProvider(new $serviceClass());
        $this->assertTrue($this->pm->getSqlSchemas()->getSchema('sql1')->hasTable('serviceTable'));
    }

    protected function getSqlConfig()
    {
        if (!extension_loaded('pdo_sqlite')) {
            $this->markTestSkipped('sqlite not found, tests skipped.');
        }

        return [
            'driver' => 'dbal',
            'connection' => [
                'url' => 'sqlite:///:memory:',
            ],
        ];
    }
}
