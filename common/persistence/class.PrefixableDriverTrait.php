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
 *
 *
 */

/**
 * Trait common_persistence_PrefixableDriverTrait
 *
 * A trait to have the possibility to add a prefix to key
 * If a persistence configuration contains the key word 'prefix', the value will be the prefix of records
 *
 * Usage : use setPrefixFromOptions() add driver connection to extract the prefix
 * Each time you use a key use getRealKey() to add the record prefix
 */
trait common_persistence_PrefixableDriverTrait
{
    /** @var  string The prefix */
    private $keyPrefix;

    /** @var string The prefix name */
    private $prefixName = 'prefix';

    /**
     * Transform the key to have the real $key (with a prefix)
     * If there is no prefix, the key is not modified
     *
     * @param $key
     * @return string
     */
    protected function getRealKey($key)
    {
        if ($this->keyPrefix) {
            return $this->keyPrefix . $key;
        }
        return $key;
    }

    /**
     * Extract the optional parameter from $option with name = $this->prefixName
     *
     * @param array $options
     */
    protected function setPrefixFromOptions(array $options)
    {
        $this->keyPrefix = isset($options[$this->prefixName]) ? $options[$this->prefixName] : false;
    }

    /**
     * Check if the prefix is applicable
     *
     * @return bool
     */
    protected function hasKeyPrefix()
    {
        return isset($this->keyPrefix);
    }
}