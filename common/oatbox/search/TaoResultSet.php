<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace oat\oatbox\search;

/**
 * Complex Search resultSet iterator
 *
 * @author Christophe GARCIA <christopheg@taotesting.com>
 */
class TaoResultSet extends \oat\tao\model\search\ResultSet 
    implements \oat\taoSearch\model\search\ResultSetInterface 
{
    
    
    use \oat\generis\model\OntologyAwareTrait;
    
    /**
    * return total number of result
    * @return integer
    */
    public function total() {
        return $this->totalCount;
    }
    
    /**
    * return a new resource create from current subject
    * @return \core_kernel_classes_Resource
    */
    public function current() {
        $index = parent::current();
        return $this->getResource($index->subject);
    }
}
