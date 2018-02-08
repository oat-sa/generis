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

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use oat\oatbox\service\ServiceManager;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

/**
 * Trait for classes that want to use the Logger
 *
 * @author Joel Bout <joel@taotesting.com>
 */
trait LoggerAwareTrait
{
    private $logger;

    /**
     * Set a new logger
     *
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Get the logger based on service manager
     *
     * @return \Psr\Log\LoggerInterface
     */
    public function getLogger()
    {
        if ($this->logger instanceof LoggerInterface) {
            return $this->logger;
        }
        if ($this instanceof ServiceLocatorAwareInterface) {
            $logger = $this->getServiceLocator()->get(LoggerService::SERVICE_ID);
        } else {
            $logger = ServiceManager::getServiceManager()->get(LoggerService::SERVICE_ID);
        }
        return ($logger instanceof LoggerInterface) ? $logger : new NullLogger();
    }

    // Helpers
    
    /**
     * Logs an emergency
     * 
     * @param string $message
     * @param array $context
     */
    public function logEmergency($message, $context = array())
    {
        $this->getLogger()->emergency($message, $context);
    }
    
    public function logAlert($message, $context = array())
    {
        $this->getLogger()->alert($message, $context);
    }
    
    public function logCritical($message, $context = array())
    {
        $this->getLogger()->critical($message, $context);
    }
    
    public function logError($message, $context = array())
    {
        $this->getLogger()->error($message, $context);
    }
    
    public function logWarning($message, $context = array())
    {
        $this->getLogger()->warning($message, $context);
    }
    
    public function logNotice($message, $context = array())
    {
        $this->getLogger()->notice($message, $context);
    }
    
    public function logInfo($message, $context = array())
    {
        $this->getLogger()->info($message, $context);
    }
    
    public function logDebug($message, $context = array())
    {
        $this->getLogger()->debug($message, $context);
    }
}