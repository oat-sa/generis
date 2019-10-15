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
                ->get(\common_persistence_Manager::SERVICE_ID)
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
        return $this->quote('');
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
    
    /**
     * @inherit
     */
    public function random() {
        $random = [
            'mysql'      => 'RAND()', 
            'postgresql' => 'random()', 
            'mssql'      => 'NEWID()',
            ];
        $name = $this->persistence->getPlatForm()->getName();
        return $random[$name]; 
    }
    
    public function groupAggregation($variable , $separator) {
        
        $group = [
            'mysql'      => 'GROUP_CONCAT', 
            'postgresql' => 'string_agg',
        ];
        
        $name = $this->persistence->getPlatForm()->getName();
        return $group[$name] . '(' . $variable . ',' . $this->escape($this->quote($separator)) . ')'; 
    }

    /**
     * return case insensitive like operator
     * @return string
     */
    public function like() {
        $like = [
            'mysql'      => 'LIKE',
            'postgresql' => 'ILIKE',
        ];

        $name = $this->persistence->getPlatForm()->getName();
        return $like[$name];
    }

    /**
     * return case insensitive like operator
     * @return string
     */
    public function notLike() {
        return 'NOT ' . $this->like() ;
    }

}
