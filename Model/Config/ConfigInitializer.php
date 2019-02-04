<?php

namespace oat\generis\Model\Config;

/**
 * Class used to initialize the configuration files for the platform
 *
 * @author Martijn Swinkels <m.swinkels@taotesting.com>
 */
class ConfigInitializer extends AbstractInitializer
{

    /**
     * @inheritdoc
     */
    public function __construct($fileName = 'config.yml')
    {
        parent::__construct($fileName);
    }
}