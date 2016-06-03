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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */
namespace oat\oatbox\action;

use oat\oatbox\service\ConfigurableService;
use oat\oatbox\service\ServiceNotFoundException;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

class ActionService extends ConfigurableService
{
    const SERVICE_ID = 'generis/actionService';
    
    static $blackList = array('\\oatbox\\composer\\ExtensionInstaller','\\oatbox\\composer\\ExtensionInstallerPlugin');
    
    /**
     * 
     * @param string $actionIdentifier
     * @return Action
     */
    public function resolve($actionIdentifier)
    {
        $action = null;
        if ($this->getServiceLocator()->has($actionIdentifier)) {
            $action = $this->getServiceManager()->get($actionIdentifier);
        } elseif (class_exists($actionIdentifier) && is_subclass_of($actionIdentifier, Action::class)) {
            $action = new $actionIdentifier();
            if ($action instanceof ServiceLocatorAwareInterface) {
                $action->setServiceLocator($this->getServiceLocator());
            }
        } else {
            throw new ResolutionException('Unknown action '.$actionIdentifier);
        }
        return $action;
    }
    
    public function getAvailableActions()
    {
        if ($this->getCache()->has(__FUNCTION__)) {
            $actions = $this->getCache()->get(__FUNCTION__);
        } else {
            $actions = array();
            foreach (\common_ext_ExtensionsManager::singleton()->getInstalledExtensions() as $ext) {
                $actions = array_merge($actions, $this->getActionsInDirectory($ext->getDir()));
            }
            $actions = array_merge($actions, $this->getActionsInDirectory(VENDOR_PATH.'oat-sa'));
            $this->getCache()->put($actions, __FUNCTION__);
        }
        return $actions;
    }
    
    /**
     * @return \common_cache_Cache
     */
    protected function getCache()
    {
        return $this->getServiceManager()->get('generis/cache');
    }
    
    protected function getActionsInDirectory($dir)
    {
        $classNames = array();
        $recIt = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir));
        $regexIt = new \RegexIterator($recIt, '/^.+\.php$/i', \RecursiveRegexIterator::GET_MATCH);
        foreach ($regexIt as $entry) {
            $info = \helpers_PhpTools::getClassInfo($entry[0]);
            $fullname = empty($info['ns'])
            ? $info['class']
            : $info['ns'].'\\'.$info['class'];
            if (!in_array($fullname, self::$blackList) && is_subclass_of($fullname, Action::class)) {
                $classNames[] = $fullname;
            }
        }
        return $classNames;
    }
}