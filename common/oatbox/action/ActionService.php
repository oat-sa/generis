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
 * Copyright (c) 2015-2022 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */

declare(strict_types=1);

namespace oat\oatbox\action;

use common_ext_Extension;
use helpers_PhpTools;
use oat\oatbox\service\ConfigurableService;
use oat\oatbox\cache\SimpleCache;
use common_ext_ExtensionsManager;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RecursiveRegexIterator;
use ReflectionClass;
use RegexIterator;
use Throwable;

class ActionService extends ConfigurableService
{
    const SERVICE_ID = 'generis/actionService';

    static $blackList = ['\\oatbox\\composer\\ExtensionInstaller','\\oatbox\\composer\\ExtensionInstallerPlugin'];

    /**
     * @throws ResolutionException
     */
    public function resolve($actionIdentifier): Action
    {
        if ($this->getServiceLocator()->has($actionIdentifier)) {
            $action = $this->getServiceLocator()->get($actionIdentifier);
        } elseif (class_exists($actionIdentifier) && is_subclass_of($actionIdentifier, Action::class)) {
            $action = new $actionIdentifier();
            $this->propagate($action);
        } else {
            throw new ResolutionException('Unknown action ' . $actionIdentifier);
        }

        return $action;
    }

    public function getAvailableActions(): array
    {
        $actions = $this->getCache()->get(__FUNCTION__);
        if (is_null($actions)) {
            $actions = [];
            foreach ($this->getServiceLocator()->get(common_ext_ExtensionsManager::SERVICE_ID)->getInstalledExtensions() as $ext) {
                $actions = array_merge($actions, $this->getActionsInDirectory($ext->getDir()));
            }
            $actions = array_merge($actions, $this->getActionsInDirectory(VENDOR_PATH . 'oat-sa'));
            $this->getCache()->set(__FUNCTION__, $actions);
        }
        return $actions;
    }

    /**
     * @throws ResolutionException
     */
    public function getActionExtension(Action $action): common_ext_Extension
    {
        $reflectionClass = new ReflectionClass($action);
        $namespace = $reflectionClass->getNamespaceName();
        $extensionId = explode('\\', $namespace)[1] ?? null;

        try {
            return $this->getServiceLocator()
                ->get(common_ext_ExtensionsManager::SERVICE_ID)
                ->getExtensionById($extensionId);
        } catch (Throwable $exception) {
            throw new ResolutionException(sprintf('Cannot get extension of action `%s`', $namespace));
        }
    }

    public function getActionName(Action $action): string
    {
        return (new ReflectionClass($action))->getName();
    }

    protected function getCache(): SimpleCache
    {
        return $this->getServiceLocator()->get(SimpleCache::SERVICE_ID);
    }

    protected function getActionsInDirectory(string $dir): array
    {
        $classNames = [];
        $recIt = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
        $regexIt = new RegexIterator($recIt, '/^.+\.php$/i', RecursiveRegexIterator::GET_MATCH);
        foreach ($regexIt as $entry) {
            $info = helpers_PhpTools::getClassInfo($entry[0]);
            $fullname = empty($info['ns'])
            ? $info['class']
            : $info['ns'] . '\\' . $info['class'];
            if (!in_array($fullname, self::$blackList) && is_subclass_of($fullname, Action::class)) {
                $classNames[] = $fullname;
            }
        }
        return $classNames;
    }
}
