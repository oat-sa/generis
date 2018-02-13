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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */
use oat\oatbox\service\ConfigurableService;

/**
 * Session implementation as a Key Value storage and using the persistence
 * 
 * @author Joel Bout <joel@taotesting.com>
 * @package generis
 */
class common_session_php_KeyValueSessionHandler extends ConfigurableService
    implements common_session_php_SessionHandler
{
    const OPTION_PERSISTENCE = 'persistence'; 

    const OPTION_USE_LOCKING = 'use_locking';

    const KEY_NAMESPACE = "generis:session:";

    /**
     * Wait time (1ms) after first locking attempt. It doubles
     * for every unsuccessful retry until it either reaches
     * MAX_WAIT_TIME or succeeds.
     */
    const MIN_WAIT_TIME = 1000;

    /**
     * Maximum wait time (128ms) between locking attempts.
     */
    const MAX_WAIT_TIME = 128000;

    /**
     * @var int
     */
    private $sessionTtl;

    /**
     * A collection of every session ID that is being locked by
     * the current thread of execution. When session_write_close()
     * is called the locks on all these IDs are removed.
     *
     * @var string[]
     */
    private $openSessions = [];

    /**
     * @var common_persistence_KeyValuePersistence
     */
    private $sessionPersistence = null;

    /**
     * @param array $options
     */
    public function __construct(array $options = array())
    {
        parent::__construct($options);

        $this->sessionTtl = (int) ini_get('session.gc_maxlifetime');
    }

    protected function getPersistence() {
        if (is_null($this->sessionPersistence)) {
            $this->sessionPersistence = common_persistence_KeyValuePersistence::getPersistence($this->getOption(self::OPTION_PERSISTENCE));
        }
        return $this->sessionPersistence;
    }

    /**
     * @return bool
     */
    protected function isLockingEnabled()
    {
        $val = $this->getOption(static::OPTION_USE_LOCKING) !== null ? $this->getOption(static::OPTION_USE_LOCKING) : 0;
        return boolval($val);
    }

    /**
     * (non-PHPdoc)
     * @see common_session_storage_SessionStorage::open()
     */
    public function open($savePath, $sessionName){
           return true;
    }

    /**
     * (non-PHPdoc)
     * @see common_session_storage_SessionStorage::close()
     */
    public function close()
    {
        if ($this->isLockingEnabled()) {
            $this->releaseLocks();
        }

        return true;
    }

    /**
     * (non-PHPdoc)
     * @see common_session_storage_SessionStorage::read()
     * @throws common_Exception
     */
    public function read($id)
    {
        if ($this->isLockingEnabled()){
            $this->acquireLockOn($id);
        }

        $session = $this->getPersistence()->get(self::KEY_NAMESPACE.$id);
        return is_string($session) ? $session : '';
    }

    /**
     * (non-PHPdoc)
     * @see common_session_storage_SessionStorage::write()
     * @throws common_Exception
     */
    public function write($id, $data)
    {  
        return $this->getPersistence()->set(self::KEY_NAMESPACE.$id, $data, $this->sessionTtl);
    }

    /**
     * (non-PHPdoc)
     * @see common_session_storage_SessionStorage::destroy()
     */
    public function destroy($id){
        if ($this->isLockingEnabled()){
            $this->getPersistence()->del("{$id}_lock");
        }
        $this->getPersistence()->del(self::KEY_NAMESPACE.$id);
        return true;
    }

    /**
     * (non-PHPdoc)
     * @see common_session_storage_SessionStorage::gc()
     */
    public function gc($maxlifetime)
    { 
        //
        //problem here either 
        // solution 1 : do two explicit handlers for each specific persistence (Redis, SQL) 
        // solution 2 : Check if the eprsistence is capable of autonomous garbage  
        //
        return true;
    }

    /**
     * @param $sessionId
     * @throws common_Exception
     */
    private function acquireLockOn($sessionId)
    {
        $wait = self::MIN_WAIT_TIME;

        while (false === $this->getPersistence()->set("{$sessionId}_lock", '', $this->sessionTtl)) {
            usleep($wait);

            if (self::MAX_WAIT_TIME > $wait) {
                $wait *= 2;
            }
        }

        $this->openSessions[] = $sessionId;
    }

    private function releaseLocks()
    {
        foreach ($this->openSessions as $session_id) {
            $this->getPersistence()->del("{$session_id}_lock");
        }

        $this->openSessions = [];
    }
}