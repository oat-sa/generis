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

use common_ext_Extension;
use common_ext_ExtensionsManager;
use InvalidArgumentException;
use oat\generis\model\Middleware\MiddlewareExtensionsMapper;
use Psr\Container\ContainerInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder as SymfonyContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface as SymfonyContainerInterface;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException as SymfonyServiceNotFoundException;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use oat\oatbox\service\ServiceNotFoundException;
use Symfony\Component\DependencyInjection\Reference;
use Throwable;

class ContainerBuilder extends SymfonyContainerBuilder
{
    /** @var ContainerCache */
    private $cache;

    /** @var bool|null */
    private $cachePath;

    /** @var ContainerInterface */
    private $legacyContainer;

    /** @var MiddlewareExtensionsMapper|null */
    private $middlewareExtensionsMapper;

    /** @var string|null */
    private $configPath;

    /** @var bool */
    private static $containerIsAlreadyBuilt = false;

    public function __construct(
        string $cachePath,
        ContainerInterface $legacyContainer,
        bool $isDebugEnabled = null,
        ContainerCache $cache = null,
        MiddlewareExtensionsMapper $middlewareExtensionsMapper = null,
        string $configPath = null
    ) {
        $this->cachePath = $cachePath;
        $this->legacyContainer = $legacyContainer;
        $this->cache = $cache ?? new ContainerCache(
            $cachePath . '_di/container.php',
            $this,
            null,
            null,
            $isDebugEnabled
        );

        parent::__construct();
        $this->middlewareExtensionsMapper = $middlewareExtensionsMapper ?? new MiddlewareExtensionsMapper();
        $this->configPath = $configPath ?? (defined('CONFIG_PATH') ? CONFIG_PATH : null);
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
        if (self::$containerIsAlreadyBuilt) {
            return $this->cache->load();
        }

        if (!$this->isApplicationInstalled()) {
            return $this->legacyContainer;
        }

        if (!is_writable($this->cachePath)) {
            throw new InvalidArgumentException(
                sprintf(
                    'DI container build requires directory "%s" to be writable',
                    $this->cachePath
                )
            );
        }

        try {
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

            self::$containerIsAlreadyBuilt = true;

            return $this->cache->forceLoad();
        } catch (Throwable $exception) {
            self::$containerIsAlreadyBuilt = false;

            throw $exception;
        }
    }

    /**
     * @inheritDoc
     */
    public function get(string $id, int $invalidBehavior = SymfonyContainerInterface::EXCEPTION_ON_INVALID_REFERENCE)
    {
        try {
            return parent::get($id, $invalidBehavior);
        } catch (SymfonyServiceNotFoundException $exception) {
        }

        try {
            return $this->legacyContainer->get($id);
        } catch (ServiceNotFoundException $exception) {
            throw new SymfonyServiceNotFoundException($id);
        }
    }

    /**
     * @inheritDoc
     */
    public function has(string $id)
    {
        if (parent::has($id)) {
            return true;
        }

        try {
            $this->legacyContainer->get($id);

            return true;
        } catch (ServiceNotFoundException $exception) {
            return false;
        }
    }

    /**
     * @inheritDoc
     */
    public function findDefinition(string $id)
    {
        try {
            return parent::findDefinition($id);
        } catch (SymfonyServiceNotFoundException $exception) {
            return (new Definition($id))
                ->setAutowired(true)
                ->setPublic(true)
                ->setFactory(new Reference(LegacyServiceGateway::class))
                ->setArguments([$id]);
        }
    }

    private function getTemporaryServiceFileContent(): string
    {
        $contents = [];

        /** @var common_ext_Extension[] $extensions */
        $extensions = $this->getExtensionsManager()->getInstalledExtensions();

        foreach ($extensions as $extension) {
            foreach ($extension->getManifest()->getContainerServiceProvider() as $serviceProvider) {
                $contents[] = '(new ' . $serviceProvider . '())($configurator);';
            }
        }

        return sprintf(
            '<?php
        use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
        use \oat\generis\model\Middleware\MiddlewareExtensionsMapper;
        
        return function (ContainerConfigurator $configurator): void
        {
            $parameter = $configurator->parameters();
            $parameter->set(MiddlewareExtensionsMapper::MAP_KEY, %s);
        
            %s
        };',
            var_export($this->middlewareExtensionsMapper->map($extensions), true),
            implode('', $contents)
        );
    }

    /**
     * @note This method as the $legacyContainer needs to be here in order to avoid to load the
     *       common_ext_ExtensionsManager unnecessarily during this class initialization.
     */
    private function getExtensionsManager(): common_ext_ExtensionsManager
    {
        return $this->legacyContainer->get(common_ext_ExtensionsManager::SERVICE_ID);
    }

    private function isApplicationInstalled(): bool
    {
        return file_exists(rtrim((string)$this->configPath, '/') . '/generis/installation.conf.php');
    }
}
