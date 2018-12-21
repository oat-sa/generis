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
 * Copyright (c) 2013-2017 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 * @author Patrick Plichart <patrick@taotesting.com>
 * @author Camille Moyon  <camille@taotesting.com>
 * @license GPLv2
 * @package generis

 *
 */
class common_persistence_AdvKeyValuePersistence extends common_persistence_KeyValuePersistence
{
    /**
     * Set all $fields of a $key
     * If one of field values is large, a map is created into storage, and reference map is the new value of the field/
     *
     * @param $key
     * @param $fields
     * @return bool
     */
    public function hmSet($key, $fields)
    {
        if ($this->hasMaxSize()) {
            foreach ($fields as $field => $value) {
                try {
                    if ($this->isLarge($value)) {
                        $fields[$field] = $this->setLargeValue($this->getMappedKey($key, $field), $value, 0, false);
                    }
                } catch (common_Exception $e) {
                    common_Logger::w('Max size value is misconfigured: ' . $e->getMessage());
                }
            }
        }

        return $this->getDriver()->hmSet($key, $fields);
    }

    /**
     * Check if a $field exists for a given $key.
     * Mapped $key will be ignored
     *
     * @param $key
     * @param $field
     * @return bool
     */
    public function hExists($key, $field)
    {
        if ($this->isMappedKey($key) || $this->isMappedKey($field)) {
            return false;
        }
        return (bool) $this->getDriver()->hExists($key, $field);
    }

    /**
     * Fill a $key $field with the given value
     * Check if old value is a map to delete all references
     * Check if value is not too large, otherwise create a map
     *
     * @param $key
     * @param $field
     * @param $value
     * @return mixed
     * @throws common_Exception If the the size is misconfigured
     */
    public function hSet($key, $field, $value)
    {
        if (!$this->hasMaxSize()) {
            return $this->getDriver()->hSet($key, $field, $value);
        }

        if ($this->isLarge($value)) {
            $value = $this->setLargeValue($this->getMappedKey($key, $field), $value, 0, false);
        }
        $oldValue = $this->getDriver()->hGet($key, $field);
        if ($this->isSplit($oldValue)) {
            $this->deleteMappedKey($field, $oldValue);
        }

        return $this->getDriver()->hSet($key, $field, $value);
    }

    /**
     * Get the value of $field under $key
     * Mapped key will be ignored
     * Check if value is a map, in case of yes, join values
     *
     * @param $key
     * @param $field
     * @return bool|mixed|string
     */
    public function hGet($key, $field)
    {
        if ($this->hasMaxSize()) {
            if ($this->isMappedKey($key) || $this->isMappedKey($field)) {
                return false;
            }
        }
        $value = $this->getDriver()->hGet($key, $field);
        if ($this->hasMaxSize()) {
            if ($this->isSplit($value)) {
                $value =  $this->join($this->getMappedKey($key, $field), $value);
            }
        }
        return $value;
    }

    /**
     * Get old $field of a $key
     * If one of values is a map, join related values
     *
     * @param $key
     * @return array
     */
    public function hGetAll($key)
    {
        $fields = $this->getDriver()->hGetAll($key);

        if (empty($fields)) {
            return $fields;
        }

        if ($this->hasMaxSize()) {
            foreach ($fields as $field => $value) {
                if ($this->isSplit($value)) {
                    $fields[$field] = $this->join($this->getMappedKey($key, $field), $value);
                }
            }
        }

        return $fields;
    }

    /**
     * Get a list of existing $keys
     * Mapped will be ignored
     *
     * @param $pattern
     * @return array
     */
    public function keys($pattern)
    {
        $keys = $this->getDriver()->keys($pattern);

        if ($this->hasMaxSize()) {
            foreach ($keys as $index => $key) {
                if ($this->isMappedKey($key)) {
                    unset($keys[$index]);
                }
            }
        }
        return $keys;
    }

    /**
     * Delete a key. If key is split, all associated mapped key are deleted too
     *
     * @param $key
     * @return bool
     */
    public function del($key)
    {
        if ($this->isMappedKey($key)) {
            return false;
        } else {
            $success = true;
            if ($this->hasMaxSize()) {
                $fields = $this->getDriver()->hGetAll($key);
                if (!empty($fields)) {
                    foreach ($fields as $subKey => $value) {
                        if ($this->isSplit($value)) {
                            $success = $success && $this->deleteMappedKey($subKey, $value);
                        }
                    }
                }
                $success = $success && $this->deleteMappedKey($key);
            }

            return $success && $this->getDriver()->del($key);
        }
    }

    /**
     * Get a serial to reference mapped $key
     *
     * @param $key
     * @param $field
     * @return string
     */
    protected function getMappedKey($key, $field)
    {
        return $key . '.' . $field;
    }

}
