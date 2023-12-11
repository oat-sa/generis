<?php

/*
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
 * Copyright (c) 2023 (original work) Open Assessment Technologies SA;
 *
 */

namespace oat\generis\model\kernel\persistence\starsql\search;

use common_persistence_Manager;
use Laudis\Neo4j\Databags\Statement;
use oat\oatbox\service\ServiceManager;
use oat\search\base\exception\SearchGateWayExeption;
use oat\search\base\QueryBuilderInterface;
use oat\search\base\ResultSetInterface;
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

    public function init()
    {
        parent::init();

        $this->connector = ServiceManager::getServiceManager()
                ->get(common_persistence_Manager::SERVICE_ID)
                ->getPersistenceById($this->options['persistence'] ?? 'neo4j');

        return $this;
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
        $result = $this->fetchObjectList(parent::search($Builder));
        $totalCount = $this->count($Builder);

        return new $this->resultSetClassName($result, $totalCount);
    }

    /**
     * @param QueryBuilderInterface $Builder
     * @param string $propertyUri
     * @param bool $isDistinct
     *
     * @return ResultSetInterface
     */
    public function searchTriples(QueryBuilderInterface $Builder, string $propertyUri, bool $isDistinct = false)
    {
        $result = $this->fetchTripleList(
            parent::searchTriples($Builder, $propertyUri, $isDistinct)
        );

        return new $this->resultSetClassName($result, count($result));
    }

    /**
     * return total count result
     *
     * @param QueryBuilderInterface $builder
     *
     * @return int
     */
    public function count(QueryBuilderInterface $builder)
    {
        return (int)($this->fetchOne(parent::count($builder)));
    }

    private function fetchTripleList(Statement $query): array
    {
        $returnValue = [];
        $statement = $this->connector->runStatement($query);
        foreach ($statement as $row) {
            $triple = new \core_kernel_classes_Triple();

            $triple->id = $row->get('id', 0);
            $triple->subject = $row->get('uri', '');
            $triple->object = $row->get('object') ?? '';

            $returnValue[] = $triple;
        }
        return $returnValue;
    }


    private function fetchObjectList(Statement $query): array
    {
        $returnValue = [];
        $statement = $this->connector->runStatement($query);
        foreach ($statement as $result) {
            $object = $result->current();
            if (!$object) {
                continue;
            }
            $returnValue[] = \common_Utils::toResource($object->getProperty('uri'));
        }
        return $returnValue;
    }

    private function fetchOne(Statement $query)
    {
        $results = $this->connector->runStatement($query);
        return $results->first()->current();
    }

    public function getQuery()
    {
        if ($this->parsedQuery instanceof Statement) {
            return $this->parsedQuery->getText();
        } else {
            return '';
        }
    }
}
