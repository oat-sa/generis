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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 * @author Christophe GARCIA <christopheg@taotesting.com>
 * @license GPLv2
 * @package generis
 *
 */
namespace  oat\oatbox\search;

use core_kernel_persistence_smoothsql_SmoothModel;
use oat\oatbox\service\ConfigurableService;
use oat\taoSearch\model\search\SearchGateWayInterface;
use Zend\ServiceManager\Config;
use Zend\ServiceManager\ServiceManager;
/**
 * Complexe search service
 *
 * @author Christophe GARCIA <christopheg@taotesting.com>
 */
class ComplexSearchService extends ConfigurableService
{
    
    const SERVICE_ID = 'generis/complexSearch';
    
    const SERVICE_SEARCH_ID = 'search.tao.gateway';

    /**
     * internal service locator
     * @var \Zend\ServiceManager\ServiceLocatorInterface 
     */
    protected $services;
    /**
     * search gateway
     * @var SearchGateWayInterface
     */
    protected $gateway;
    /**
     * 
     * @param array $options
     */
    public function __construct($options = array()) {
        $config         = new Config($options);
        $this->services =  new ServiceManager($config);
        parent::__construct($options);
        
        $this->gateway = $this->services->get(self::SERVICE_SEARCH_ID)
                ->setServiceLocator($this->services)
                ->init();
    }
    /**
     * determine which operator may be used
     * @param boolean $like
     * @return string
     */
    protected function getOperator($like ) {
        $operator = 'equals';
        
        if($like) {
            $operator = 'contains';
        } 
        
        return $operator;
    }
    /**
     * return search gateway
     * @return SearchGateWayInterface
     */
    public function getGateway() {
        return $this->gateway;
    }
    /**
     * return a preset query builder with types
     * @param string $class_uri
     * @param boolean $recursive
     * @return \oat\taoSearch\model\search\QueryBuilderInterface
     */
    public function searchType($class_uri , $recursive = false) {
        $query = $this->gateway->query();
        
        $Class    = new \core_kernel_classes_Class($class_uri);
        $rdftypes = [];
        
        foreach($Class->getSubClasses($recursive) as $subClass){
            $rdftypes[] = $subClass->getUri();
        }
         
        $rdftypes[] = $class_uri;
        
        $query->criteria()
                ->add('http://www.w3.org/1999/02/22-rdf-syntax-ns#type')
                ->in($rdftypes);
        
        return $query;
    }
    
    /**
     * set gateway language options
     * @param string $userLanguage
     * @param string $defaultLanguage
     * @return $this
     */
    public function setLanguage($userLanguage = '' , $defaultLanguage = \DEFAULT_LANG) {
        $options = $this->gateway->getOptions();
        if(!empty($userLanguage)) {
            $options['language'] = $userLanguage;
        }
        $options['defaultLanguage'] = $defaultLanguage;
        
        $this->gateway->setOptions($options);
        
        return $this;
    }

    /**
     * serialyse a query for searchInstance
     * use for legacy search
     * @param core_kernel_persistence_smoothsql_SmoothModel $model
     * @param array $classUri
     * @param array $propertyFilters
     * @param boolean $and
     * @param boolean $like
     * @param string $lang
     * @param integer $offset
     * @param integer $limit
     * @param string $order
     * @param string $orderDir
     * @return string
     */
    public function getQuery(core_kernel_persistence_smoothsql_SmoothModel $model, $classUri, array $propertyFilters, $and = true, $like = true, $lang = '', $offset = 0, $limit = 0, $order = '', $orderDir = 'ASC') 
    {
        $query = $this->gateway->query()->setOffset( $limit , $offset );
        
        if(!empty($order)) {
            $query->sort([$order => strtolower($orderDir)]);
        }
        
        $this->setLanguage($lang);
        
        $operator = $this->getOperator($like);
        
        $criteria = $query->criteria()
                ->add('http://www.w3.org/1999/02/22-rdf-syntax-ns#type')
                ->in($classUri)->andQuery();
        
        foreach ($propertyFilters as $predicate => $value ) {

            $nextValue = [];
            
            if(is_array($value)) {
                $nextValue = array_shift($value);
                $value = $value[0];
            }
            
            $param = $criteria->addOperation($predicate , $operator , $value);
            
            foreach ($nextValue as $value) {
                $param->addAnd($value);
            }
            if($and) {
                $param->andQuery();
            } else {
                $param->orQuery()
                        ->add('http://www.w3.org/1999/02/22-rdf-syntax-ns#type')
                        ->in($classUri)
                        ->andQuery();
            }
        }
        $queryString = $this->gateway->parse($query)->getQuery();

        if(DEBUG_MODE) {
            \common_Logger::i($queryString);
        }
        return $queryString;
    }
    
}
