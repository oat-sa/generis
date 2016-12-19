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
 * Copyright (c) 2016 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 * @author Christophe GARCIA <christopheg@taotesting.com>
 * @license GPLv2
 * @package generis
 *
 */

namespace oat\generis\model\kernel\persistence\smoothsql\search;

use oat\search\base\LimitableInterface;
use oat\search\base\OptionsInterface;
use oat\search\base\ParentFluateInterface;
use oat\search\base\Query\DriverSensitiveInterface;
use oat\search\base\QueryBuilderInterface;
use oat\search\base\SortableInterface;
use oat\search\UsableTrait\DriverSensitiveTrait;
use oat\search\UsableTrait\LimitableTrait;
use oat\search\UsableTrait\OptionsTrait;
use oat\search\UsableTrait\ParentFluateTrait;
use oat\search\UsableTrait\SortableTrait;

/**
 *
 * @author Christophe GARCIA <christopheg@taotesting.com>
 */
class QueryJoiner implements DriverSensitiveInterface, SortableInterface, LimitableInterface, ParentFluateInterface, OptionsInterface {

    use SortableTrait;

use LimitableTrait;

use ParentFluateTrait;

use DriverSensitiveTrait;

use OptionsTrait;

    /**
     *
     * @var QueryBuilderInterface 
     */
    protected $query;

    /**
     *
     * @var QueryBuilderInterface
     */
    protected $join;

    /**
     *
     * @var string 
     */
    protected $on;
    protected $count = false;

    /**
     * 
     * @param QueryBuilderInterface $query
     * @return $this
     */
    public function setQuery(QueryBuilderInterface $query) {
        $this->query = $query->sort([])->setOffset(null)->setLimit(null);
        return $this;
    }

    /**
     * 
     * @param QueryBuilderInterface $query
     * @return $this
     */
    public function join(QueryBuilderInterface $query) {
        $this->join = $query->sort([])->setOffset(null)->setLimit(null);
        return $this;
    }

    /**
     * 
     * @param string $predicate1
     * @param string $predicate2
     * @return $this
     */
    public function on($predicate) {
        $this->on = $predicate;
        return $this;
    }

    /**
     * 
     */
    public function execute() {
        /* @var $gateWay GateWay */
        $gateWay = $this->getParent();
        $mainQuery = $gateWay->getSerialyser()->setCriteriaList($this->query)->serialyse();
        $joinQuery = $gateWay->getSerialyser()->setCriteriaList($this->join)->serialyse();
        return $this->createMetaQuery($mainQuery, $joinQuery, false);
    }

    public function count() {
        /* @var $gateWay GateWay */
        $gateWay = $this->getParent();
        $mainQuery = $gateWay->getSerialyser()->setCriteriaList($this->query)->serialyse();
        $joinQuery = $gateWay->getSerialyser()->setCriteriaList($this->join)->serialyse();
        return $this->createMetaQuery($mainQuery, $joinQuery, true);
    }

    /**
     * 
     * @param type $query
     * @return string
     */
    protected function addLimit($query) {
        $limit = $this->getLimit();
        $offset = $this->getOffset();
        if (intval($limit) > 0) {
            $query .= ' ' . $this->getDriverEscaper()->dbCommand('LIMIT') . ' ' . $limit;
            if (!is_null($offset)) {
                $query .= ' ' . $this->getDriverEscaper()->dbCommand('OFFSET') . ' ' . $offset;
            }
        }
        return $query;
    }

    /**
     * create query begining
     * @return $this
     */
    public function getLanguage() {
        $options = $this->getOptions();
        $language = '';
        if (array_key_exists('language', $options)) {
            $language = $this->setLanguageCondition($options['language'], true);
        }
        if (array_key_exists('defaultLanguage', $options)) {
            $language = $this->setLanguageCondition($options['defaultLanguage'], true);
        }

        return $language;
    }

    /**
     * return an SQL string with language filter condition
     * 
     * @param string $language
     * @param boolean $emptyAvailable
     * @return string
     */
    public function setLanguageCondition($language, $emptyAvailable = false) {
        $languageField = $this->getDriverEscaper()->reserved('l_language');
        $languageValue = $this->getDriverEscaper()->escape($language);
        $sql = ' AND ( ';
        $sql .= $languageField . ' = ' . $this->getDriverEscaper()->quote($languageValue) . '';
        if ($emptyAvailable) {
            $sql .= ' ' . $this->getDriverEscaper()->dbCommand('OR') . ' ' . $languageField . ' = ' . $this->getDriverEscaper()->getEmpty();
        }
        $sql .= ' ) ';
        return $sql;
    }

    /**
     * 
     */
    protected function sortedQuery($main, $join) {

        $sort = $this->getSort();
        $index = 1;

        $aggrObject = $this->getDriverEscaper()->reserved('J') . '.' .
                $this->getDriverEscaper()->reserved('object');

        $query = $this->getDriverEscaper()->dbCommand('SELECT') . ' ' . $this->getDriverEscaper()->reserved('subject') . ' ' .
                $this->getDriverEscaper()->dbCommand('FROM') . ' ( ' .
                $this->getDriverEscaper()->dbCommand('SELECT') . ' ' . $this->getDriverEscaper()->dbCommand('DISTINCT') . ' ' .
                $this->getDriverEscaper()->reserved('T') . '.' . $this->getDriverEscaper()->reserved('subject') . ' , ';

        $sortKeys = [];

        foreach ($sort as $predicate => $sortOrder) {

            $alias = 'J' . $index;
            $sorterAlias = 'sorter' . $index;

            $aggrObject = $this->getDriverEscaper()->reserved($alias) . '.' .
                    $this->getDriverEscaper()->reserved('object');

            $sortKeys[] = $this->getDriverEscaper()->groupAggregation($aggrObject, ' ') . ' ' .
                    $this->getDriverEscaper()->dbCommand('AS') . ' ' .
                    $this->getDriverEscaper()->reserved($sorterAlias);

            $index++;
        }

        $query .= implode(' , ', $sortKeys) . ' ' . $this->getDriverEscaper()->dbCommand('FROM') . ' ( ' .
                $main . ' )' .
                $this->getDriverEscaper()->dbCommand('AS') . ' ' . $this->getDriverEscaper()->reserved('T') .
                $this->getDriverEscaper()->dbCommand('JOIN') . ' ( ' .
                $this->getDriverEscaper()->dbCommand('SELECT') . ' ' . $this->getDriverEscaper()->reserved('subject') . ' , ' .
                $this->getDriverEscaper()->reserved('object') . ' ' . $this->getDriverEscaper()->dbCommand('FROM') . ' ' .
                $this->getDriverEscaper()->reserved('statements') . ' ' . $this->getDriverEscaper()->dbCommand('WHERE') . ' ' .
                $this->getDriverEscaper()->reserved('predicate') . ' = ' .
                $this->getDriverEscaper()->quote($this->getDriverEscaper()->escape($this->on)) . ' ) ' .
                $this->getDriverEscaper()->dbCommand('AS') . ' ' . $this->getDriverEscaper()->reserved('R') .
                $this->getDriverEscaper()->dbCommand('ON') . ' ( ' .
                $this->getDriverEscaper()->reserved('T') . '.' . $this->getDriverEscaper()->reserved('subject') . ' = ' .
                $this->getDriverEscaper()->reserved('R') . '.' . $this->getDriverEscaper()->reserved('subject') . ' ) ';


        $index = 1;
        $sortBy = [];
        foreach ($sort as $predicate => $sortOrder) {

            $alias = 'J' . $index;
            $orderSub = 'SUBJ' . $index;
            $orderAlias = 'ORDERJ' . $index;

            $query .= $this->getDriverEscaper()->dbCommand('JOIN') . ' ( ' .
                    $this->getDriverEscaper()->dbCommand('SELECT') . ' ' .
                    $this->getDriverEscaper()->reserved($orderAlias) . '.' . $this->getDriverEscaper()->reserved('subject') . ' , ' .
                    $this->getDriverEscaper()->reserved($orderAlias) . '.' . $this->getDriverEscaper()->reserved('object') . ' ' .
                    $this->getDriverEscaper()->dbCommand('FROM') . ' ( ' .
                    $join . ')' .
                    $this->getDriverEscaper()->dbCommand('AS') . ' ' . $this->getDriverEscaper()->reserved($orderSub) . ' ' .
                    $this->getDriverEscaper()->dbCommand('JOIN') . ' ( ' .
                    $this->getDriverEscaper()->dbCommand('SELECT') . ' ' . $this->getDriverEscaper()->reserved('subject') . ' , ' .
                    $this->getDriverEscaper()->reserved('object') . ' ' . $this->getDriverEscaper()->dbCommand('FROM') . ' ' .
                    $this->getDriverEscaper()->reserved('statements') . ' ' . $this->getDriverEscaper()->dbCommand('WHERE') . ' ' .
                    $this->getDriverEscaper()->reserved('predicate') . ' = ' .
                    $this->getDriverEscaper()->quote($this->getDriverEscaper()->escape($predicate)) . ' ' .
                    $this->getLanguage() . ' ' . $this->getDriverEscaper()->dbCommand('GROUP') . ' ' . $this->getDriverEscaper()->dbCommand('BY') . ' ' .
                    $this->getDriverEscaper()->reserved('subject') . ' , ' .
                    $this->getDriverEscaper()->reserved('object') . ' ) ' .
                    $this->getDriverEscaper()->dbCommand('AS') . ' ' . $this->getDriverEscaper()->reserved($orderAlias) . ' ' .
                    $this->getDriverEscaper()->dbCommand('ON') . ' ( ' .
                    $this->getDriverEscaper()->reserved($orderSub) . '.' . $this->getDriverEscaper()->reserved('subject') . ' = ' .
                    $this->getDriverEscaper()->reserved($orderAlias) . '.' . $this->getDriverEscaper()->reserved('subject') . ' ) ) ' .
                    $this->getDriverEscaper()->dbCommand('AS') . ' ' . $this->getDriverEscaper()->reserved($alias) . ' ' .
                    $this->getDriverEscaper()->dbCommand('ON') . ' ( ' .
                    $this->getDriverEscaper()->reserved($alias) . '.' . $this->getDriverEscaper()->reserved('subject') . ' = ' .
                    $this->getDriverEscaper()->reserved('R') . '.' . $this->getDriverEscaper()->reserved('object') . ' ) ';

            $sortBy[] = $this->getDriverEscaper()->reserved('sorter' . $index) . ' ' . $this->getDriverEscaper()->dbCommand($sortOrder);
            $index ++;
        }

        $query .= $this->getDriverEscaper()->dbCommand('GROUP') . ' ' . $this->getDriverEscaper()->dbCommand('BY') . ' ' .
                $this->getDriverEscaper()->reserved('T') . '.' . $this->getDriverEscaper()->reserved('subject') . ' ' .
                $this->getDriverEscaper()->dbCommand('ORDER') . ' ' . $this->getDriverEscaper()->dbCommand('BY') . ' ' .
                implode(' , ', $sortBy) . ' ) ' .
                $this->getDriverEscaper()->dbCommand('AS') . ' ' . $this->getDriverEscaper()->reserved('rootq');

        return ($query);
    }

    /**
     * 
     * @param string $main
     * @param string $join
     * @return string
     */
    protected function unSortedQuery($main, $join) {

        $query = $this->getDriverEscaper()->dbCommand('SELECT') . ' ' .
                $this->getDriverEscaper()->dbCommand('DISTINCT') . ' ' .
                $this->getDriverEscaper()->reserved('A') . '.' .
                $this->getDriverEscaper()->reserved('subject') . ' ' .
                $this->getDriverEscaper()->dbCommand('FROM') . ' ' .
                '(' . $main . ')' . $this->getDriverEscaper()->dbCommand('AS') . ' ' .
                $this->getDriverEscaper()->reserved('A') . ' ' .
                $this->getDriverEscaper()->dbCommand('JOIN') . ' ' .
                '(' . $this->getDriverEscaper()->dbCommand('SELECT') . ' ' .
                $this->getDriverEscaper()->reserved('subject') . ' , ' . $this->getDriverEscaper()->reserved('object') . ' ' .
                $this->getDriverEscaper()->dbCommand('FROM') . ' ' . $this->getDriverEscaper()->reserved('statements') . ' ' .
                $this->getDriverEscaper()->dbCommand('WHERE') . ' ' .
                $this->getDriverEscaper()->reserved('predicate') . ' = ' .
                $this->getDriverEscaper()->quote($this->getDriverEscaper()->escape($this->on)) . ') ' .
                $this->getDriverEscaper()->dbCommand('AS') . ' ' . $this->getDriverEscaper()->reserved('R') . ' ' .
                $this->getDriverEscaper()->dbCommand('ON') .
                ' (' . $this->getDriverEscaper()->reserved('A') . '.' .
                $this->getDriverEscaper()->reserved('subject') . ' = ' .
                $this->getDriverEscaper()->reserved('R') . '.' .
                $this->getDriverEscaper()->reserved('subject') . ') ' .
                $this->getDriverEscaper()->dbCommand('JOIN') . ' (' .
                $this->getDriverEscaper()->dbCommand('SELECT') . ' ' .
                $this->getDriverEscaper()->reserved('subject') . ' ' .
                $this->getDriverEscaper()->dbCommand('FROM') . ' ' .
                '(' . $join . ')' . $this->getDriverEscaper()->dbCommand('AS') . ' ' .
                $this->getDriverEscaper()->reserved('sd') . ' ) ' .
                $this->getDriverEscaper()->dbCommand('AS') . ' ' .
                $this->getDriverEscaper()->reserved('D') . ' ' .
                $this->getDriverEscaper()->dbCommand('ON') .
                '(' . $this->getDriverEscaper()->reserved('D') . '.' . $this->getDriverEscaper()->reserved('subject') .
                ' = ' . $this->getDriverEscaper()->reserved('R') . '.' . $this->getDriverEscaper()->reserved('object') . ')';

        if ($this->getRandom()) {
            $query = $this->getDriverEscaper()->dbCommand('SELECT') . ' ' .
                    $this->getDriverEscaper()->reserved('subject') . ' ' .
                    $this->getDriverEscaper()->dbCommand('FROM') . ' ' .
                    ' ( ' . $query . ' ) ' . $this->getDriverEscaper()->dbCommand('AS') . ' ' .
                    $this->getDriverEscaper()->reserved('finalQ') . ' ' .
                    $this->getDriverEscaper()->dbCommand('ORDER') . ' ' .
                    $this->getDriverEscaper()->dbCommand('BY') . ' ' .
                    $this->getDriverEscaper()->random() . ' ';
        }
        return $query;
    }

    /**
     * @param string $main
     * @param string $join
     * @return string
     */
    protected function createMetaQuery($main, $join, $count) {
        $sort = $this->getSort();

        if (empty($sort)) {
            $query = $this->unSortedQuery($main, $join);
        } else {
            $query = $this->sortedQuery($main, $join);
        }
        if ($count) {
            $query = $this->getDriverEscaper()->dbCommand('SELECT') . ' ' .
                    $this->getDriverEscaper()->dbCommand('COUNT') .
                    '( ' . $this->getDriverEscaper()->reserved('subject') . ' ) ' .
                    $this->getDriverEscaper()->dbCommand('AS') . ' ' . $this->getDriverEscaper()->reserved('cpt') . ' ' .
                    $this->getDriverEscaper()->dbCommand('FROM') . ' ( ' . $query . ' ) as cptQ';
            return $query;
        }
        return $this->addLimit($query);
    }

}
