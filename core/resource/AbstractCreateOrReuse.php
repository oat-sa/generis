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

namespace oat\generis\model\resource;

use core_kernel_classes_Resource;
use oat\generis\model\kernel\persistence\smoothsql\search\ComplexSearchService;
use oat\generis\model\kernel\persistence\smoothsql\search\TaoResultSet;
use oat\generis\model\resource\exception\DuplicateResourceException;
use oat\oatbox\service\ConfigurableService;
use oat\generis\model\OntologyAwareTrait;

/**
 * Abstract base of CreateAndReuse service
 *
 * @author Christophe GARCIA <christopheg@taotesting.com>
 */
abstract class AbstractCreateOrReuse
    extends ConfigurableService
    implements CreateOrReuseInterface
{
    use OntologyAwareTrait;
    
    /**
     * Returns the common parent all resources have
     *
     * @return \core_kernel_classes_Class
     */
    abstract public function getRootClass();
    
    /**
     * List of keys that need to be identical between
     * two resources to represent equivalence
     *
     * @return string[]
     */
    abstract public function getUniquePredicates();

    /**
     * WILL break on non smooth implementations
     *
     * @return ComplexSearchService
     */
    protected function getSearchService() {
        return $this->getModel()->getSearchInterface();
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
        
        $searchService->searchType($searchQueryBuilder, $this->getRootClass()->getUri() , true);
        
        $criterion = $searchQueryBuilder->newQuery();
        
        foreach ($this->getUniquePredicates() as $field) {
            if (array_key_exists($field, $values)) {
                $value = $values[$field];
                $criterion->add($field)->equals($value);
            } else {
                \common_Logger::i('Predicate ' . $field . ' is not found.');
            }

        }
        
        $searchQueryBuilder->setCriteria($criterion)->setLimit(1);
        
        return $gateWay->search($searchQueryBuilder);
    }
    
    /**
     * return a new resource
     * @param array $values
     * @return core_kernel_classes_Resource
     */
    protected function createResource(array $values)  {
        return $this->getRootClass()->createInstanceWithProperties($values);
    }

    /**
     * 
     * @param array $values
     * @return boolean
     * @throws DuplicateResourceException
     */
    public function hasResource(array $values) {
        
        $result = $this->searchResource($values);
        $count = $result->count();

        if($count === 1) {
            return true;
        } elseif($count === 0) {
            return false;
        } else {
            throw new DuplicateResourceException($this->getRootClass()->getUri() , $values);
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
        $count = $result->count();

        if($count === 1) {
            return $result->current();
        } elseif($count === 0) {
            return $this->createResource($values);
        } else {
            throw new DuplicateResourceException($this->getRootClass()->getUri() , $values);
        }
    }
    
}
