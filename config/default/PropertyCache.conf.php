<?php

/**
 * The default cache implementation
 */

use oat\generis\persistence\PersistenceManager;
use oat\oatbox\cache\PropertyCache;
use oat\oatbox\service\ServiceFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

return new class implements ServiceFactoryInterface {
    public function __invoke(ServiceLocatorInterface $serviceLocator)
    {
        try {
            $serviceLocator
                ->get(PersistenceManager::SERVICE_ID)
                ->getPersistenceById('redis');

            return new PropertyCache([
                PropertyCache::OPTION_PERSISTENCE => 'redis'
            ]);
        } catch (Exception $e) {
            return new PropertyCache([
                PropertyCache::OPTION_PERSISTENCE => 'cache'
            ]);
        }
    }
};
