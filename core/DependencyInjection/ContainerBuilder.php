<?php

namespace oat\generis\model\DependencyInjection;

use common_ext_Extension;
use common_ext_ExtensionsManager;
use InvalidArgumentException;
use oat\oatbox\service\ServiceManager;
use Psr\Container\ContainerInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder as SymfonyContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;

class ContainerBuilder extends SymfonyContainerBuilder
{
    /** @var ContainerCache */
    private $cache;

    /** @var bool|null */
    private $temporaryDirectory;

    /** @var string|null */
    private $configPath;

    public function __construct(
        string $configPath,
        string $cacheFile,
        bool $isDebug = null,
        bool $temporaryDirectory = null,
        ContainerCache $cache = null
    ) {
        $this->cache = $cache ?? new ContainerCache(
            $cacheFile,
            $this,
            null,
            null,
            $isDebug ?? false
        );

        $this->configPath = $configPath;
        $this->temporaryDirectory = $temporaryDirectory ?? sys_get_temp_dir();

        parent::__construct();
    }

    public function build(): ContainerInterface
    {
        if (!is_writable($this->temporaryDirectory)) {
            throw new InvalidArgumentException(
                sprintf(
                    'DI container build requires directory "%" to be writable',
                    $this->temporaryDirectory
                )
            );
        }

        file_put_contents($this->temporaryDirectory . '/services.php', $this->getTemporaryServiceFileContent());

        $phpLoader = new PhpFileLoader(
            $this,
            new FileLocator(
                [
                    $this->temporaryDirectory
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

        return $this->cache->load();
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
