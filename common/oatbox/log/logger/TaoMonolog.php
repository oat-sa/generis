<?php
/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2018 (original work) Open Assessment Technologies SA
 *
 */

namespace oat\oatbox\log\logger;

use Monolog\Formatter\FormatterInterface;
use Monolog\Handler\HandlerInterface;
use Monolog\Logger;
use oat\oatbox\service\ConfigurableService;
use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;

/**
 * Class TaoMonolog
 *
 * A wrapper to acces monolog from tao platform
 * Build the logger from configuration with handlers
 * - see generis/config/header/log.conf.php
 *
 * @package oat\oatbox\log\logger
 */
class TaoMonolog extends ConfigurableService implements LoggerInterface
{
    use LoggerTrait;

    const HANDLERS_OPTION = 'handlers';

    /** @var Logger null  */
    protected $logger = null;

    /**
     * @param mixed $level
     * @param string $message
     * @param array $context
     * @throws \common_configuration_ComponentFactoryException
     */
    public function log($level, $message, array $context = array())
    {
        if (is_null($this->logger)) {
            $this->logger = $this->buildLogger();
        }

        $this->logger->log($level, $message, $context);
    }

    /**
     * @return Logger
     * @throws \common_configuration_ComponentFactoryException
     */
    protected function buildLogger()
    {
        $logger = new Logger($this->getOption('name'));

        if ($this->hasOption(self::HANDLERS_OPTION)) {
            foreach ($this->getOption(self::HANDLERS_OPTION) as $handlerOptions) {
                $logger->pushHandler($this->buildHandler($handlerOptions));
            }
        }

        if ($this->hasOption('processors')) {
            $processorsOptions = $this->getOption('processors');
            if (!is_array($processorsOptions)) {
                throw new \common_configuration_ComponentFactoryException('Handler processors options as to be formatted as array');
            }

            foreach ($processorsOptions as $processorsOption) {
                $logger->pushProcessor($this->buildProcessor($processorsOption));
            }
        }

        return $logger;
    }

    /**
     * @param array $options
     * @return HandlerInterface
     * @throws \common_configuration_ComponentFactoryException
     */
    protected function buildHandler(array $options)
    {
        if (!isset($options['class'])) {
            throw new \common_configuration_ComponentFactoryException('Handler options has to contain a class attribute.');
        }

        if (!is_a($options['class'], HandlerInterface::class, true)) {
            throw new \common_configuration_ComponentFactoryException('Handler class option has to be a HandlerInterface.');
        }

        $handlerOptions = [];
        if (isset($options['options'])) {
            $handlerOptions = is_array($options['options']) ? $options['options'] : [$options['options']];
        }
        /** @var HandlerInterface $handler */
        $handler = $this->buildObject($options['class'], $handlerOptions);

        if (isset($options['processors'])) {
            $processorsOptions = $options['processors'];
            if (!is_array($processorsOptions)) {
                throw new \common_configuration_ComponentFactoryException('Handler processors options as to be formatted as array');
            }

            foreach ($processorsOptions as $processorsOption) {
                $handler->pushProcessor($this->buildProcessor($processorsOption));
            }
        }

        if (isset($options['formatter'])) {
            $handler->setFormatter($this->buildFormatter($options['formatter']));
        }

        return $handler;
    }

    /**
     * @param $options
     * @return callable
     * @throws \common_configuration_ComponentFactoryException
     */
    protected function buildProcessor($options)
    {
        if (is_object($options)) {
            return $options;
        } else {
            if (!isset($options['class'])) {
                throw new \common_configuration_ComponentFactoryException('Processor options has to contain a class attribute.');
            }

            $processorOptions = [];
            if (isset($options['options'])) {
                $processorOptions = is_array($options['options']) ? $options['options'] : [$options['options']];
            }

            return $this->buildObject($options['class'], $processorOptions);
        }
    }

    /**
     * @param $options
     * @return FormatterInterface
     * @throws \common_configuration_ComponentFactoryException
     */
    protected function buildFormatter($options)
    {
        if (is_object($options)) {
            if (!is_a($options, FormatterInterface::class)) {
                throw new \common_configuration_ComponentFactoryException('Formatter has to be a FormatterInterface.');
            }
            return $options;
        } else {
            if (!isset($options['class'])) {
                throw new \common_configuration_ComponentFactoryException('Formatter options has to contain a class attribute.');
            }

            if (!is_a($options['class'], FormatterInterface::class, true)) {
                throw new \common_configuration_ComponentFactoryException('Formatter class option has to be a FormatterInterface.');
            }

            $formatterOptions = [];
            if (isset($options['options'])) {
                $formatterOptions = is_array($options['options']) ? $options['options'] : [$options['options']];
            }

            return $this->buildObject($options['class'], $formatterOptions);
        }
    }

    /**
     * @param $className
     * @param array $args
     * @return object
     * @throws \common_configuration_ComponentFactoryException
     */
    protected function buildObject($className, array $args)
    {
        try {
            $class = new \ReflectionClass($className);
            return $class->newInstanceArgs($args);
        } catch (\ReflectionException $e) {
            throw new \common_configuration_ComponentFactoryException('Unable to create object for logger', 0, $e);
        }
    }
}