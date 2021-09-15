<?php

namespace oat\generis\model\DependencyInjection;

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
            /**
             * @TODO
             * [ ] Read services from all extensions
             * [ ] Cache layer
             */
            $servicesPaths = [
                __DIR__ . '/../../config/dependency-injection'
            ];

            $containerBuilder = new ContainerBuilder();
            $loader = new PhpFileLoader($containerBuilder, new FileLocator($servicesPaths));
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
    public function has(string $id)
    {
        try {
            return $this->getServiceLocator()->has($id);
        } catch (ServiceNotFoundException $exception) {
            return $this->getContainer()->has($id);
        }
    }

    private function getServiceLocator(): ServiceManager
    {
        return ServiceManager::getServiceManager();
    }
}
