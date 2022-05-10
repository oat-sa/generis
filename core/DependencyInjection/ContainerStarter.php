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
 * Copyright (c) 2021 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 * @author Gabriel Felipe Soares <gabriel.felipe.soares@taotesting.com>
 */

declare(strict_types=1);

namespace oat\generis\model\DependencyInjection;

use LogicException;
use Psr\Container\ContainerInterface;

final class ContainerStarter
{
    /** @var ContainerInterface */
    private $container;

    /** @var ContainerBuilder */
    private $containerBuilder;

    /** @var ContainerInterface */
    private $legacyContainer;

    /** @var string|null */
    private $cachePath;

    public function __construct(
        ContainerInterface $legacyContainer,
        string $cachePath = null
    ) {
        if (!$cachePath) {
            $cachePath = defined('GENERIS_CACHE_PATH') ? GENERIS_CACHE_PATH : null;
        }

        if (!$cachePath) {
            throw new LogicException('Required application constants were not initialized!');
        }

        $this->legacyContainer = $legacyContainer;
        $this->cachePath = $cachePath;
    }

    public function getContainer(): ContainerInterface
    {
        if (!$this->container) {
            $this->container = $this->getContainerBuilder()->build();
        }

        return $this->container;
    }

    public function getContainerBuilder(): ContainerBuilder
    {
        if (!$this->containerBuilder) {
            $this->containerBuilder = new ContainerBuilder(
                $this->cachePath,
                $this->legacyContainer
            );
        }

        return $this->containerBuilder;
    }
}
