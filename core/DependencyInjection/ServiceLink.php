<?php

namespace oat\generis\model\DependencyInjection;

use oat\oatbox\service\ServiceManager;

class ServiceLink
{
    protected string $serviceId;

    /**
     * @var mixed
     */
    protected $service;

    public function __construct(string $serviceId)
    {
        $this->serviceId = $serviceId;
    }

    /**
     * Gets a service this link refers to
     *
     * @return object
     */
    public function getService()
    {
        if (!$this->service) {
            $this->service = ServiceManager::getServiceManager()->get($this->serviceId);
        }

        return $this->service;
    }

    public function __call($name, $arguments) {
        return $this->getService()->$name(...$arguments);
    }
}
