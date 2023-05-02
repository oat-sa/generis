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
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 *               2013-2014 (update and modification) Open Assessment Technologies SA;
 */

/**
 * basic interface a cache implementation has to implement
 *
 * @deprecated please use oat\oatbox\cache\SimpleCache
 */
interface common_cache_Cache
{
    /**
     * Service manager id.
     */
    public const SERVICE_ID = 'generis/cache';

    // --- OPERATIONS ---

    /**
     * puts "something" into the cache,
     *      * If this is an object and implements Serializable,
     *      * we use the serial provided by the object
     *      * else a serial must be provided
     *
     * @access public
     *
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     *
     * @param mixed $mixed
     * @param null $serial
     * @param null $ttl
     *
     * @return mixed
     */
    public function put($mixed, $serial = null, $ttl = null);

    /**
     * gets the entry associted to the serial
     * throws an exception if not found
     *
     * @access public
     *
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     *
     * @param  string serial
     * @param mixed $serial
     *
     * @throws common_cache_NotFoundException
     *
     * @return common_Serializable
     */
    public function get($serial);

    /**
     * test whenever an entry associted to the serial exists
     *
     * @access public
     *
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     *
     * @param  string serial
     * @param mixed $serial
     *
     * @return boolean
     */
    public function has($serial);

    /**
     * removes an entry from the cache
     *
     * @access public
     *
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     *
     * @param  string serial
     * @param mixed $serial
     *
     * @return mixed
     */
    public function remove($serial);

    /**
     * empties the cache
     *
     * @access public
     *
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     *
     * @return mixed
     */
    public function purge();
}
