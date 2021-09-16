<?php

namespace oat\generis\model\DependencyInjection;

use common_ext_Extension;
use common_ext_ExtensionsManager;
use oat\oatbox\service\ServiceManager;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder as SymfonyContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;

class ContainerBuilder extends SymfonyContainerBuilder
{
    public function build(): ContainerInterface
    {
        /**
         * @TODO
         * [ ] Cache layer
         */
        $tmpDir = sys_get_temp_dir();
        file_put_contents($tmpDir . '/services.php', $this->getTemporaryServiceFileContent());

        $loader1 = new PhpFileLoader($this, new FileLocator([$tmpDir]));
        $loader1->load('services.php');

        $loader2 = new LegacyFileLoader($this, new FileLocator([CONFIG_PATH]));
        $loader2->load('*/*.conf.php');

        return $this;
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
        $contents = [];

        /** @var common_ext_Extension $extension */
        foreach ($this->getExtensionManager()->getInstalledExtensions() as $extension) {
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
