<?php

namespace oat\generis\scripts;

use oat\oatbox\service\ConfigurableService;

class MultipleEchoService extends ConfigurableService implements Printer
{
    const OPTION_INNER = 'inner';
    const OPTION_OCCURRENCE = 'occurrence';

    public function printWord()
    {
        for ($i=0; $i < $this->getOption(self::OPTION_OCCURRENCE); $i++) {
            $this->getInner()->printWord();
        }
    }

    protected function getInner()
    {
        return $this->getOption('inner');
    }
}