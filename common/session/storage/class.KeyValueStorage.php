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

/**
 * Session implementation using the persistence
 * 
 * @author Joel Bout <joel@taotesting.com>
 * @package generis
 */
class common_session_storage_KeyValueStorage
    implements common_session_storage_SessionStorage
{
    /**
     * @var common_persistence_KeyValuePersistence
     */
    private $server = null;

    /**
     * (non-PHPdoc)
     * @see common_session_storage_SessionStorage::open()
     */
    public function open($savePath, $sessionName){
        $this->server = common_persistence_KeyValuePersistence::getPersistence('redis');
        return true;
    }

    /**
     * (non-PHPdoc)
     * @see common_session_storage_SessionStorage::close()
     */
    public function close()
    {
        return true;
    }

    /**
     * (non-PHPdoc)
     * @see common_session_storage_SessionStorage::read()
     */
    public function read($id)
    {
        return $this->server->get($id);
    }

    /**
     * (non-PHPdoc)
     * @see common_session_storage_SessionStorage::write()
     */
    public function write($id, $data)
    {  
        return $this->server->set($id, $data, (int) ini_get('session.gc_maxlifetime'));
    }

    /**
     * (non-PHPdoc)
     * @see common_session_storage_SessionStorage::destroy()
     */
    public function destroy($id){
        $this->server->del($id);
    }

    /**
     * (non-PHPdoc)
     * @see common_session_storage_SessionStorage::gc()
     */
    public function gc($maxlifetime)
    { 
        //ttl set when writing to storage
    }
}