<?php

namespace oat\generis\model\DependencyInjection;

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

interface ContainerServiceProviderInterface
{
    public function __invoke(ContainerConfigurator $configurator): void;
}
