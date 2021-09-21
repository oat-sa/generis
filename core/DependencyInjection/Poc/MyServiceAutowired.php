<?php

namespace oat\generis\model\DependencyInjection\Poc;

//@TODO @FIXME Delete this class after PoC...
class MyServiceAutowired
{
    /** @var MyService */
    private $myService;

    public function __construct(MyService $myService)
    {
        $this->myService = $myService;
    }

    public function test()
    {
        echo '========= Calling ' . __METHOD__ . ' ========';
    }
}
