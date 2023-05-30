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
 * Copyright (c) 2020 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */

declare(strict_types=1);

namespace oat\oatbox\extension;

use common_ext_ExtensionException;

/**
 * Class ComposerInfo
 * @package oat\oatbox\extension
 */
class ComposerInfo
{
    private static $jsons = [];
    private static $locks = [];
    private static $availablePackages;

    /** @var null|string  */
    private $rootDir;

    private const COMPOSER_JSON = 'composer.json';
    private const COMPOSER_LOCK = 'composer.lock';
    private const COMPOSER_LOCK_PACKAGES = 'packages';
    private const COMPOSER_LOCK_EXTRA = 'extra';
    private const COMPOSER_LOCK_EXTENSION_NAME = 'tao-extension-name';
    private const COMPOSER_LOCK_PACKAGE_NAME = 'name';

    /**
     * ComposerInfo constructor.
     * @param string|null $rootDir directory where composer file located
     * @throws common_ext_ExtensionException
     */
    public function __construct(string $rootDir = null)
    {
        if ($rootDir === null) {
            $this->rootDir = defined('ROOT_PATH') ? ROOT_PATH : realpath(__DIR__ . '/../../../../');
        } else {
            $this->rootDir = $rootDir;
        }
        $composerJsonPath = realpath($this->rootDir) . DIRECTORY_SEPARATOR . self::COMPOSER_JSON;

        if (!file_exists($composerJsonPath)) {
            throw new common_ext_ExtensionException(sprintf('Composer file missed at %s', $this->rootDir));
        }
    }

    /**
     * @return array
     * @throws common_ext_ExtensionException
     */
    public function getAvailableTaoExtensions(): array
    {
        if (self::$availablePackages !== null) {
            return self::$availablePackages;
        }

        self::$availablePackages = [];
        $composerLock = $this->getComposerLock();

        $extensionPackages = array_filter($composerLock[self::COMPOSER_LOCK_PACKAGES], function ($package) {
            return isset($package[self::COMPOSER_LOCK_EXTRA][self::COMPOSER_LOCK_EXTENSION_NAME]);
        });
        foreach ($extensionPackages as $package) {
            $extId = $package[self::COMPOSER_LOCK_EXTRA][self::COMPOSER_LOCK_EXTENSION_NAME];
            self::$availablePackages[$package[self::COMPOSER_LOCK_PACKAGE_NAME]] = $extId;
        }

        return self::$availablePackages;
    }

    /**
     * Get dependant tao extensions
     * @return array
     * @throws common_ext_ExtensionException
     */
    public function extractExtensionDependencies()
    {
        $result = [];
        $availableTaoExtensions = $this->getAvailableTaoExtensions();
        $composerJson = $this->getComposerJson();
        foreach ($composerJson['require'] as $packageId => $packageVersion) {
            if (isset($availableTaoExtensions[$packageId])) {
                $result[$availableTaoExtensions[$packageId]] = $packageVersion;
            }
        }
        return $result;
    }

    /**
     * @return string
     */
    public function getPackageId(): string
    {
        return $this->getComposerJson()['name'];
    }

    /**
     * @param $path
     * @return mixed
     */
    private function getEncodedFileContent($path)
    {
        $content = file_get_contents($path);
        return json_decode($content, true);
    }

    /**
     * @return array
     */
    private function getComposerJson(): array
    {
        if (!isset(self::$jsons[$this->rootDir])) {
            $file = realpath($this->rootDir) . DIRECTORY_SEPARATOR . self::COMPOSER_JSON;
            self::$jsons[$this->rootDir] = $this->getEncodedFileContent($file);
        }
        return self::$jsons[$this->rootDir];
    }


    /**
     * @return array
     * @throws common_ext_ExtensionException
     */
    private function getComposerLock(): array
    {
        if (isset(self::$locks[$this->rootDir])) {
            return self::$locks[$this->rootDir];
        }

        $composerLockPath = realpath($this->rootDir) . DIRECTORY_SEPARATOR . self::COMPOSER_LOCK;
        if (!file_exists($composerLockPath)) {
            $composerLockPath = rtrim($this->getTaoRoot(), DIRECTORY_SEPARATOR) .
                DIRECTORY_SEPARATOR . self::COMPOSER_LOCK;
        }

        if (!file_exists($composerLockPath)) {
            throw new common_ext_ExtensionException(sprintf('Composer lock file missed at %s', $composerLockPath));
        }

        self::$locks[$this->rootDir] = $this->getEncodedFileContent($composerLockPath);

        return self::$locks[$this->rootDir];
    }

    /**
     * @return false|string
     */
    private function getTaoRoot(): string
    {
        return defined('ROOT_PATH') ? ROOT_PATH : realpath(__DIR__ . '/../../../../');
    }
}
