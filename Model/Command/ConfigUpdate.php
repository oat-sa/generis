<?php

namespace oat\generis\Model\Command;

use oat\generis\Model\Config\ConfigInitializer;
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
        $this->setName('config:update')
            ->setDescription('Rebuilds the configuration for the platform');
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->writeln('Rebuilding config');
        $configInitializer = new ConfigInitializer();
        $configInitializer->initialize(true);
        $this->success('Config has been rebuilt!');
    }
}
