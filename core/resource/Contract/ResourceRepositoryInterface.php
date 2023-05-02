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
 * Copyright (c) 2021 (original work) Open Assessment Technologies SA.
 */

declare(strict_types=1);

namespace oat\generis\model\resource\Contract;

use BadMethodCallException;
use common_exception_Error;
use core_kernel_classes_Resource;
use InvalidArgumentException;
use oat\generis\model\Context\ContextInterface;
use oat\search\base\exception\SearchGateWayExeption;
use RuntimeException;

interface ResourceRepositoryInterface
{
    /**
     * @throws common_exception_Error
     * @throws SearchGateWayExeption
     * @throws BadMethodCallException
     *
     * @return array|core_kernel_classes_Resource[]
     */
    public function findBy(ContextInterface $context): array;

    /**
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public function delete(ContextInterface $context): void;
}
