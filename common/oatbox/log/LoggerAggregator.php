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
 * Copyright (c) 2016 (original work) Open Assessment Technologies SA
 * 
 */

namespace oat\oatbox\log;

use oat\oatbox\Configurable;
use oat\oatbox\service\ConfigurableService;
use Psr\Log\LoggerInterface;
use Psr\Log\AbstractLogger;
use Psr\Log\LoggerTrait;

/**
 * An aggregator that broadcast logs to multiple loggers
 * 
 * @author Joel Bout <joel@taotesting.com>
 */
class LoggerAggregator extends ConfigurableService implements LoggerInterface
{
    use LoggerTrait;

    /**
     * @var LoggerInterface[]
     */
    private $loggers;

    /**
     * Instantiate the aggregator.
     *
     * @param LoggerInterface[] $options
     * @throws \common_Exception If one of logger isnot a Psr3 logger
     */
    public function __construct($options = array())
    {
        parent::__construct($options);

        foreach ($this->getOptions() as $logger) {
            if (!$logger instanceof LoggerInterface) {
                throw new \common_Exception('Non PSR-3 compatible logger ' . get_class($logger) . ' added to '.__CLASS__);
            }
        }

        $this->loggers = $this->getOptions();
    }
    
    /**
     * (non-PHPdoc)
     * @see \Psr\Log\LoggerInterface::log()
     */
    public function log($level, $message, array $context = array())
    {
         foreach ($this->loggers as $logger) {
             $logger->log($level, $message, $context);
         }
    }
}