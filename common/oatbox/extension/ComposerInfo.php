<?php

declare(strict_types=1);

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

namespace oat\oatbox\extension;

use oat\oatbox\extension\exception\ManifestException;

/**
 * Class ComposerInfo
 * @package oat\oatbox\extension
 */
class ComposerInfo
{
    private static $jsons = [];
    private static $locks = [];

    const COMPOSER_JSON = 'composer.json';
    const COMPOSER_LOCK = 'composer.lock';
    const COMPOSER_LOCK_PACKAGES = 'packages';

    /**
     * @param $folder
     * @return array
     * @throws ManifestException
     */
    public function getComposerJson($folder): array
    {
        if (!isset(self::$jsons[$folder])) {
            $file = realpath($folder).DIRECTORY_SEPARATOR.self::COMPOSER_JSON;
            if (!file_exists($file)) {
                throw new ManifestException($file.' file not found');
            }
            $content = file_get_contents($file);
            self::$jsons[$folder] = json_decode($content, true);
        }
        return self::$jsons[$folder];
    }

    /**
     * @param $folder
     * @return array
     * @throws ManifestException
     */
    public function getComposerLock($folder): array
    {
        if (!isset(self::$locks[$folder])) {
            $file = realpath($folder).DIRECTORY_SEPARATOR.self::COMPOSER_LOCK;
            if (!file_exists($file)) {
                throw new ManifestException($file . ' file not found');
            }
            $content = file_get_contents($file);
            self::$locks[$folder] = json_decode($content, true);
        }
        return self::$locks[$folder];
    }

    /**
     * @param $packageId
     * @param string $folder
     * @return array
     * @throws ManifestException
     */
    public function getPackageInfo($packageId, $folder = ROOT_PATH): array
    {
        $composerLock = $this->getComposerLock($folder);
        foreach ($composerLock[self::COMPOSER_LOCK_PACKAGES] as $package) {
            if ($package['name'] === $packageId) {
                return $package;
            }
        }
        throw new ManifestException(sprintf(
            'Package "%s" not found in %s',
            $packageId,
            $folder.DIRECTORY_SEPARATOR.self::COMPOSER_LOCK
        ));
    }
}