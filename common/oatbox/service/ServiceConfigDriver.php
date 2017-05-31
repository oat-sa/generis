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
 * Copyright (c) 2017 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */
namespace oat\oatbox\service;

use common_Utils;
use oat\oatbox\config\ConfigurationDriver;

/**
 * Class ServiceConfigDriver
 *
 * Driver dedicated to store only ConfigurableService into config
 *
 * @package oat\oatbox\service
 */
class ServiceConfigDriver extends \common_persistence_PhpFileDriver implements ConfigurationDriver
{
    /**
     * Get the config content associated to given $key
     * $key has to be a configurable service
     *
     * @param string $key
     * @param mixed $value
     * @return null|string
     */
    protected function getContent($key, $value)
    {
        if (! $value instanceof ConfigurableService) {
            return null;
        }
        $content = $value->getHeader() . PHP_EOL . "return " . common_Utils::toHumanReadablePhpString($value) . ";" . PHP_EOL;
        return $content;
    }

    /**
     * Get the path associated to the given key
     * Must be a two part key (e.q. path into a config folder)
     *
     * @param string $key
     * @return string
     */
    protected function getPath($key)
    {
        $parts = explode('/', $key);
        $path = substr(parent::getPath(array_shift($parts)), 0, -4);
        foreach ($parts as $part) {
            $path .= DIRECTORY_SEPARATOR . $this->sanitizeReadableFileName($part);
        }
        return $path.'.conf.php';
    }
}