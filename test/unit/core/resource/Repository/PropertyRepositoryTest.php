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
 * Copyright (c) 2022 (original work) Open Assessment Technologies SA.
 */

declare(strict_types=1);

namespace oat\generis\test\unit\model\resource\Repository;

use oat\generis\test\TestCase;
use core_kernel_classes_Resource;
use oat\generis\model\GenerisRdf;
use oat\generis\model\OntologyRdf;
use oat\search\base\QueryInterface;
use oat\generis\test\IteratorMockTrait;
use oat\search\base\ResultSetInterface;
use oat\search\base\QueryBuilderInterface;
use oat\search\base\SearchGateWayInterface;
use PHPUnit\Framework\MockObject\MockObject;
use oat\search\helper\SupportedOperatorHelper;
use oat\generis\model\Context\ContextInterface;
use oat\generis\model\resource\Repository\PropertyRepository;
use oat\generis\model\resource\Context\PropertyRepositoryContext;
use oat\generis\model\kernel\persistence\smoothsql\search\ComplexSearchService;

class PropertyRepositoryTest extends TestCase
{
    use IteratorMockTrait;

    /** @var ComplexSearchService|MockObject */
    private $complexSearch;

    /** @var PropertyRepository */
    private $sut;

    protected function setUp(): void
    {
        $this->complexSearch = $this->createMock(ComplexSearchService::class);
        $this->sut = new PropertyRepository($this->complexSearch);
    }

    public function testFindByWithAliases(): void
    {
        $queryBuilder = $this->createMock(QueryBuilderInterface::class);

        $this->complexSearch
            ->expects($this->once())
            ->method('query')
            ->willReturn($queryBuilder);

        $query = $this->createMock(QueryInterface::class);

        $this->complexSearch
            ->expects($this->once())
            ->method('searchType')
            ->with($queryBuilder, OntologyRdf::RDF_PROPERTY, true)
            ->willReturn($query);

        $query
            ->expects($this->at(0))
            ->method('addCriterion')
            ->with(OntologyRdf::RDF_TYPE, SupportedOperatorHelper::EQUAL, OntologyRdf::RDF_PROPERTY)
            ->willReturnSelf();

        $context = $this->createMock(ContextInterface::class);
        $context
            ->expects($this->once())
            ->method('hasParameter')
            ->with(PropertyRepositoryContext::PARAM_ALIASES)
            ->willReturn(true);
        $context
            ->expects($this->once())
            ->method('getParameter')
            ->with(PropertyRepositoryContext::PARAM_ALIASES, [])
            ->willReturn([]);

        $query
            ->expects($this->at(1))
            ->method('addCriterion')
            ->with(GenerisRdf::PROPERTY_ALIAS, SupportedOperatorHelper::IN, [])
            ->willReturnSelf();

        $queryBuilder
            ->expects($this->once())
            ->method('setCriteria')
            ->with($query);

        $gateway = $this->createMock(SearchGateWayInterface::class);

        $this->complexSearch
            ->expects($this->once())
            ->method('getGateway')
            ->willReturn($gateway);

        $resource = $this->createMock(core_kernel_classes_Resource::class);
        $resultSet = $this->createIteratorMock(ResultSetInterface::class, [$resource]);

        $gateway
            ->expects($this->once())
            ->method('search')
            ->with($queryBuilder)
            ->willReturn($resultSet);

        $this->assertEquals([$resource], $this->sut->findBy($context));
    }

    public function testFindByWithoutAliases(): void
    {
        $queryBuilder = $this->createMock(QueryBuilderInterface::class);

        $this->complexSearch
            ->expects($this->once())
            ->method('query')
            ->willReturn($queryBuilder);

        $query = $this->createMock(QueryInterface::class);

        $this->complexSearch
            ->expects($this->once())
            ->method('searchType')
            ->with($queryBuilder, OntologyRdf::RDF_PROPERTY, true)
            ->willReturn($query);

        $query
            ->expects($this->once())
            ->method('addCriterion')
            ->with(OntologyRdf::RDF_TYPE, SupportedOperatorHelper::EQUAL, OntologyRdf::RDF_PROPERTY)
            ->willReturnSelf();

        $context = $this->createMock(ContextInterface::class);
        $context
            ->expects($this->once())
            ->method('hasParameter')
            ->with(PropertyRepositoryContext::PARAM_ALIASES)
            ->willReturn(false);
        $context
            ->expects($this->never())
            ->method('getParameter');

        $queryBuilder
            ->expects($this->once())
            ->method('setCriteria')
            ->with($query);

        $gateway = $this->createMock(SearchGateWayInterface::class);

        $this->complexSearch
            ->expects($this->once())
            ->method('getGateway')
            ->willReturn($gateway);

        $resource = $this->createMock(core_kernel_classes_Resource::class);
        $resultSet = $this->createIteratorMock(ResultSetInterface::class, [$resource]);

        $gateway
            ->expects($this->once())
            ->method('search')
            ->with($queryBuilder)
            ->willReturn($resultSet);

        $this->assertEquals([$resource], $this->sut->findBy($context));
    }
}
