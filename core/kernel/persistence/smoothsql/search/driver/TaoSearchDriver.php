<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace  oat\generis\model\kernel\persistence\smoothsql\search\driver;

use common_persistence_SqlPersistence;
use oat\oatbox\service\ServiceManager;
use oat\search\base\Query\EscaperAbstract;

/**
 * Description of TaoSearchDriver
 *
 * @author Christophe GARCIA <christopheg@taotesting.com>
 */
class TaoSearchDriver extends EscaperAbstract {
    
    /**
     * @var common_persistence_SqlPersistence 
     */
    protected $persistence;

    public function __construct() {
        $this->persistence = ServiceManager::getServiceManager()
                ->get(\common_persistence_Manager::SERVICE_KEY)
                ->getPersistenceById('default');
    }
    
    /**
     * @inherit
     */
    public function dbCommand($stringValue) {
        return strtoupper($stringValue);
    }
    /**
     * @inherit
     */
    public function escape($stringValue) {
        return $stringValue;
    }
    
    /**
     * return quoted empty string 
     */
    public function getEmpty() {
        return $this->persistence->getPlatForm()->getNullString();
    }
    
    /**
     * @inherit
     */
    public function quote($stringValue) {
        return $this->persistence->quote($stringValue);
    }
    
    /**
     * @inherit
     */
    public function reserved($stringValue) {
        return $this->persistence->getPlatForm()->quoteIdentifier($stringValue);
    }
    
}
