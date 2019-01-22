<?php

namespace oat\generis\Model;

use common_Config;
use oat\oatbox\service\ServiceManager;
use oat\oatbox\service\ServiceManagerAwareTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Base CLI Command class
 *
 * @author Martijn Swinkels <martijn@taotesting.com>
 */
class ConsoleCommand extends Command
{

    use ServiceManagerAwareTrait;

    /**
     * @var SymfonyStyle
     */
    protected $io;

    /**
     * Initialize the command.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        common_Config::load();
        $this->io = new SymfonyStyle($input, $output);
    }

    /**
     * @return ServiceManager
     */
    public function getServiceManager()
    {
        return ServiceManager::getServiceManager();
    }

}