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
 * Copyright (c) 2020 (original work) Open Assessment Technologies SA
 *
 */

use oat\oatbox\service\ConfigurableService;
use Psr\SimpleCache\CacheInterface;
use oat\oatbox\cache\SimpleCache;

/**
 * Wrap the PSR simple cache implementation into the legacy interface
 * @deprecated Please use oat\oatbox\cache\SimpleCache
 */
class common_cache_PsrWrapperCache extends ConfigurableService implements common_cache_Cache
{

    /**
     * puts "something" into the cache,
     *      * If this is an object and implements Serializable,
     *      * we use the serial provided by the object
     *      * else a serial must be provided
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param mixed $mixed
     * @param null $serial
     * @param null $ttl
     * @return bool
     * @throws common_exception_Error
     */
    public function put($mixed, $serial = null, $ttl = null)
    {
        if ($mixed instanceof common_Serializable) {
            if (!is_null($serial) && $serial != $mixed->getSerial()) {
                throw new common_exception_Error('Serial mismatch for Serializable ' . $mixed->getSerial());
            }
            $serial = $mixed->getSerial();
        }

        return $this->getPsrSimpleCache()->set($serial, $mixed, $ttl);
    }

    /**
     * gets the entry associted to the serial
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string serial
     * @return common_Serializable
     * @throws common_cache_NotFoundException
     */
    public function get($serial)
    {
        $returnValue = $this->getPsrSimpleCache()->get($serial, false);
        if ($returnValue === false && !$this->getPsrSimpleCache()->has($serial)) {
            $msg = "No cache entry found for '" . $serial . "'.";
            throw new common_cache_NotFoundException($msg);
        }
        return $returnValue;
    }

    /**
     * test whenever an entry associated to the serial exists
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string serial
     * @return boolean
     */
    public function has($serial)
    {
        return $this->getPsrSimpleCache()->has($serial);
    }

    /**
     * removes an entry from the cache
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string serial
     * @return mixed
     */
    public function remove($serial)
    {
        return $this->getPsrSimpleCache()->delete($serial);
    }

    /**
     * empties the cache
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return mixed
     */
    public function purge()
    {
        return $this->getPsrSimpleCache()->clear();
    }

    protected function getPsrSimpleCache() : CacheInterface
    {
        return $this->getServiceLocator()->get(SimpleCache::SERVICE_ID);
    }
}
