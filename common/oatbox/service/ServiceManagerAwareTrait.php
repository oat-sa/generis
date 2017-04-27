<?php
/**
 * Created by PhpStorm.
 * User: siwane
 * Date: 26/04/17
 * Time: 16:28
 */

namespace oat\oatbox\service;


use Zend\ServiceManager\ServiceLocatorAwareTrait;

trait ServiceManagerAwareTrait
{
    use ServiceLocatorAwareTrait {
        getServiceLocator as protected getZendServiceLocator;
    }

    /**
     * @throws \common_exception_Error
     * @return ServiceManager
     */
    public function getServiceLocator()
    {
        $serviceManager = $this->getZendServiceLocator();
        if (! $serviceManager instanceof ServiceManager) {
            throw new \common_exception_Error('Alternate service locator not compatible with ' . __CLASS__);
        }
        return $serviceManager;
    }

    public function registerService($serviceKey, $service, $allowOverride = true)
    {
        if ($allowOverride || ! $this->getServiceLocator()->has($serviceKey)) {
            $this->getServiceLocator()->register($serviceKey, $service);
        }
    }

}