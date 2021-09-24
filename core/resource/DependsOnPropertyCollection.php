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

namespace oat\generis\model\resource;

use ArrayIterator;
use core_kernel_classes_Property;

/**
 * @method core_kernel_classes_Property|null current()
 */
class DependsOnPropertyCollection extends ArrayIterator
{
    public function isEqual(DependsOnPropertyCollection $dependsOnPropertyCollection): bool
    {
        return $this->areArraysEqual($this->getPropertyUris(), $dependsOnPropertyCollection->getPropertyUris());
    }

    public function getPropertyUris(): array
    {
        return array_map(
            static function (core_kernel_classes_Property $property) {
                return $property->getUri();
            },
            $this->getArrayCopy()
        );
    }

    private function areArraysEqual(array $array1, array $array2): bool
    {
        return empty(array_diff($array1, $array2)) && empty(array_diff($array2, $array1));
    }
}
