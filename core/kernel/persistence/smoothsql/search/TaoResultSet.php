<?php
/**
 * This program is free software; you can redistribute it and/or
 *  modify it under the terms of the GNU General Public License
 *  as published by the Free Software Foundation; under version 2
 *  of the License (non-upgradable).
 *  
 * This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 * 
 *  You should have received a copy of the GNU General Public License
 *  along with this program; if not, write to the Free Software
 *  Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * 
 *  Copyright (c) 2016 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 */

namespace oat\generis\model\kernel\persistence\smoothsql\search;

use core_kernel_classes_Resource;
use oat\search\base\ResultSetInterface;
use oat\search\ResultSet;

/**
 * Complex Search resultSet iterator
 *
 * @author Christophe GARCIA <christopheg@taotesting.com>
 */
class TaoResultSet extends ResultSet 
    implements ResultSetInterface, \oat\search\base\ParentFluateInterface
{
    
    use \oat\search\UsableTrait\ParentFluateTrait;
    use \oat\generis\model\OntologyAwareTrait;
    
    /**
     *
     * @var \oat\search\QueryBuilder 
     */
    protected $countQuery;
    protected $totalCount = null;
    
    public function setCountQuery($query) {
        $this->countQuery = $query;
        return $this;
    }

    /**
    * return total number of result
    * @return integer
    */
    public function total() {
        
        if(is_null($this->totalCount)) {
            $cpt = $this->getParent()->fetchQuery($this->countQuery);
            $this->totalCount = intval($cpt['cpt']);
        }
        
        return $this->totalCount;
    }
    
    /**
    * return a new resource create from current subject
    * @return core_kernel_classes_Resource
    */
    public function current() {
        $index = parent::current();
        return $this->getResource($index->subject);
    }
}
