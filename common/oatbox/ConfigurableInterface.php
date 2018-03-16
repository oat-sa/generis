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
 * Copyright (c) 2018 (original work) Open Assessment Technologies SA;
 *
 */

namespace oat\oatbox;

interface ConfigurableInterface
{
    /**
     * Get an option value by name
     *
     * If the option is empty or not set a NULL value will be returned.
     *
     * @param  string $name
     * @return mixed
     */
    public function getOption($name);

    /**
     * Returns whenever or not the option is defined
     *
     * @param  string $name
     * @return boolean
     */
    public function hasOption($name);

    /**
     * Set an option
     *
     * @param $name
     * @param $value
     * @return void
     */
    public function setOption($name, $value);

    /**
     * Get all options
     *
     * @return array
     */
    public function getOptions();

    /**
     * Set options
     *
     * @param array $options
     * @return void
     */
    public function setOptions(array $options);
}