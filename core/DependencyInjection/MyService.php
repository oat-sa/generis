<?php

namespace oat\generis\model\DependencyInjection;

use oat\generis\persistence\PersistenceManager;

//@TODO @FIXME Delete this class after PoC...
class MyService
{
    /** @var PersistenceManager */
    private $persistenceManager;

    /** @var OptionsInterface */
    private $options;

//    public function __construct(OptionsInterface $options, PersistenceManager $persistenceManager)
//    {
//        $this->persistenceManager = $persistenceManager;
//        $this->options = $options;
//    }

    public function test()
    {
        echo '========= TEST ========';
//        var_dump($this->options->getOptions());
//        var_dump($this->persistenceManager->hasPersistence('default'));
    }
}
