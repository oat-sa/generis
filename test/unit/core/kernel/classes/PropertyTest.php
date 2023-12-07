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
 * Copyright (c) 2023 (original work) Open Assessment Technologies SA;
 */

declare(strict_types=1);

namespace oat\generis\test\unit\core\kernel\classes;

use core_kernel_classes_Property;
use core_kernel_persistence_PropertyInterface;
use core_kernel_persistence_starsql_StarModel;
use oat\generis\model\data\RdfsInterface;
use oat\generis\model\GenerisRdf;
use oat\generis\model\OntologyRdfs;
use oat\generis\test\GenerisTestCase;
use oat\oatbox\cache\SimpleCache;
use PHPUnit\Framework\MockObject\MockObject;

class PropertyTest extends GenerisTestCase
{
    /** @var core_kernel_classes_Property */
    private $property;

    /** @var core_kernel_persistence_starsql_StarModel|MockObject */
    private $model;

    /** @var core_kernel_persistence_PropertyInterface|MockObject */
    private $persistenceProperty;

    /** @var SimpleCache|MockObject */
    private $cache;

    public function setUp(): void
    {
        $this->model = $this->createMock(core_kernel_persistence_starsql_StarModel::class);
        $this->rdfs = $this->createMock(RdfsInterface::class);
        $this->persistenceProperty = $this->createMock(core_kernel_persistence_PropertyInterface::class);
        $this->cache = $this->createMock(SimpleCache::class);

        $this->model
            ->method('getRdfsInterface')
            ->willReturn($this->rdfs);
        $this->rdfs
            ->method('getPropertyImplementation')
            ->willReturn($this->persistenceProperty);

        $this->model
            ->method('getCache')
            ->willReturn($this->cache);

        $this->property = $this->createPartialMock(
            core_kernel_classes_Property::class,
            ['getRange', 'getProperty', 'getOnePropertyValue']
        );
        $this->property->__construct('uri');
        $this->property->setModel($this->model);
    }

    public function testIsRelationshipFalseWithoutCache(): void
    {
        $this->property
            ->expects(self::once())
            ->method('getRange')
            ->willReturn(new core_kernel_classes_Property(OntologyRdfs::RDFS_LITERAL));

        $this->cache
            ->expects(self::once())
            ->method('has')
            ->with('PropIsRelationship_uri')
            ->willReturn(false);
        $this->cache
            ->expects(self::never())
            ->method('get');
        $this->cache
            ->expects(self::once())
            ->method('set')
            ->with('PropIsRelationship_uri', false);

        $result = $this->property->isRelationship();

        $this->assertFalse($result);
    }

    public function testIsRelationshipTrueWithoutCache(): void
    {
        $this->property
            ->expects(self::once())
            ->method('getRange')
            ->willReturn(new core_kernel_classes_Property(OntologyRdfs::RDFS_LABEL));

        $this->cache
            ->expects(self::once())
            ->method('has')
            ->with('PropIsRelationship_uri')
            ->willReturn(false);
        $this->cache
            ->expects(self::never())
            ->method('get');
        $this->cache
            ->expects(self::once())
            ->method('set')
            ->with('PropIsRelationship_uri', true);

        $result = $this->property->isRelationship();

        $this->assertTrue($result);
    }

    public function testIsRelationshipTrueWithCache(): void
    {
        $this->property
            ->expects(self::never())
            ->method('getRange');

        $this->cache
            ->expects(self::once())
            ->method('has')
            ->with('PropIsRelationship_uri')
            ->willReturn(true);
        $this->cache
            ->expects(self::once())
            ->method('get')
            ->with('PropIsRelationship_uri')
            ->willReturn(true);
        $this->cache
            ->expects(self::never())
            ->method('set');

        $result = $this->property->isRelationship();

        $this->assertTrue($result);
    }

    public function testIsLgDependentFalseWithoutCache(): void
    {
        $this->persistenceProperty
            ->expects(self::once())
            ->method('isLgDependent')
            ->willReturn(false);

        $this->cache
            ->expects(self::once())
            ->method('has')
            ->with('PropIsLgDependent_uri')
            ->willReturn(false);
        $this->cache
            ->expects(self::never())
            ->method('get');
        $this->cache
            ->expects(self::once())
            ->method('set')
            ->with('PropIsLgDependent_uri', false);

        $result = $this->property->isLgDependent();

        $this->assertFalse($result);
    }

    public function testIsLgDependentTrueWithCache(): void
    {
        $this->persistenceProperty
            ->expects(self::never())
            ->method('isLgDependent');

        $this->cache
            ->expects(self::once())
            ->method('has')
            ->with('PropIsLgDependent_uri')
            ->willReturn(true);
        $this->cache
            ->expects(self::once())
            ->method('get')
            ->with('PropIsLgDependent_uri')
            ->willReturn(true);
        $this->cache
            ->expects(self::never())
            ->method('set');

        $result = $this->property->isLgDependent();

        $this->assertTrue($result);
    }

    public function testSetLgDependent(): void
    {
        $this->cache
            ->expects(self::once())
            ->method('set')
            ->with('PropIsLgDependent_uri', true);

        $this->property->setLgDependent(true);
    }

    public function testIsMultipleTrueWithoutCache(): void
    {
        $this->property
            ->expects(self::once())
            ->method('getProperty')
            ->willReturn(new core_kernel_classes_Property('test'));
        $this->property
            ->expects(self::once())
            ->method('getOnePropertyValue')
            ->willReturn(new core_kernel_classes_Property(GenerisRdf::GENERIS_TRUE));

        $this->cache
            ->expects(self::once())
            ->method('has')
            ->with('PropIsMultiple_uri')
            ->willReturn(false);
        $this->cache
            ->expects(self::never())
            ->method('get');
        $this->cache
            ->expects(self::once())
            ->method('set')
            ->with('PropIsMultiple_uri', true);

        $result = $this->property->isMultiple();

        $this->assertTrue($result);
    }

    public function testIsMultipleFalseWithCache(): void
    {
        $this->property
            ->expects(self::never())
            ->method('getProperty');
        $this->property
            ->expects(self::never())
            ->method('getOnePropertyValue');

        $this->cache
            ->expects(self::once())
            ->method('has')
            ->with('PropIsMultiple_uri')
            ->willReturn(true);
        $this->cache
            ->expects(self::once())
            ->method('get')
            ->with('PropIsMultiple_uri')
            ->willReturn(false);
        $this->cache
            ->expects(self::never())
            ->method('set');

        $result = $this->property->isMultiple();

        $this->assertFalse($result);
    }

    public function testSetMultiple(): void
    {
        $this->cache
            ->expects(self::once())
            ->method('set')
            ->with('PropIsMultiple_uri', true);

        $this->property->setMultiple(true);
    }

    public function testOnDeleteWithCacheClear(): void
    {
        $this->cache
            ->expects(self::exactly(3))
            ->method('has')
            ->withConsecutive(['PropIsRelationship_uri'], ['PropIsMultiple_uri'], ['PropIsLgDependent_uri'])
            ->willReturnOnConsecutiveCalls(true, true, true);
        $this->cache
            ->expects(self::exactly(3))
            ->method('delete')
            ->withConsecutive(['PropIsRelationship_uri'], ['PropIsMultiple_uri'], ['PropIsLgDependent_uri']);

        $this->property->delete();
    }

    public function testOnDeleteWithoutCacheClear(): void
    {
        $this->cache
            ->expects(self::exactly(3))
            ->method('has')
            ->withConsecutive(['PropIsRelationship_uri'], ['PropIsMultiple_uri'], ['PropIsLgDependent_uri'])
            ->willReturnOnConsecutiveCalls(false, false, false);
        $this->cache
            ->expects(self::never())
            ->method('delete');

        $this->property->delete();
    }
}
