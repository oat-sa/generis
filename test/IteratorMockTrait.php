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

namespace oat\generis\test;

use stdClass;
use PHPUnit\Framework\MockObject\MockObject;

trait IteratorMockTrait
{
    public function createIteratorMock(
        string $originalClassName,
        array $items = [],
        array $exceptMethods = []
    ): MockObject {
        $iteratorData = new stdClass();
        $iteratorData->array = $items;
        $iteratorData->position = 0;

        $iteratorMock = $this->createMock($originalClassName);

        if (!in_array('rewind', $exceptMethods, true)) {
            $iteratorMock
                ->expects($this->any())
                ->method('rewind')
                ->willReturnCallback(
                    static function () use ($iteratorData): void {
                        $iteratorData->position = 0;
                    }
                );
        }

        if (!in_array('current', $exceptMethods, true)) {
            $iteratorMock
                ->expects($this->any())
                ->method('current')
                ->willReturnCallback(
                    static function () use ($iteratorData) {
                        return $iteratorData->array[$iteratorData->position];
                    }
                );
        }

        if (!in_array('key', $exceptMethods, true)) {
            $iteratorMock
                ->expects($this->any())
                ->method('key')
                ->willReturnCallback(
                    static function () use ($iteratorData) {
                        return $iteratorData->position;
                    }
                );
        }

        if (!in_array('next', $exceptMethods, true)) {
            $iteratorMock
                ->expects($this->any())
                ->method('next')
                ->willReturnCallback(
                    static function () use ($iteratorData): void {
                        ++$iteratorData->position;
                    }
                );
        }

        if (!in_array('valid', $exceptMethods, true)) {
            $iteratorMock
                ->expects($this->any())
                ->method('valid')
                ->willReturnCallback(
                    static function () use ($iteratorData): bool {
                        return isset($iteratorData->array[$iteratorData->position]);
                    }
                );
        }

        if (!in_array('count', $exceptMethods, true)) {
            $iteratorMock
                ->expects($this->any())
                ->method('count')
                ->willReturnCallback(
                    static function () use ($iteratorData): int {
                        return count($iteratorData->array);
                    }
                );
        }

        return $iteratorMock;
    }
}
