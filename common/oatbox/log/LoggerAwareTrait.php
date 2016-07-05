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
/**
 * Trait for classes that want to use the Logger
 * 
 * @author Joel Bout <joel@taotesting.com>
 */
trait LoggerAwareTrait
{
    /**
     * @var LoggerInterface
     */
    private $logger;
    
    /**
     * 
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
    
    /**
     * 
     * @return \Psr\Log\LoggerInterface
     */
    public function getLogger()
    {
        if (is_null($this->logger)) {
            $this->logger = new \common_log_Logger2Psr(\common_Logger::singleton());
        }
        return $this->logger;
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