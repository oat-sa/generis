<?php

/**b
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
 * Copyright (c) 2016 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 * @author Christophe GARCIA <christopheg@taotesting.com>
 * @license GPLv2
 * @package generis
 *
 */

namespace oat\generis\model\kernel\persistence\starsql\search;

use common_persistence_Manager;
use oat\oatbox\service\ServiceManager;
use oat\search\base\exception\SearchGateWayExeption;
use oat\search\base\QueryBuilderInterface;
use oat\search\ResultSet;
use oat\search\TaoSearchGateWay;

class GateWay extends TaoSearchGateWay
{
    /**
     *
     * @var \common_persistence_GraphPersistence
     */
    protected $connector;

    protected $serialyserList = [
        'taoRdf' => 'search.neo4j.serialyser'
    ];

    protected $driverList = [
        'taoRdf' => 'search.driver.neo4j'
    ];

    /**
     * resultSet service or className
     * @var string
     */
    protected $resultSetClassName = ResultSet::class;

    public function __construct()
    {
        $this->connector = ServiceManager::getServiceManager()
                ->get(common_persistence_Manager::SERVICE_ID)
                ->getPersistenceById('neo4j');
    }

    /**
     * try to connect to database. throw an exception
     * if connection failed.
     *
     * @throws SearchGateWayExeption
     * @return $this
     */
    public function connect()
    {
        return !is_null($this->connector);
    }

    public function search(QueryBuilderInterface $Builder)
    {
        $this->serialyse($Builder);
        $result    = $this->fetchObjectList($this->parsedQuery);
        $totalCount = $this->count($Builder);

        return new $this->resultSetClassName($result, $totalCount);
    }

    /**
     * @param string $query
     *
     * @return array
     */
    private function fetchObjectList(string $query): array
    {
        $returnValue = [];
        $statement = $this->connector->run($query);
        foreach ($statement as $result) {
            $object = $result->current();
            if (!$object) {
                continue;
            }
            $returnValue[] = \common_Utils::toResource($object);
        }
        return $returnValue;
    }

    /**
     * @param string $query
     */
    private function fetchOne(string $query)
    {
        $results = $this->connector->run($query);
        return $results->first()->current();
    }

    /**
     * return total count result
     *
     * @param QueryBuilderInterface $builder
     *
     * @return integer
     */
    public function count(QueryBuilderInterface $builder)
    {
        $this->parsedQuery = parent::count($builder);
        return (int)($this->fetchOne($this->parsedQuery));
    }
}
