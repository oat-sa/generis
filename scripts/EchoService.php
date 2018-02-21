<?php
/**
 * Created by PhpStorm.
 * User: siwane
 * Date: 21/02/18
 * Time: 14:45
 */

namespace oat\generis\scripts;


use oat\oatbox\service\ConfigurableService;

class EchoService extends ConfigurableService implements Printer
{
    const OPTION_WORD = 'word';

    public function printWord()
    {
        echo $this->getOption(self::OPTION_WORD) . PHP_EOL;
    }
}