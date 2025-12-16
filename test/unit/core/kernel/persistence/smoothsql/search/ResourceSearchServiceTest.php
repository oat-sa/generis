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
 * Copyright (c) 2021 (original work) Open Assessment Technologies SA;
 */

declare(strict_types=1);

namespace oat\generis\test\unit\core\kernel\persistence\smoothsql\search;

use oat\generis\model\kernel\persistence\smoothsql\search\ComplexSearchService;
use oat\generis\model\kernel\persistence\smoothsql\search\GateWay;
use oat\generis\model\kernel\persistence\smoothsql\search\ResourceSearchService;
use oat\generis\test\FileSystemMockTrait;
use oat\generis\test\OntologyMockTrait;
use oat\generis\test\ServiceManagerMockTrait;
use oat\search\base\QueryBuilderInterface;
use oat\search\base\QueryInterface;
use oat\search\base\ResultSetInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ResourceSearchServiceTest extends TestCase
{
    use ServiceManagerMockTrait;
    use OntologyMockTrait;
    use FileSystemMockTrait;

    /** @var ResourceSearchService */
    private $subject;

    /** @var ComplexSearchService|MockObject */
    private $complexSearchService;

    public function setUp(): void
    {
        $this->complexSearchService = $this->createMock(ComplexSearchService::class);

        $this->subject = new ResourceSearchService();
        $this->subject->setServiceLocator(
            $this->getServiceManagerMock(
                [
                    ComplexSearchService::SERVICE_ID => $this->complexSearchService
                ]
            )
        );
    }

    public function testFindByClassUri(): void
    {
        $queryBuilder = $this->createMock(QueryBuilderInterface::class);
        $criteria = $this->createMock(QueryInterface::class);
        $gateway = $this->createMock(GateWay::class);

        $this->complexSearchService
            ->method('query')
            ->willReturn($queryBuilder);

        $this->complexSearchService
            ->method('searchType')
            ->with($queryBuilder, 'uri', true)
            ->willReturn($criteria);

        $this->complexSearchService
            ->method('getGateway')
            ->willReturn($gateway);

        $queryBuilder->method('setCriteria')
            ->willReturnSelf();

        $gateway->method('search')
            ->willReturn($this->createMock(ResultSetInterface::class));

        $result = $this->subject->findByClassUri('uri');

        $this->assertInstanceOf(ResultSetInterface::class, $result);
    }
}
