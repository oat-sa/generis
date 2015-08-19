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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 * @author Konstantin Sasim <sasim@1pt.com>
 * @license GPLv2
 * @package generis
 *
 */

/**
 * Class common_persistence_CouchbaseDriver
 *
 * Couchbase driver for KeyValue persistence
 *
 * see CouchbasePersistenceTest::_getCouchbaseConfig() for configuration example
 */
class common_persistence_CouchbaseDriver implements \common_persistence_KvDriver{

    const COUCHBASE_NOT_FOUND_CODE = 13;

    /** @var \CouchbaseCluster */
    private $connection;
    /** @var \CouchbaseBucket */
    private $bucket;
    /**
     * Allow to connect the driver and return the connection
     *
     * Params:
     * - cluster:  couchbase cluster dsn (Ex: couchbase://localhost)
     * - bucket:   bucket name
     * - password: bucket password if required
     *
     * @param string $id
     * @param array $params
     * @return common_persistence_Persistence
     * @throws common_exception_Error
     */
    function connect($id, array $params)
    {
        if( !array_key_exists('cluster', $params) ){
            throw new common_exception_PersistenceError('Couchbase driver not configured: cluster not set');
        }

        if( !array_key_exists('bucket', $params) ){
            throw new common_exception_PersistenceError('Couchbase driver not configured: bucket not set');
        }

        $password = ( array_key_exists('password', $params) ? $params['password'] : '' );

        try {
            $this->connection = $this->_getCluster($params['cluster']);
            $this->bucket = $this->_getBucket($params['bucket'], $password);
        }catch (CouchbaseException $exception){
            $this->_wrapAndThrowException($exception);
        }

        return new common_persistence_KeyValuePersistence($params, $this);
    }

    /**
     * Stores a value, implementing time to live is optional
     *
     * @param string $id
     * @param string $value
     * @param null   $ttl Not implemented
     * @throws common_exception_NotImplemented
     * @throws common_exception_PersistenceError
     * @return boolean
     */
    public function set($id, $value, $ttl = null)
    {
        if (!is_null($ttl)) {
            throw new common_exception_NotImplemented('TTL not implemented in ' . __CLASS__);
        }

        $result = null;

        try{
            $result = $this->bucket->upsert( $id, serialize($value) );
        } catch(CouchbaseException $exception) {
            $this->_wrapAndThrowException($exception);
        }

        return ($result instanceof CouchbaseMetaDoc) && empty($result->error);
    }

    /**
     * Couchbase API Cluster wrapping for mocking purpose
     *
     * @param string $cluster
     * @return CouchbaseCluster
     */
    protected function _getCluster($cluster)
    {
        return new \CouchbaseCluster($cluster);
    }

    /**
     * Couchbase API Bucket wrapping for mocking purpose
     *
     * @param $bucketName
     * @param string $password
     * @return mixed
     */
    protected function _getBucket($bucketName, $password='')
    {
        return $this->connection->openBucket($bucketName, $password);
    }


    /**
     * Common document retrievement routine
     *
     * @param $id
     * @return bool|mixed
     * @throws common_exception_PersistenceError
     */
    protected function _get( $id )
    {
        try{
            $result = $this->bucket->get( $id );

            $returnValue = unserialize($result->value);
        } catch(CouchbaseException $exception) {
            //thrown if not found
            $returnValue = false;

            if( $exception->getCode() != self::COUCHBASE_NOT_FOUND_CODE ){
                $this->_wrapAndThrowException($exception);
            }
        }

        return $returnValue;
    }

    /**
     * Wraps Couchbase exception into PersistenceError exception
     *
     * @param CouchbaseException $exception
     * @throws common_exception_PersistenceError
     */
    protected function _wrapAndThrowException(CouchbaseException $exception)
    {
        throw new common_exception_PersistenceError( $exception->getMessage(), $exception->getCode() );
    }

    /**
     * Returns a value from storage
     * or false if not found
     *
     * @param string $id
     * @return string
     */
    public function get($id)
    {
        return $this->_get($id);
    }

    /**
     * test whenever or not an entry exists
     *
     * @param string $id
     * @return boolean
     */
    public function exists($id)
    {
        $result = ( $this->_get($id) !== false );

        return $result;
    }

    /**
     * Remove an  entry from storage
     *
     * @param string $id
     * @return boolean
     */
    public function del($id)
    {
        try{
            $this->bucket->remove( $id );
            $result = true;
        }catch(CouchbaseException $exception){
            //thrown if do not exist
            $result = false;

            if( $exception->getCode() != self::COUCHBASE_NOT_FOUND_CODE ){
                $this->_wrapAndThrowException($exception);
            }
        }

        return $result;
    }

}