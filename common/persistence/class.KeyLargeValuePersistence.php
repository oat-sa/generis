<?php
/**
 * Copyright 2017 Open Assessment Technologies SA
 *
 * This file is part of the Tao AWS tools.
 *
 * Foobar is free software: you can redistribute it and/or modify it under the terms of the GNU
 * General Public License as published by the Free Software Foundation, either version 3 of the License,
 * or (at your option) any later version.
 *
 * Foobar is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even
 * the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General
 * Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this package.
 * If not, see http://www.gnu.org/licenses/.
 *
 */

class common_persistence_KeyLargeValuePersistence extends common_persistence_Persistence
{
    /**
     * @var int The maximum with allowed by the driver for the value
     */
    protected $width;

    /**
     * common_persistence_LargeValueMapper constructor.
     *
     * Create a LargeValue persistence with a value max width
     *
     * @param array $params
     * @param common_persistence_driver $driver
     * @param $width
     * @throws common_Exception
     */
    public function __construct(array $params = array(), common_persistence_driver $driver, $width)
    {
        parent::__construct($params, $driver);
        if (!is_int($width)) {
            throw new common_Exception('Persistence max value width has to be an integer');
        }
        $this->width = $width - strlen($this->getMapIdentifier());
    }

    /**
     * Set a $key with a $value
     * If $value is too large, it is split into multiple $mappedKey.
     * These new keys are serialized and stored into actual $key
     *
     * @param $key
     * @param $value
     * @param null $ttl
     * @return bool
     */
    public function set($key, $value, $ttl = null)
    {
        if ($this->isLarge($value)) {
            common_Logger::i('Large value detected into KeyValue persistence. Splitting value for key : ' . $key);
            $value = $this->setLargeValue($key, $value);
        }
        return $this->getDriver()->set($key, $value, $ttl);
    }

    /**
     * Get $key from driver. If $key is split, all mapped values are retrieved and join to restore original value
     *
     * @param string $key
     * @return bool|int|null|string
     */
    public function get($key)
    {
        $value = $this->getDriver()->get($key);
        if ($this->isSplit($value)) {
            common_Logger::i('Large value detected into KeyValue persistence. Joining value for key : ' . $key);
            $value =  $this->join($key, $value);
        }
        return $value;
    }

    /**
     * Check if a key exists
     * Return false if $key is a mappedKey
     *
     * @param $key
     * @return bool
     */
    public function exists($key)
    {
        if ($this->isMappedKey($key)) {
            return false;
        } else {
            return $this->getDriver()->exists($key);
        }
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
        }

        $success = true;
        if ($this->isSplit($key)) {
            foreach ($this->unSerializeMap($this->get($key)) as $mappedKey) {
                $success = $success && $this->getDriver()->del($mappedKey);
            }
        }
        return $success && $this->getDriver()->del($key);
    }

    /**
     * Set a large value recursively.
     * Create a map of value (split by width range) and store the serialize map as current value
     *
     * @param $key
     * @param $value
     * @param int $level
     * @return bool
     */
    protected function setLargeValue($key, $value, $level = 0)
    {
        if ($level > 0) {
            $key .= '-' . $level;
        }

        if (! $this->isLarge($value)) {
            return $this->set($key, $value);
        }

        $map = $this->createMap($key, $value);
        foreach ($map as $mappedKey => $valuePart) {
            $this->set($this->transformReferenceToMappedKey($mappedKey), $valuePart);
        }

        return $this->setLargeValue($key, $this->serializeMap($map), $level + 1);
    }

    /**
     * Check if the given $value is larger than $this max width
     *
     * @param $value
     * @return bool
     */
    protected function isLarge($value)
    {
        return strlen($value) > $this->width;
    }

    /**
     * Cut a string into an array with $width option
     *
     * @param $value
     * @return array
     */
    protected function split($value)
    {
        return str_split($value, $this->width);
    }

    /**
     * Join different values referenced into a map recursively
     *
     * @param $key
     * @param $value
     * @param int $level
     * @return string
     */
    protected function join($key, $value, $level = 0)
    {
        if ($level > 0) {
            $key = $key . '-' . $level;
        }

        $valueParts = [];
        foreach ($this->unSerializeMap($value) as $mappedKey) {
            $mappedKey = $this->transformReferenceToMappedKey($mappedKey);
            $valueParts[$this->getMappedKeyIndex($mappedKey, $key)] = $this->getDriver()->get($mappedKey);
        }

        uksort($valueParts, 'strnatcmp');
        $value = implode('', $valueParts);
        if ($this->isSplit($value)) {
            $value = $this->join($key, $value, $level + 1);
        }
        return $value;
    }

    /**
     * Split a large value to an array with value width lesser than required max width
     * Construct the array with index of value
     *
     * @param $key
     * @param $value
     * @return array
     */
    protected function createMap($key, $value)
    {
        $splitValue = $this->split($value);
        $map = [];
        foreach ($splitValue as $index => $part) {
            $map[$key . '-' . $index] = $part;
        }
        return $map;
    }

    /**
     * Transform a map reference to an identifiable $key
     *
     * @param $key
     * @return string
     */
    protected function transformReferenceToMappedKey($key)
    {
        return '<<<<mappedKey>>>>' . $key . '<<<</mappedKey>>>>';
    }

    /**
     * Check if current $key is part of a map
     *
     * @param $key
     * @return bool
     */
    protected function isMappedKey($key)
    {
        return substr($key, 0, 5) == '<<<<mappedKey>>>>' && substr($key, -5) == '<<<</mappedKey>>>>';
    }

    /**
     *  Get the mapped key index of a mappedKey
     *
     * @param $mappedKey
     * @param $key
     * @return bool|string
     */
    protected function getMappedKeyIndex($mappedKey, $key)
    {
        $startWidth = strlen('<<<<mappedKey>>>>') - 1;
        $key = substr($key, $startWidth, strrpos($key, '<<<</mappedKey>>>>')-$startWidth);
        return substr($mappedKey, strlen($key . '-'));
    }

    /**
     * Serialize a map to set it as a value
     *
     * @param array $map
     * @return string
     */
    protected function serializeMap(array $map)
    {
        return $this->getMapIdentifier() . json_encode(array_keys($map));
    }

    /**
     * Unserialize a map that contains references to mapped keys
     *
     * @param $map
     * @return mixed
     */
    protected function unSerializeMap($map)
    {
        return json_decode(substr_replace($map, '', 0, strlen($this->getMapIdentifier())), true);
    }

    /**
     * Check if the value was split into couple of values.
     * Identifiable by the map identifier at beginning
     *
     * @param $value
     * @return bool
     */
    protected function isSplit($value)
    {
        return strpos($value, $this->getMapIdentifier()) === 0;
    }

    /**
     * Get the identifier to identify a value as split. Should be no used into beginning of none mapped key
     *
     * @return string
     */
    protected function getMapIdentifier()
    {
        return '<<<<mapped>>>>';
    }
}