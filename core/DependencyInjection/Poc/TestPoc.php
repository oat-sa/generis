<?php

namespace oat\generis\model\DependencyInjection\Poc;

use oat\generis\model\data\Ontology;
use oat\generis\model\DependencyInjection\ServiceOptions;
use oat\generis\persistence\PersistenceManager;
use oat\oatbox\filesystem\FileSystemService;
use oat\oatbox\log\LoggerService;
use oat\oatbox\service\ConfigurableService;
use oat\tao\model\security\ActionProtector;
use Pimple\Psr11\Container;

//@TODO @FIXME Delete this class after PoC...
class TestPoc extends ConfigurableService
{
    private const LIMIT = 100;

    public function testWithServiceManager(): void
    {
        $serviceManager = $this->getServiceManager();

        for ($i = 0; $i <= self::LIMIT; $i++) {
            $persistenceManager = $serviceManager->get(PersistenceManager::SERVICE_ID);
            $fileSystemService = $serviceManager->get(FileSystemService::SERVICE_ID);
            $loggerService = $serviceManager->get(LoggerService::SERVICE_ID);
            $ontology = $serviceManager->get(Ontology::SERVICE_ID);
            $options = $serviceManager->get(ServiceOptions::SERVICE_ID);
            $actionProtector = $serviceManager->get(ActionProtector::SERVICE_ID);
        }
    }

    public function testWithContainer(): void
    {
        /** @var Container $container */
        $container = $this->getServiceManager()->getContainer();

        for ($i = 0; $i <= self::LIMIT; $i++) {
            /** @var MyService $myService */ // This service includes all the previous 5 in its constructor
            //$myService = $container->get(MyService::class);

            $persistenceManager = $container->get(PersistenceManager::SERVICE_ID);
            $fileSystemService = $container->get(FileSystemService::SERVICE_ID);
            $loggerService = $container->get(LoggerService::SERVICE_ID);
            $ontology = $container->get(Ontology::SERVICE_ID);
            $options = $container->get(ServiceOptions::SERVICE_ID);
            $actionProtector = $container->get(ActionProtector::SERVICE_ID);
        }
    }
}
