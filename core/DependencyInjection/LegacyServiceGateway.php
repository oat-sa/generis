<?php

namespace oat\generis\model\DependencyInjection;

use oat\oatbox\service\ServiceManager;

class LegacyServiceGateway
{
    public function __invoke($id = null)
    {
        if ($id) {
            return ServiceManager::getServiceManager()->get($id);
        }

        return ServiceManager::getServiceManager();
    }
}
