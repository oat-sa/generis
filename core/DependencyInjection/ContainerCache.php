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

use Psr\Container\ContainerInterface;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\DependencyInjection\ContainerBuilder as SymfonyContainerBuilder;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;

class ContainerCache
{
    /** @var string */
    private $cacheFile;

    /** @var ConfigCache */
    private $configCache;

    /** @var SymfonyContainerBuilder */
    private $builder;

    /** @var PhpDumper */
    private $dumper;

    /** @var string */
    private $cachedContainerClassName;

    public function __construct(
        string $cacheFile,
        SymfonyContainerBuilder $builder,
        ConfigCache $configCache = null,
        PhpDumper $dumper = null,
        bool $isDebugEnabled = null,
        string $cachedContainerClassName = null
    ) {
        $this->cacheFile = $cacheFile;
        $this->builder = $builder;
        $this->configCache = $configCache ?? new ConfigCache(
            $this->cacheFile,
            $isDebugEnabled ?? $this->isEnvVarTrue('DI_CONTAINER_DEBUG')
        );

        $this->dumper = $dumper;
        $this->cachedContainerClassName = $cachedContainerClassName ?? 'MyCachedContainer';
    }

    public function isFresh(): bool
    {
        return $this->configCache->isFresh();
    }

    public function load(): ContainerInterface
    {
        if ($this->isFresh()) {
            return $this->getCachedContainer();
        }

        return $this->forceLoad();
    }

    public function forceLoad(): ContainerInterface
    {
        $this->builder->compile();

        $this->configCache->write(
            $this->getDumper()->dump(
                [
                    'class' => $this->cachedContainerClassName,
                    'base_class' => BaseContainer::class
                ]
            ),
            $this->builder->getResources()
        );

        return $this->getCachedContainer();
    }

    private function getCachedContainer(): ContainerInterface
    {
        require_once $this->cacheFile;

        return new $this->cachedContainerClassName();
    }

    private function isEnvVarTrue(string $envVar): bool
    {
        if (!isset($_ENV[$envVar])) {
            return false;
        }

        return filter_var($_ENV[$envVar], FILTER_VALIDATE_BOOLEAN) ?? false;
    }

    private function getDumper(): PhpDumper
    {
        return $this->dumper ?? new PhpDumper($this->builder);
    }
}
