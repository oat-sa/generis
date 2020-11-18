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

namespace oat\oatbox\cache;

use oat\oatbox\service\ConfigurableService;
use common_persistence_KeyValuePersistence;
use oat\generis\persistence\PersistenceManager;
use common_exception_NotImplemented;
use DateInterval;
use DateTimeImmutable;

/**
 * Caches data in a key-value store
 *
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package generis
 */
class KeyValueCache extends ConfigurableService implements SimpleCache
{
    use MultipleCacheTrait;

    const OPTION_PERSISTENCE = 'persistence';

    /** @var common_persistence_KeyValuePersistence */
    private $persistence;

    public function set($key, $value, $ttl = null)
    {
        if ($ttl instanceof DateInterval) {
            $ttl = $this->dateIntervalToSeconds($ttl);
        }
        return $this->getPersistence()->set($key, $value, $ttl);
    }

    public function clear()
    {
        try {
            return $this->getPersistence()->purge();
        } catch (common_exception_NotImplemented $e) {
            return false;
        }
    }

    public function delete($key)
    {
        return $this->getPersistence()->del($key);
    }

    public function get($key, $default = null)
    {
        $returnValue = $this->getPersistence()->get($key);
        // persistence can return false on a value of false or a not found key
        return ($returnValue !== false || $this->has($key))
            ? $returnValue
            : $default;
    }

    public function has($key)
    {
        return $this->getPersistence()->exists($key);
    }

    protected function dateIntervalToSeconds(DateInterval $dateInterval): int
    {
        $reference = new DateTimeImmutable;
        $endTime = $reference->add($dateInterval);
        return $endTime->getTimestamp() - $reference->getTimestamp();
    }

    /**
     * @return common_persistence_KeyValuePersistence
     */
    protected function getPersistence()
    {
        if (is_null($this->persistence)) {
            $this->persistence = $this->getServiceLocator()->get(PersistenceManager::SERVICE_ID)->getPersistenceById($this->getOption(self::OPTION_PERSISTENCE));
        }
        return $this->persistence;
    }
}
