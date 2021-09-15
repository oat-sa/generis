<?php

namespace oat\generis\model\DependencyInjection;

use common_ext_Extension;
use common_ext_ExtensionsManager;
use oat\oatbox\service\ServiceManager;
use oat\oatbox\service\ServiceNotFoundException;
use Psr\Container\ContainerInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;

class Container implements ContainerInterface
{
    /** @var ContainerInterface */
    private $container;

    private function getContainer(): ContainerInterface
    {
        if (!$this->container) {
            $tmpDir = sys_get_temp_dir();
            file_put_contents($tmpDir . '/services.php', $this->getTemporaryServiceFileContent());

            /**
             * @TODO
             * [ ] Cache layer
             */
            $containerBuilder = new ContainerBuilder();
            $loader = new PhpFileLoader($containerBuilder, new FileLocator([$tmpDir]));
            $loader->load('services.php');

            $this->container = $containerBuilder;
        }

        return $this->container;
    }

    /**
     * @inheritDoc
     */
    public function get(string $id)
    {
        try {
            return $this->getServiceLocator()->get($id);
        } catch (ServiceNotFoundException $exception) {
            return $this->getContainer()->get($id);
        }
    }

    /**
     * @inheritDoc
     */
    public function has(string $id): bool
    {
        return $this->getServiceLocator()->has($id) || $this->getContainer()->has($id);
    }

    private function getServiceLocator(): ServiceManager
    {
        return ServiceManager::getServiceManager();
    }

    private function getExtensionManager(): common_ext_ExtensionsManager
    {
        return $this->getServiceLocator()->get(common_ext_ExtensionsManager::SERVICE_ID);
    }

    private function getTemporaryServiceFileContent(): string
    {
        $file = '
        <?php
        use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
        
        return function (ContainerConfigurator $configurator): void
        {
            %s
        };';

        $contents = [];

        /** @var common_ext_Extension $extension */
        foreach ($this->getExtensionManager()->getInstalledExtensions() as $extension) {
            foreach ($extension->getManifest()->getContainerServiceProvider() as $serviceProvider) {
                $contents[] = '(new ' . $serviceProvider . '())($configurator);';
            }
        }

        return vsprintf($file, $contents);
    }
}
