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
 * Copyright (c) 2019  (original work) Open Assessment Technologies SA;
 *
 * @author Oleksandr Zagovorychev <zagovorichev@gmail.com>
 */

namespace oat\oatbox\service;


class ExtendedConfigDriver extends ServiceConfigDriver
{
    const LOCAL_CONFIG_PREFIX = 'local';

    /**
     * Local configuration with high priority which overrides configuration for the service
     * @var array
     */
    private $localConfig;

    public function get($id)
    {
        $entry = parent::get($id);
        return $this->mergeLocalConfig($id, $entry);
    }

    private function mergeLocalConfig($id, $entry)
    {
        $opts = self::array_merge_recursive_ex($entry->getOptions(), $this->getLocalConfig($id));
        $entry->setOptions($opts);
        return $entry;
    }

    private static function array_merge_recursive_ex(array $array1, array $array2)
    {
        $merged = $array1;

        foreach ($array2 as $key => & $value) {
            if (is_array($value) && isset($merged[$key]) && is_array($merged[$key])) {
                $merged[$key] = static::array_merge_recursive_ex($merged[$key], $value);
            } else if (is_numeric($key)) {
                if (!in_array($value, $merged, 1)) {
                    $merged[] = $value;
                }
            } else {
                $merged[$key] = $value;
            }
        }

        return $merged;
    }

    private function getLocalConfig($id)
    {
        $localConfig = $this->loadLocalConfig();
        $path = explode('/', $id);
        $conf = $localConfig;
        foreach ($path as $key) {
            if (array_key_exists($key, $conf)) {
                $conf = $conf[$key];
            } else {
                $conf = [];
                break;
            }
        }
        return $conf;
    }

    /**
     *
     */
    /**
     * @return array|mixed
     */
    private function loadLocalConfig()
    {
        if (!$this->localConfig) {
            $this->localConfig = [];
            $config = str_replace('.php', '.json', $this->getPath(self::LOCAL_CONFIG_PREFIX));
            // extended configuration for the local service
            if (is_readable($config)) {
                $this->localConfig = @json_decode(@file_get_contents($config), 1);
            }
        }
        return $this->localConfig;
    }


}
