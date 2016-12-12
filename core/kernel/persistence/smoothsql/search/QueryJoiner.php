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
use oat\search\base\ParentFluateInterface;
use oat\search\base\QueryBuilderInterface;
use oat\search\base\SortableInterface;
use oat\search\UsableTrait\LimitableTrait;
use oat\search\UsableTrait\ParentFluateTrait;
use \oat\search\UsableTrait\SortableTrait;

/**
 *
 * @author Christophe GARCIA <christopheg@taotesting.com>
 */
class QueryJoiner implements SortableInterface, LimitableInterface, ParentFluateInterface  {
    
    use SortableTrait;
    use LimitableTrait;
    use ParentFluateTrait;
    
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
     * @var array 
     */
    protected $on = [];


    /**
     * 
     * @param QueryBuilderInterface $query
     * @return $this
     */
    public function setQuery(QueryBuilderInterface $query) {
        $this->query = $query;
        return $this;
    }
    /**
     * 
     * @param QueryBuilderInterface $query
     * @return $this
     */
    public function join(QueryBuilderInterface $query) {
        $this->join = $query;
        return $this;
    }
    /**
     * 
     * @param string $predicate1
     * @param string $predicate2
     * @return $this
     */
    public function on($predicate1 , $predicate2) {
        $this->on[$predicate1] = $predicate2;
        return $this;
    }

    /**
     * 
     */
    public function execute() {
        /* @var $gateWay GateWay */
       $gateWay   =  $this->parent();
       $mainQuery = $gateWay->getSerialyser()->setCriteriaList($this->query)->serialyse();
       $joinQuery = $gateWay->getSerialyser()->setCriteriaList($this->join)->serialyse();
       
    }
    
}
