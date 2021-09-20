<?php

namespace oat\generis\model\DependencyInjection\Poc;

use oat\generis\model\data\Ontology;
use oat\generis\model\DependencyInjection\ServiceOptionsInterface;
use oat\generis\persistence\PersistenceManager;
use oat\oatbox\filesystem\FileSystemService;
use oat\oatbox\log\LoggerService;
use oat\tao\model\security\ActionProtector;

//@TODO @FIXME Delete this class after PoC...
class MyService
{
    public const OPTION_TEST = 'option_test';

    /** @var PersistenceManager */
    private $persistenceManager;

    /** @var ServiceOptionsInterface */
    private $options;

    /** @var FileSystemService */
    private $fileSystemService;

    /** @var LoggerService */
    private $loggerService;

    /** @var Ontology */
    private $ontology;
    /**
     * @var ActionProtector
     */
    private $actionProtector;

    public function __construct(
        PersistenceManager $persistenceManager,
        FileSystemService $fileSystemService,
        LoggerService $loggerService,
        Ontology $ontology,
        ServiceOptionsInterface $options,
        ActionProtector $actionProtector
    ) {
        $this->persistenceManager = $persistenceManager;
        $this->fileSystemService = $fileSystemService;
        $this->loggerService = $loggerService;
        $this->ontology = $ontology;
        $this->options = $options;
        $this->actionProtector = $actionProtector;
    }

    public function test()
    {
        echo '========= TEST ========';
        echo get_class($this->persistenceManager);
        echo get_class($this->options);
        echo get_class($this->fileSystemService);
        echo get_class($this->loggerService);
        echo get_class($this->ontology);
        echo get_class($this->actionProtector);
    }
}
