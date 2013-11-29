<?php


class common_session_storage_RedisStorage

    implements common_session_storage_SessionStorage
{
    private $server = null;
    private function initRedisPersistence() {
        $manager = core_persistence_Manager::singleton();
        $old = $manager->getPersistenceId();
        $manager->selectPersistence('redis');
        $this->server = $manager->getCurrentPersistence();
        $manager->selectPersistence($old);
    }
    public function open($savePath, $sessionName){
        
        $this->initRedisPersistence();
        return true;
    }
    public function close()
    {
        return true;
    }

    public function read($id)
    {
        return json_decode($this->server->get($id));
    }

    public function write($id, $data)
    {  
       $this->server->set($id, json_encode($data), (int) ini_get('session.gc_maxlifetime'));
       return true;
    }

    public function destroy($id){
        $this->server->del($id);
    }

    public function gc($maxlifetime)
    { 
        //ttl set when writing to redis
    }
   
 }
?>