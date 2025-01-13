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
 * Copyright (c) 2021-2025 (original work) Open Assessment Technologies SA;
 */

declare(strict_types=1);

namespace oat\generis\model\DependencyInjection;

use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * @note Class used only to help load not supported services from container.
 *       Please DO NOT use it for other purposes.
 *
 * @internal
 */
class BaseContainer extends Container
{
    /** @var LegacyServiceGateway|null */
    private $legacyContainer;

    public function __construct(ParameterBagInterface $parameterBag = null, ContainerInterface $legacyContainer = null)
    {
        parent::__construct($parameterBag);

        $this->legacyContainer = $legacyContainer ?? new LegacyServiceGateway();
    }

    /**
     * @inheritDoc
     */
    public function get($id, int $invalidBehavior = self::EXCEPTION_ON_INVALID_REFERENCE)
    {
        try {
            return parent::get($id, self::NULL_ON_INVALID_REFERENCE) ?? $this->legacyContainer->get($id);
        } catch (NotFoundExceptionInterface $exception) {
            if ($invalidBehavior >= self::NULL_ON_INVALID_REFERENCE) {
                return null;
            }

            throw $exception;
        }
    }

    /**
     * @inheritDoc
     */
    public function has($id)
    {
        return parent::has($id) || $this->legacyContainer->has($id);
    }
}
