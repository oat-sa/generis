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
 */

declare(strict_types=1);

namespace oat\generis\model\DependencyInjection;

use common_ext_Extension;
use common_ext_ExtensionsManager;
use InvalidArgumentException;
use Psr\Container\ContainerInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder as SymfonyContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;

class ContainerBuilder extends SymfonyContainerBuilder
{
    /** @var ContainerCache */
    private $cache;

    /** @var bool|null */
    private $cachePath;

    /** @var string|null */
    private $configPath;

    /** @var common_ext_ExtensionsManager */
    private $extensionsManager;

    public function __construct(
        string $configPath,
        string $cachePath,
        common_ext_ExtensionsManager $extensionsManager,
        bool $isDebugEnabled = null,
        ContainerCache $cache = null
    ) {
        $this->configPath = $configPath;
        $this->cachePath = $cachePath;
        $this->extensionsManager = $extensionsManager;
        $this->cache = $cache ?? new ContainerCache(
            $cachePath . '_di/container.php',
            $this,
            null,
            null,
            $isDebugEnabled
        );

        parent::__construct();
    }

    public function build(): ContainerInterface
    {
        if ($this->cache->isFresh()) {
            return $this->cache->load();
        }

        return $this->forceBuild();
    }

    public function forceBuild(): ContainerInterface
    {
        if (!is_writable($this->cachePath)) {
            throw new InvalidArgumentException(
                sprintf(
                    'DI container build requires directory "%" to be writable',
                    $this->cachePath
                )
            );
        }

        file_put_contents($this->cachePath . '/services.php', $this->getTemporaryServiceFileContent());

        $phpLoader = new PhpFileLoader(
            $this,
            new FileLocator(
                [
                    $this->cachePath
                ]
            )
        );
        $phpLoader->load('services.php');

        $legacyLoader = new LegacyFileLoader(
            $this,
            new FileLocator(
                [
                    $this->configPath
                ]
            )
        );
        $legacyLoader->load('*/*.conf.php');

        return $this->cache->forceLoad();
    }

    private function getTemporaryServiceFileContent(): string
    {
        $contents = [];

        /** @var common_ext_Extension $extension */
        foreach ($this->extensionsManager->getInstalledExtensions() as $extension) {
            foreach ($extension->getManifest()->getContainerServiceProvider() as $serviceProvider) {
                $contents[] = '(new ' . $serviceProvider . '())($configurator);';
            }
        }

        return vsprintf(
            '<?php
        use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
        
        return function (ContainerConfigurator $configurator): void
        {
            %s
        };',
            $contents
        );
    }
}
