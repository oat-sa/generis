<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace   oat\generis\model\kernel\persistence\smoothsql\search;

use common_persistence_Manager;
use common_persistence_SqlPersistence;
use oat\oatbox\service\ServiceManager;
use oat\search\base\exception\SearchGateWayExeption;
use oat\search\base\QueryBuilderInterface;
use oat\search\TaoSearchGateWay;

/**
 * Description of GateWay
 *
 * @author Christophe GARCIA <christopheg@taotesting.com>
 */
class GateWay extends TaoSearchGateWay {
    
    /**
     *
     * @var common_persistence_SqlPersistence 
     */
    protected $connector;
    /**
     * parser service or className
     * @var string 
     */
    protected $parserList = [
        'taoRdf' => 'search.tao.parser'
    ];
    /**
     * driver escaper list
     * @var array 
     */
    protected $driverList = [
        'taoRdf' => 'search.driver.tao'
    ];
    
    /**
     * resultSet service or className
     * @var string 
     */
    protected $resultSetClassName = '\\oat\\oatbox\\search\\TaoResultSet';
    
    public function __construct() {
        $this->connector = ServiceManager::getServiceManager()
                ->get(common_persistence_Manager::SERVICE_KEY)
                ->getPersistenceById('default');
    }

        /**
     * try to connect to database. throw an exception
     * if connection failed.
     *
     * @throws SearchGateWayExeption
     * @return $this
     */
    public function connect() {
        return !is_null($this->connector);
    }
    /**
     * execute Parsed Query
     * 
     * @return type
     */
    public function search(QueryBuilderInterface $Builder) {
        $this->parse($Builder);
        if(DEBUG_MODE) {
            \common_Logger::i($this->parsedQuery);
        }
        $statement = $this->connector->query($this->parsedQuery);
        $result    = $this->statementToArray($statement);
        $cpt       = $this->count($Builder);
        $resultSet = $this->resultSetClassName;
        return new $resultSet($result , $cpt);
    }
    /**
     * 
     * @param \PDOStatement $statement
     * @return array
     */
    protected function statementToArray(\PDOStatement $statement) {
        $result = [];
        while($row = $statement->fetch(\PDO::FETCH_OBJ)) {
            $result[] = $row;
        }
        return $result;
    }


    /**
     * return total count result
     * @param QueryBuilderInterface $Builder
     * @return type
     */
    public function count(QueryBuilderInterface $Builder) {
        $this->parsedQuery = $this->getParser()->setCriteriaList($Builder)->count(true)->parse();
        if(DEBUG_MODE) {
            \common_Logger::i($this->parsedQuery);
        }
        $statement = $this->connector->query($this->parsedQuery);
        $result = $statement->fetch(\PDO::FETCH_ASSOC);
        return $result['cpt'];
    }

        /**
     * return parsed query as string
     * @return $this
     */
    public function printQuery() {
        echo $this->parsedQuery;
        return $this;
    }
    
}
