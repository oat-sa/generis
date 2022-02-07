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
 * Copyright (c) 2022 (original work) Open Assessment Technologies SA;
 */

declare(strict_types=1);

namespace oat\generis\test\unit\core\kernel\persistence\smoothsql;

use core_kernel_classes_Resource;
use core_kernel_classes_Triple;
use core_kernel_persistence_smoothsql_SmoothModel;
use oat\generis\test\GenerisTestCase;

class SmoothModelTest extends GenerisTestCase
{
    /** @var core_kernel_persistence_smoothsql_SmoothModel */
    private $sut;

    public function setUp(): void
    {
        $this->sut = new core_kernel_persistence_smoothsql_SmoothModel();
        $this->sut->setOption(
            core_kernel_persistence_smoothsql_SmoothModel::OPTION_WRITEABLE_MODELS,
            [
                core_kernel_persistence_smoothsql_SmoothModel::DEFAULT_WRITABLE_MODEL,
            ]
        );
    }

    public function testIsWritableByDefault(): void
    {
        $this->assertTrue(
            $this->sut->isWritable(
                $this->createResourceWithTriples([])
            )
        );
    }

    public function testIsWritable(): void
    {
        $this->assertTrue(
            $this->sut->isWritable(
                $this->createResourceWithTriples(
                    [
                        core_kernel_persistence_smoothsql_SmoothModel::DEFAULT_WRITABLE_MODEL,
                    ]
                )
            )
        );
    }

    public function testIsNotWritable(): void
    {
        $this->assertFalse(
            $this->sut->isWritable(
                $this->createResourceWithTriples(
                    [
                        core_kernel_persistence_smoothsql_SmoothModel::DEFAULT_WRITABLE_MODEL,
                        core_kernel_persistence_smoothsql_SmoothModel::DEFAULT_READ_ONLY_MODEL
                    ]
                )
            )
        );
    }

    private function createResourceWithTriples(array $modelIds): core_kernel_classes_Resource
    {
        $triples = [];

        foreach ($modelIds as $modelId) {
            $triple = $this->createMock(core_kernel_classes_Triple::class);
            $triple->modelid = $modelId;

            $triples[] = $triple;
        }

        $resource = $this->createMock(core_kernel_classes_Resource::class);
        $resource->method('getRdfTriples')
            ->willReturn($triples);

        return $resource;
    }
}
