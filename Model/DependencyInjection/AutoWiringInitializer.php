<?php

namespace oat\generis\Model\DependencyInjection;

use oat\generis\Model\Config\AbstractInitializer;

/**
 * Class used to initialize the Auto wiring config for the platform
 *
 * @author Martijn Swinkels <m.swinkels@taotesting.com>
 */
class AutoWiringInitializer extends AbstractInitializer
{

    /**
     * @inheritdoc
     */
    public function __construct($fileName = 'extension.yml')
    {
        parent::__construct($fileName);
    }
}