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

use Closure;
use oat\oatbox\config\ConfigurationService;
use oat\oatbox\Configurable;
use oat\oatbox\service\ConfigurableService;
use oat\tao\model\OntologyClassService;
use oat\taoDeliveryRdf\model\DeliveryAssemblyService;
use oat\taoGroups\models\GroupsService;
use oat\taoLti\models\classes\ConsumerService;
use oat\taoOutcomeUi\model\ResultsService;
use oat\taoTestTaker\models\TestTakerService;
use ReflectionClass;
use SplFileInfo;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\FileLoader;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use taoItems_models_classes_ItemsService;
use taoQtiTest_models_classes_QtiTestService;
use taoTests_models_classes_TestsService;

class LegacyFileLoader extends FileLoader
{
    private const LEGACY_CONFIGURABLE_CLASS_LIST = [
        ConfigurableService::class,
        Configurable::class,
        ConfigurationService::class
    ];

    private const UNSUPPORTED_LEGACY_CLASSES = [
        taoTests_models_classes_TestsService::class,
        taoItems_models_classes_ItemsService::class,
        TestTakerService::class,
        GroupsService::class,
        ResultsService::class,
        ConsumerService::class,
        DeliveryAssemblyService::class,
        taoQtiTest_models_classes_QtiTestService::class,
    ];

    /**
     * @inheritDoc
     */
    public function supports($resource, string $type = null)
    {
        return stripos($resource, '*.conf.php') !== 0;
    }

    /**
     * @inheritDoc
     */
    public function load($resource, string $type = null)
    {
        $loadClassClosure = $this->createLoadClosure();

        /**
         * @var string $path
         * @var SplFileInfo $info
         */
        foreach ($this->glob($resource, false, $globResource) as $path => $info) {
            try {
                $class = $loadClassClosure($path);

                if ($this->isLegacyClass($class)) {
                    $this->registerClassDefinition($info, $class);
                }
            } finally {
                $this->instanceof = [];
                $this->registerAliasesForSinglyImplementedInterfaces();
            }
        }

        $this->autoWireUnsupportedLegacyClasses();
    }

    /**
     * @param object $class
     */
    private function registerClassDefinition(SplFileInfo $info, $class): void
    {
        $className = get_class($class);
        $serviceName = $this->getServiceName($className);
        $alias = $this->createAlias($info);

        $definition = new Definition($serviceName);
        $definition->setAutowired(true)
            ->setPublic(true)
            ->setFactory(new Reference(LegacyServiceGateway::class))
            ->setArguments([$alias]);

        $this->container->setDefinition($serviceName, $definition);

        $this->container->setAlias($alias, $serviceName)
            ->setPublic(true);

        if ($className !== $serviceName) {
            $this->container->setAlias($className, $serviceName)
                ->setPublic(true);
        }
    }

    private function createLoadClosure(): Closure
    {
        return Closure::bind(
            function ($path) {
                return include $path;
            },
            $this,
            ProtectedPhpFileLoader::class
        );
    }

    private function getServiceName(string $className): string
    {
        $interfacesWithServiceId = array_filter(
            (new ReflectionClass($className))->getInterfaces(),
            function (ReflectionClass $int) {
                return array_key_exists('SERVICE_ID', $int->getConstants());
            }
        );

        if ($interfacesWithServiceId) {
            $interface = array_pop($interfacesWithServiceId);

            return $interface->getName();
        }

        return $className;
    }

    private function createAlias(SplFileInfo $info): string
    {
        $pathInfo = explode('/', pathinfo($info->getRealPath(), PATHINFO_DIRNAME));
        $prefix = end($pathInfo);

        return $prefix . '/' . $info->getBasename('.conf.php');
    }

    /**
     * @param object $aClass
     */
    private function isLegacyClass($aClass): bool
    {
        $legacy = array_filter(
            self::LEGACY_CONFIGURABLE_CLASS_LIST,
            function ($class) use ($aClass) {
                return is_a($aClass, $class);
            }
        );

        return !empty($legacy);
    }

    private function autoWireUnsupportedLegacyClasses(): void
    {
        foreach (array_merge(get_declared_classes(), self::UNSUPPORTED_LEGACY_CLASSES) as $class) {
            if (is_subclass_of($class, OntologyClassService::class)) {
                $this->container->setDefinition(
                    $class,
                    (new Definition($class))
                        ->setAutowired(true)
                        ->setPublic(true)
                        ->setClass($class)
                );
            }
        }
    }
}

/**
 * @internal
 */
final class ProtectedPhpFileLoader extends PhpFileLoader
{
}
