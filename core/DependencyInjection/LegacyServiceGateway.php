<?php

namespace oat\generis\model\DependencyInjection;

use oat\oatbox\service\ServiceManager;

class LegacyServiceGateway
{
    /** @var ServiceManager|null */
    private $serviceManager;

    public function __construct(ServiceManager $serviceManager = null)
    {
        $this->serviceManager = $serviceManager ?? ServiceManager::getServiceManager();
    }

    public function __invoke($id = null)
    {
        return $id ? $this->serviceManager->get($id) : $this->serviceManager;
    }
}
