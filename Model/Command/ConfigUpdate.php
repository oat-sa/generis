<?php

namespace oat\generis\Model\Command;

use oat\generis\Model\Config\Initialize;
use oat\generis\Model\Console\ConsoleCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ConfigUpdate
 *
 * @author Martijn Swinkels <m.swinkels@taotesting.com>
 */
class ConfigUpdate extends ConsoleCommand
{

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setName('tao:config:update')
            ->setDescription('Rebuilds the configuration for the platform');
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->title('Rebuilding config');
        $configInitalizer = new Initialize();
        $configInitalizer->initialize(true);
        $this->success('Config has been rebuilt!');
    }
}
