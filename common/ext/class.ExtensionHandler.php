<?php
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use oat\oatbox\service\ServiceManager;
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
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */

/**
 * EXtension Wrapper
 *
 * @abstract
 * @access public
 * @author lionel.lecaque@tudor.lu
 * @package generis
 * @see @license  GNU General Public (GPL) Version 2 http://www.opensource.org/licenses/gpl-2.0.php
 
 */
abstract class common_ext_ExtensionHandler
{
    // Adding container and logger.
    use \oat\oatbox\log\ContainerLoggerTrait;

    /**
     * @var common_ext_Extension
     */
    public $extension = null;


    /**
     * Initialise the extension handler
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  common_ext_Extension $extension
     */
    public function __construct( common_ext_Extension $extension)
    {
		$this->extension = $extension;
    }
    
    /**
     * @return common_ext_Extension
     */
    protected function getExtension()
    {
        return $this->extension;
    }
    
    /**
     * Run Extension Script
     *
     * @param string $script
     * @param array $arguments (optional)
     * @throws common_ext_InstallationException
     */
    protected function runExtensionScript($script, array $arguments = [])
    {
        $this->log('d', 'Running custom extension script ' . $script . ' for extension ' . $this->getExtension()->getId(), 'INSTALL');
        if (file_exists($script)) {
            require_once $script;
        } elseif (class_exists($script) && is_subclass_of($script, \oat\oatbox\action\Action::class)) {
            $action = new $script();

            if ($action instanceof ServiceLocatorAwareInterface) {
                $action->setServiceLocator($this->getServiceManager());
            }

            call_user_func($action, $arguments);
        } else {
            $error = new common_ext_InstallationException('Unable to run install script ' . $script);
            $error->setExtensionId($this->getExtension()->getId());

            throw $error;
        }
    }

    protected function getServiceManager()
    {
        return ServiceManager::getServiceManager();
    }

    /**
     * Log message
     *
     * @see common_Logger class
     *
     * @param string $logLevel
     * <ul>
     *   <li>'w' - warning</li>
     *   <li>'t' - trace</li>
     *   <li>'d' - debug</li>
     *   <li>'i' - info</li>
     *   <li>'e' - error</li>
     *   <li>'f' - fatal</li>
     * </ul>
     * @param string $message
     * @param array $tags
     */
    public function log($logLevel, $message, $tags = array())
    {
        if ($this->getLogger() instanceof \Psr\Log\LoggerInterface) {
            $this->getLogger()->log(
                common_log_Logger2Psr::getPsrLevelFromCommon($logLevel),
                $message
            );
        }
        if (method_exists('common_Logger', $logLevel)) {
            call_user_func('common_Logger::' . $logLevel, $message, $tags);
        }
    }
}