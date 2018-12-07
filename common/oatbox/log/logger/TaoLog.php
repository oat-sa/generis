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

use oat\oatbox\service\ConfigurableService;
use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;

/**
 * Class TaoLog
 *
 * Tao internal appenders to store logs
 *
 * @package oat\oatbox\log\logger
 * @deprecated This wrapper is for backward compatibility purpose, use TaoMonolog for newer logger
 */
class TaoLog extends ConfigurableService implements LoggerInterface
{
    use LoggerTrait;

    const OPTION_APPENDERS = 'appenders';

    /** @var \common_log_Dispatcher */
    private $dispatcher = null;
    

    /**
     * @param mixed $level
     * @param string $message
     * @param array $context
     * @throws \common_configuration_ComponentFactoryException
     */
    public function log($level, $message, array $context = array())
    {
        $stack = defined('DEBUG_BACKTRACE_IGNORE_ARGS')
                ? debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS)
                : debug_backtrace(false);
		array_shift($stack);
		// retrieving the user can be a complex procedure, leading to missing log informations
		$user = null;
		$keys = array_keys($stack);
		$current = isset($keys[2]) ? $stack[$keys[2]] : $stack[end($keys)];
        $current = array_merge($current, $context);
        if (isset($current['file']) && isset($current['line'])) {
            $errorFile = $current['file'];
            $errorLine = $current['line'];
        } elseif (isset($current['class']) && isset($current['function'])) {
            $errorFile = $current['class'];
            $errorLine = $current['function'];
        } else {
            $errorFile = $errorLine = 'undefined';
        }
		if(PHP_SAPI != 'cli'){
			$requestURI = $_SERVER['REQUEST_URI'];
		} else {
			$requestURI = implode(' ', $_SERVER['argv']);
		}
		
		//reformat input
		if(is_object($message)){
			$message = 'Message is object of type ' . gettype($message);

            //show content of logged object only from debug level
            if($level <= \common_Logger::DEBUG_LEVEL){
                $message .= ' : ' . PHP_EOL . var_export($message, true);
            }
        //same for arrays
	    } else if (is_array($message) && $level <= \common_Logger::DEBUG_LEVEL){
			$message = 'Message is an array : ' . PHP_EOL . var_export($message, true);
		} else{
			$message = (string) $message;
		}
		$level = \common_log_Logger2Psr::getCommonFromPsrLevel($level);
		$this->getDispatcher()->log(new \common_log_Item($message, $level, time(), $stack, $context, $requestURI, $errorFile, $errorLine));
    }
    
    /**
     * Returns the dispatcher
     *
     * @return Appender
     */
    private function getDispatcher() {
        if (is_null($this->dispatcher)) {
            $this->dispatcher = new \common_log_Dispatcher($this->getOption(self::OPTION_APPENDERS));
        }
        return $this->dispatcher;
    }


}