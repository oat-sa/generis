<?php

namespace oat\generis\model\DependencyInjection\Poc;

use oat\generis\model\DependencyInjection\ServiceOptionsInterface;
use oat\generis\persistence\PersistenceManager;

//@TODO @FIXME Delete this class after PoC...
class MyService
{
    public const OPTION_TEST = 'option_test';

    /** @var PersistenceManager */
    private $persistenceManager;

    /** @var ServiceOptionsInterface */
    private $options;

    public function __construct(PersistenceManager $persistenceManager, ServiceOptionsInterface $options)
    {
        $this->persistenceManager = $persistenceManager;
        $this->options = $options;
    }

    public function test()
    {
        echo '========= TEST ========';
        echo get_class($this->persistenceManager);
        echo get_class($this->options);
    }
}
