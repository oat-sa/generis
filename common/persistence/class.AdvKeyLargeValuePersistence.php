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
 */

class common_persistence_AdvKeyLargeValuePersistence extends common_persistence_AdvKeyValuePersistence
{
    /**
     * Trait to allow usage a KeyLargeValuePersistence
     */
    use common_persistence_KeyLargeValuePersistenceTrait;

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
        foreach ($fields as $field => $value) {
            if ($this->isLarge($value)) {
                common_Logger::i('Large value detected into KeyValue persistence. Splitting value for key : ' . $key . ' (field : ' . $field . ')');
                $fields[$field] = $this->setLargeValue($this->getMappedKey($key, $field), $value, 0, false);
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
     * @return int
     */
    public function hSet($key, $field, $value)
    {
        $oldValue = $this->hGet($key, $field);
        if ($this->isSplit($oldValue)) {
            foreach ($this->unSerializeMap($oldValue) as $mappedKey) {
                $this->getDriver()->del($mappedKey);
            }
        }

        if ($this->isLarge($value)) {
            common_Logger::i('Large value detected into KeyValue persistence. Splitting value for key : ' . $key . ' (field : ' . $field . ')');
            $value = $this->setLargeValue($this->getMappedKey($key, $field), $value);
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
        if ($this->isMappedKey($key) || $this->isMappedKey($field)) {
            return false;
        }

        $value = $this->getDriver()->hGet($key, $field);
        if ($this->isSplit($value)) {
            common_Logger::i('Large value detected into KeyValue persistence. Joining value for key : ' . $key . ' (field : ' . $field . ')');
            $value =  $this->join($this->getMappedKey($key, $field), $value);
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
        foreach ($fields as $field => $value) {
            if ($this->isSplit($value)) {
                common_Logger::i('Large value detected into KeyValue persistence. Joining value for key : ' . $key . ' (field : ' . $field . ')');
                $fields[$field] = $this->join($this->getMappedKey($key, $field), $value);
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
        foreach ($keys as $index => $key) {
            if ($this->isMappedKey($key)) {
                unset($keys[$index]);
            }
        }
        return $keys;
    }

    /**
     * Increment $key, only for numeric
     * Mapped key will be ignored
     *
     * @param $key
     * @return bool|int
     */
    public function incr($key)
    {
        if ($this->isMappedKey($key)) {
            return false;
        }
       return $this->getDriver()->incr($key); 
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
