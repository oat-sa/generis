<?php
/**
 * Created by PhpStorm.
 * User: ksasim
 * Date: 3.7.15
 * Time: 17.32
 */

class common_persistence_CouchdbDriver implements \common_persistence_KvDriver{

    /** @var \couchClient */
    private $connection;

    /**
     * Allow to connect the driver and return the connection
     *
     * @param string $id
     * @param array $params
     * @return common_persistence_Persistence
     */
    function connect($id, array $params)
    {
        $this->connection = new couchClient($params['dsn'], $params['dbname']);

        if( !$this->connection->databaseExists() ){

            $this->connection->createDatabase();
        } else {
            $this->connection->deleteDatabase();
            $this->connection->createDatabase();
        }

        return new common_persistence_KeyValuePersistence($params, $this);
    }

    /**
     * Stores a value, implementing time to live is optional
     *
     * @param string $id
     * @param string $value
     * @param string $ttl
     * @return boolean
     */
    public function set($id, $value, $ttl = null)
    {
        $doc = new couchDocument( $this->connection );

        return $doc->set( array('_id' => $id, 'value' => serialize($value)) );
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
        $returnValue = false;

        try{
            $document = $this->connection->getDoc( $id );

            $returnValue = unserialize($document->value);
        } catch( Exception $e ) {

        }

        return $returnValue;
    }

    /**
     * test whenever or not an entry exists
     *
     * @param string $id
     * @return boolean
     */
    public function exists($id)
    {
        $result = false;

        try{
            $this->connection->getDoc( $id );

            $result = true;
        } catch( Exception $e ) {

        }

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
        $result = false;

        try{
            $document = $this->connection->getDoc( $id );
            $this->connection->deleteDoc( $document );
            $result = true;
        } catch( Exception $e ) {

        }

        return $result;
    }

}