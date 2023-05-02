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
 * Copyright (c) 2017 (original work) Open Assessment Technologies SA;
 */

class common_persistence_InMemoryAdvKvDriver extends common_persistence_InMemoryKvDriver implements common_persistence_AdvKvDriver
{
    public const HPREFIX = 'hPrfx_';

    /**
     * @see common_persistence_Driver::connect()
     *
     * @param mixed $id
     */
    public function connect($id, array $params)
    {
        return new \common_persistence_AdvKeyValuePersistence($params, $this);
    }

    public function hmSet($key, $fields)
    {
        if (!is_array($fields)) {
            return false;
        }

        foreach ($fields as $hashkey => $value) {
            $this->persistence[$key][self::HPREFIX . $hashkey] = $value;
        }

        return true;
    }

    public function hExists($key, $field)
    {
        return $this->hGet($key, $field) !== false;
    }

    public function hSet($key, $field, $value)
    {
        $result = !isset($this->persistence[$key][self::HPREFIX . $field]);
        $this->persistence[$key][self::HPREFIX . $field] = $value;

        return $result;
    }

    public function hGet($key, $field)
    {
        if (
            ! isset($this->persistence[$key])
            || ! isset($this->persistence[$key][self::HPREFIX . $field])
        ) {
            return false;
        }

        return $this->persistence[$key][self::HPREFIX . $field];
    }

    public function hDel($key, $field): bool
    {
        if (!isset($this->persistence[$key]) || !isset($this->persistence[$key][self::HPREFIX . $field])) {
            return false;
        }
        unset($this->persistence[$key][self::HPREFIX . $field]);

        return true;
    }

    public function hGetAll($key)
    {
        if (! isset($this->persistence[$key])) {
            return [];
        }
        $data = [];
        $prefixLength = strlen(self::HPREFIX);

        foreach ($this->persistence[$key] as $hash => $attributes) {
            if (mb_substr($hash, 0, $prefixLength) === self::HPREFIX) {
                $data[mb_substr($hash, $prefixLength)] = $this->persistence[$key][$hash];
            }
        }

        return $data;
    }

    public function keys($pattern)
    {
        if ($pattern == '*') {
            return array_keys($this->persistence);
        }

        return [];
    }
}
