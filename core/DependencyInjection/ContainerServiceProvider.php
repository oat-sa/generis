<?php

namespace oat\generis\model\DependencyInjection;

use oat\generis\model\DependencyInjection\Poc\MyService;
use oat\generis\persistence\PersistenceManager;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\env;
use function Symfony\Component\DependencyInjection\Loader\Configurator\param;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

class ContainerServiceProvider implements ContainerServiceProviderInterface
{
    public function __invoke(ContainerConfigurator $configurator): void
    {
        $services = $configurator->services();

        $services->set(LegacyServiceGateway::class, LegacyServiceGateway::class);

        $services->set(MyService::class, MyService::class)
            ->public()
            ->args(
                [
                    service(PersistenceManager::SERVICE_ID),
                    service(ServiceOptions::SERVICE_ID)
                ]
            );
    }
}
