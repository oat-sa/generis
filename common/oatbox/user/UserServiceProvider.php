<?php

namespace oat\oatbox\user;

use oat\generis\model\DependencyInjection\ContainerServiceProviderInterface;
use oat\tao\model\featureFlag\FeatureFlagChecker;
use oat\tao\model\user\implementation\UserSettingsService;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

class UserServiceProvider implements ContainerServiceProviderInterface
{
    public function __invoke(ContainerConfigurator $configurator): void
    {
        $services = $configurator->services();

        $services->set(UserInterfaceModeService::class, UserInterfaceModeService::class)
            ->public()
            ->args(
                [
                    service(FeatureFlagChecker::class),
                    service(UserSettingsService::class)
                ]
            );
    }
}