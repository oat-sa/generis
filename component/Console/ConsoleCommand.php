<?php

namespace oat\generis\component\Console;

use common_Config;
use oat\oatbox\service\ServiceManager;
use oat\oatbox\service\ServiceManagerAwareTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
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
     * @var bool
     */
    protected $loadConfig = true;

    /**
     * @var ProgressBar
     */
    private $progressBar;

    /**
     * @var InputInterface
     */
    private $initialInput;

    /**
     * @var OutputInterface
     */
    private $initialOutput;

    /**
     * Initialize the command.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        if ($this->loadConfig === true) {
            common_Config::load();
        }

        $this->initialInput = new SymfonyStyle($input, $output);
        $this->initialOutput = new SymfonyStyle($input, $output);
        $this->io = new SymfonyStyle($input, $output);
    }

    /**
     * Create a progress bar
     *
     * @param int $max
     * @return ProgressBar
     */
    protected function createProgressBar($max = 0)
    {
        $this->initialOutput->setDecorated(true);
        $progressBar = new ProgressBar($this->initialOutput, $max);
        $progressBar->setOverwrite(true);
        $progressBar->setBarCharacter('<fg=green>▓</>');
        $progressBar->setEmptyBarCharacter('<fg=white>░</>');
        $progressBar->setProgressCharacter('<fg=yellow>▓</>');

        if ($max > 0) {
            $progressBar->setFormat('%bar%%percent:3s%% - %memory:6s%');
        } else {
            $progressBar->setFormat('%bar%');
        }

        return $progressBar;
    }

    /**
     * Write a title to the output.
     *
     * @param string|array $message
     */
    protected function title($message)
    {
        $this->io->title($message);
    }

    /**
     * Write $count newline(s) to the output.
     *
     * @param int $count
     */
    protected function newLine($count = 1)
    {
        $this->io->newLine($count);
    }

    /**
     * Write a section to the output.
     *
     * @param string|array $message
     */
    protected function section($message)
    {
        $this->io->section($message);
    }

    /**
     * Write a note to the output.
     *
     * @param string|array $message
     */
    protected function note($message)
    {
        $this->io->note($message);
    }

    /**
     * Write a list to the output.
     *
     * @param array $elements
     */
    protected function listing(array $elements)
    {
        $this->io->listing($elements);
    }

    /**
     * Write a success message to the output.
     *
     * @param string|array $message
     */
    protected function success($message)
    {
        $this->io->success($message);
    }

    /**
     * Write an error to the output.
     *
     * @param string|array $message
     */
    protected function error($message)
    {
        $this->io->error($message);
    }

    /**
     * Write a warning to the output.
     *
     * @param string|array $message
     */
    protected function caution($message)
    {
        $this->io->caution($message);
    }

    /**
     * Write a warning to the output.
     *
     * @param string|array $message
     */
    protected function warning($message)
    {
        $this->io->warning($message);
    }

    /**
     * Write a message with a newline to the output.
     *
     * @param string|array $message
     */
    protected function writeln($message)
    {
        $this->io->writeln($message);
    }

    /**
     * @return ServiceManager
     */
    public function getServiceManager()
    {
        return ServiceManager::getServiceManager();
    }
}