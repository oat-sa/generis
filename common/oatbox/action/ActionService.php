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
use Zend\ServiceManager\ServiceLocatorAwareInterface;

class ActionService extends ConfigurableService
{
    const SERVICE_ID = 'generis/actionService';
    
    static $blackList = [
        '\\oatbox\\composer\\ExtensionInstaller',
        '\\oatbox\\composer\\ExtensionInstallerPlugin',
    ];
    
    /**
     * 
     * @param string $actionIdentifier
     * @return Action
     */
    public function resolve($actionIdentifier)
    {
        $action = $this->getActionInstance($actionIdentifier);
        return $action;
    }


    /**
     * @param string $extId
     * @return array
     * @throws \common_ext_ExtensionException
     */
    public function getAvailableActions($extId = '')
    {
        $cacheKey = __FUNCTION__ . $extId;
        $extManager = \common_ext_ExtensionsManager::singleton();
        if ($this->getCache()->has($cacheKey)) {
            $actions = $this->getCache()->get($cacheKey);
        } else {
            $actions = array();
            if ($extId !== '' && $extManager->isInstalled($extId)) {
                $extensions = [$extId => $extManager->getExtensionById($extId)];
            } else {
                $extensions = $extManager->getInstalledExtensions();
            }

            foreach ($extensions as $ext) {
                $actions = array_merge($actions, $this->getActionsInDirectory($ext->getDir()));
            }
            $actions = array_merge($actions, $this->getActionsInDirectory(VENDOR_PATH.'oat-sa'));
            $this->getCache()->put($actions, $cacheKey);
        }
        return $actions;
    }

    /**
     * @param string $actionIdentifier
     * @return Action
     * @throws ResolutionException
     */
    protected function getActionInstance($actionIdentifier)
    {
        $action = null;

        if ($this->getServiceLocator()->has($actionIdentifier)) {
            $action = $this->getServiceManager()->get($actionIdentifier);
        } elseif (class_exists($actionIdentifier) && is_subclass_of($actionIdentifier, '\oat\oatbox\action\Action')) {
            $action = new $actionIdentifier();
            if ($action instanceof ServiceLocatorAwareInterface) {
                $action->setServiceLocator($this->getServiceLocator());
            }
        } else {
            throw new ResolutionException('Unknown action '.$actionIdentifier);
        }

        return $action;
    }

    /**
     * @return \common_cache_Cache
     */
    protected function getCache()
    {
        return $this->getServiceManager()->get('generis/cache');
    }

    /**
     * Get list of actions
     * @param $dir
     * @return array list of class names
     */
    protected function getActionsInDirectory($dir)
    {
        $classNames = array();
        $filter = $this->hasOption('filterDirs') ? $this->getOption('filterDirs') : [];
        $recIt = new \RecursiveIteratorIterator(
            new \RecursiveCallbackFilterIterator(
                new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS),
                function ($fileInfo, $key, $iterator) use ($filter) {
                    return $fileInfo->isFile() || !in_array($fileInfo->getBaseName(), $filter);
                }
            )
        );
        $regexIt = new \RegexIterator($recIt, '/^.+\.php$/i', \RecursiveRegexIterator::GET_MATCH);
        foreach ($regexIt as $entry) {
            $info = \helpers_PhpTools::getClassInfo($entry[0]);
            $fullname = empty($info['ns'])
            ? $info['class']
            : $info['ns'].'\\'.$info['class'];
            $reflectionClass = new \ReflectionClass(Action::class);
            if (is_subclass_of($fullname, Action::class) && !in_array($fullname, self::$blackList) && $reflectionClass->IsInstantiable()) {
                $classNames[] = $fullname;
            }
        }
        return $classNames;
    }
}