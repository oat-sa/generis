<?php

namespace oat\oatbox\service;

class ConfigurationService extends ConfigurableService
{
    const OPTION_CONFIG = 'config';

    public function getHeader()
    {
        $header = parent::getHeader();
        $header .= PHP_EOL .
            '/**' . PHP_EOL .
            ' * To avoid your config to be wrapped, use a ConfigurableService :)' . PHP_EOL .
            ' */ ' . PHP_EOL;
        return $header;
    }

}