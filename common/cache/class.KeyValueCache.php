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
 *               2010-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 */

use oat\oatbox\service\ConfigurableService;

/**
 * Caches data in a key-value store
 *
 * @access public
 *
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 *
 * @package generis
 *
 * @deprecated Please use oat\oatbox\cache\SimpleCache
 */
class common_cache_KeyValueCache extends ConfigurableService implements common_cache_Cache
{
    public const OPTION_PERSISTENCE = 'persistence';

    /**
     * @var common_persistence_KeyValuePersistence
     */
    private $persistence;

    protected function getPersistence()
    {
        if (is_null($this->persistence)) {
            $this->persistence = $this->getServiceLocator()->get('generis/persistences')->getPersistenceById($this->getOption(self::OPTION_PERSISTENCE));
        }

        return $this->persistence;
    }

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
     * @throws common_exception_Error
     *
     * @return bool
     */
    public function put($mixed, $serial = null, $ttl = null)
    {
        if ($mixed instanceof common_Serializable) {
            if (!is_null($serial) && $serial != $mixed->getSerial()) {
                throw new common_exception_Error('Serial mismatch for Serializable ' . $mixed->getSerial());
            }
            $serial = $mixed->getSerial();
        }

        return $this->getPersistence()->set($serial, $mixed, $ttl);
    }

    /**
     * gets the entry associted to the serial
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
    public function get($serial)
    {
        $returnValue = $this->getPersistence()->get($serial);

        if ($returnValue === false && !$this->has($serial)) {
            $msg = "No cache entry found for '" . $serial . "'.";

            throw new common_cache_NotFoundException($msg);
        }

        return $returnValue;
    }

    /**
     * test whenever an entry associated to the serial exists
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
    public function has($serial)
    {
        return $this->getPersistence()->exists($serial);
    }

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
    public function remove($serial)
    {
        return $this->getPersistence()->del($serial);
    }

    /**
     * empties the cache
     *
     * @access public
     *
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     *
     * @return mixed
     */
    public function purge()
    {
        return $this->getPersistence()->purge();
    }
}
