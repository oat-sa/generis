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
 * Copyright (c) 2016 (original work) Open Assessment Technologies SA
 * 
 */

namespace oat\generis\model\Resource;

use core_kernel_classes_Class;
use core_kernel_classes_Resource;
use oat\generis\model\kernel\persistence\smoothsql\search\ComplexSearchService;
use oat\generis\model\kernel\persistence\smoothsql\search\TaoResultSet;
use oat\generis\model\Resource\exception\DuplicateResourceException;
use oat\oatbox\service\ConfigurableService;

/**
 * Abstract base of CreateAndReuse service
 *
 * @author Christophe GARCIA <christopheg@taotesting.com>
 */
abstract class AbstractCreateOrReuse 
    extends ConfigurableService
    implements CreateOrReuseInterface
{
    /**
     * resource type 
     * @var string
     */
    protected $type;
    
    /**
     * property list
     * @var array 
     */
    protected $uniquePredicate = [];

    /**
     * @return ComplexSearchService
     */
    protected function getSearchService() {
        return $this->getServiceManager()->get(self::SEARCH_SERVICE_ID);
    }
    
    /**
     * 
     * @param array $values
     * @return TaoResultSet
     */
    protected function searchResource(array $values) {
        
        $searchService = $this->getSearchService();
        $gateWay       = $searchService->getGateway();
        
        $searchQueryBuilder = $gateWay->query();
        
        $searchService->searchType($searchQueryBuilder, $this->type , true);
        
        $criterion = $searchQueryBuilder->newQuery();
        
        foreach ($this->uniquePredicate as $field) {
            $value = $values[$field];
            $criterion->add($field)->equals($value);
        }
        
        $searchQueryBuilder->setCriteria($criterion)->setLimit(1);
        
        return $result = $gateWay->search($searchQueryBuilder);
    }
    
    /**
     * return a new resource
     * @param array $values
     * @return core_kernel_classes_Resource
     */
    protected function createResource(array $values)  {
        $class = new core_kernel_classes_Class($this->type);
        return $class->createInstanceWithProperties($values);
    }

    /**
     * 
     * @param array $values
     * @return boolean
     * @throws DuplicateResourceException
     */
    public function hasResource(array $values) {
        
        $result = $this->searchResource($values);
        $count = $result->getTotalCount();
        
        if($count === 1) {
            return true;
        } elseif($count === 0) {
            return false;
        } else {
            throw new DuplicateResourceException($this->type , $values);
        }
    }
    
    /**
     * 
     * @param array $values
     * @return core_kernel_classes_Resource
     * @throws DuplicateResourceException
     */
    public function getResource(array $values) {
        
        $result = $this->searchResource($values);
        $count = $result->getTotalCount();
        
        if($count === 1) {
            return $result->current();
        } elseif($count === 0) {
            return $this->createResource($values);
        } else {
            throw new DuplicateResourceException($this->type , $values);
        }
    }
    
}
