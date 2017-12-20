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
 * Copyright (c) 2017 (original work) Open Assessment Technologies SA
 *
 */

namespace oat\oatbox\log\logger;

use Monolog\Formatter\FormatterInterface;
use Monolog\Handler\HandlerInterface;
use Monolog\Logger;
use oat\oatbox\Configurable;
use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

class TaoMonolog extends Configurable implements LoggerInterface
{
    use ServiceLocatorAwareTrait;
    use LoggerTrait;

    const HANDLERS_OPTION = 'handlers';

    /** @var Logger null  */
    protected $logger = null;

    public function log($level, $message, array $context = array())
    {
        if (is_null($this->logger)) {
            $this->logger = $this->buildLogger();
        }

        $this->logger->log($level, $message, $context);
    }

    protected function buildLogger()
    {
        $logger = new Logger($this->getOption('name'));

        if ($this->hasOption(self::HANDLERS_OPTION)) {
            foreach ($this->getOption(self::HANDLERS_OPTION) as $handlerOptions) {
                $logger->pushHandler($this->buildHandler($handlerOptions));
            }
        }

        return $logger;
    }

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

        if (isset($handlerOptions['processors'])) {
            $processorsOptions = $handlerOptions['processors'];
            if (!is_array($processorsOptions)) {
                throw new \common_configuration_ComponentFactoryException('Handler processors options as to be formatted as array');
            }

            foreach ($processorsOptions as $processorsOption) {
                $handler->pushProcessor($this->buildProcessor($processorsOption));
            }
        }

        if (isset($handlerOptions['formatter'])) {
            $handler->setFormatter($this->buildFormatter($processorsOption));
        }

        return $handler;
    }

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
            if (!is_a($options['class'], FormatterInterface::class)) {
                throw new \common_configuration_ComponentFactoryException('Formatter class option has to be a FormatterInterface.');
            }

            $formatterOptions = [];
            if (isset($options['options'])) {
                $formatterOptions = is_array($options['options']) ? $options['options'] : [$options['options']];
            }

            return $this->buildObject($options['class'], $formatterOptions);
        }
    }

    protected function buildObject($className, array $args)
    {
        $class = new \ReflectionClass($className);
        return $class->newInstanceArgs($args);
    }
}