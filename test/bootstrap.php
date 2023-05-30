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
 * @license    GPLv2
 * @package    package_name
 * @subpackage
 *
 * phpcs:disable PSR1.Files.SideEffects
 */

$extensionRoot = realpath(__DIR__ . '/../');

function generisInstalledAsExtension(string $extensionRoot)
{
    return is_dir($extensionRoot . '/../vendor') && is_dir($extensionRoot . '/../generis');
}

function generisInstalledAsRootPackage(string $extensionRoot)
{
    return is_dir($extensionRoot . '/vendor');
}

if (generisInstalledAsExtension($extensionRoot)) {
    define('ROOT_PATH', realpath(__DIR__ . '/../../'));
    require_once $extensionRoot . '/../vendor/autoload.php';
} elseif (generisInstalledAsRootPackage($extensionRoot)) {
    define('ROOT_PATH', realpath(__DIR__ . '/../'));
    require_once $extensionRoot . '/vendor/autoload.php';
} else {
    throw new \Exception('Vendor directory not found');
}

\common_Config::load($extensionRoot . '/test/config/generis.conf.php');
