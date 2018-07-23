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
namespace   oat\generis\model\kernel\persistence\smoothsql\search;

use core_kernel_persistence_smoothsql_SmoothModel;
use oat\generis\model\kernel\persistence\smoothsql\search\filter\FilterFactory;
use oat\oatbox\service\ConfigurableService;
use oat\search\base\QueryBuilderInterface;
use oat\search\base\SearchGateWayInterface;
use Zend\ServiceManager\Config;
use Zend\ServiceManager\ServiceManager;
use oat\generis\model\data\ModelManager;
use oat\generis\model\data\Model;
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
     * @var Model
     */
    protected $model;

    /**
     * Returns the internal service manager
     * @return \Zend\ServiceManager\ServiceLocatorInterface
     */
    protected function getZendServiceManager()
    {
        if (is_null($this->services)) {
            $options = $this->getOptions();
            $options['services']['search.options']['model'] = $this->model;
            $config         = new Config($options);
            $this->services =  new ServiceManager($config);
        }
        return $this->services;
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
     * Set the model the search should apply to
     * @param Model $model
     */
    public function setModel(Model $model)
    {
        $this->model = $model;
    }

    /**
     * return search gateway
     * @return SearchGateWayInterface
     */
    public function getGateway() {
        if (is_null($this->gateway)) {
            $this->gateway = $this->getZendServiceManager()->get(self::SERVICE_SEARCH_ID)
                ->setServiceLocator($this->getZendServiceManager())
                ->init();
        }
        return $this->gateway;
    }
    /**
     * return a new query builder
     * @return \oat\search\QueryBuilder
     */
    public function query() {
        return $this->getGateway()->query();
    }

        /**
     * return a preset query builder with types
     * @param QueryBuilderInterface $query
     * @param string $class_uri
     * @param boolean $recursive
     * @return QueryBuilderInterface
     */
    public function searchType(QueryBuilderInterface $query , $class_uri , $recursive = false) {

        $Class    = new \core_kernel_classes_Class($class_uri);
        $rdftypes = [];

        if ($recursive === true) {
            foreach($Class->getSubClasses(true) as $subClass) {
                $rdftypes[] = $subClass->getUri();
            }
        }

        $rdftypes[] = $class_uri;
        
        $criteria = $query->newQuery()
                ->add('http://www.w3.org/1999/02/22-rdf-syntax-ns#type')
                ->in($rdftypes);
        
        return $criteria;
    }
    
    /**
     * set gateway language options
     * @param QueryBuilderInterface $query
     * @param string $userLanguage
     * @param string $defaultLanguage
     * @return $this
     */
    public function setLanguage(QueryBuilderInterface $query , $userLanguage = '' , $defaultLanguage = \DEFAULT_LANG) {
        $options = $this->getGateway()->getOptions();
        if(!empty($userLanguage)) {
            $options['language'] = $userLanguage;
        }
        $options['defaultLanguage'] = $defaultLanguage;
        
        $this->getGateway()->setOptions($options);
        
        return $query->newQuery();
    }
    
    protected function parseValue($rawValue) {
        $result = [];
        if (!is_array($rawValue)) {
            $rawValue = [$rawValue];
        }
        foreach ($rawValue as $value) {
            if($value instanceof \core_kernel_classes_Resource ){
                $result[] = $value->getUri();
            } else {
                $result[] = preg_replace('/^\*$/', '', $value);
            }
        }
        return count($result) === 1 ? $result[0] : $result;
    }
    
    /**
     * verify if value is valid
     * @param string $value
     * @return boolean
     * @throws exception\InvalidValueException
     */
    protected function validValue($value) {
        if(is_array($value)) {
                
                if(empty($value)) {
                    throw new exception\InvalidValueException('query filter value cann\'t be empty ');
                }

            } 
    }

    /**
     * serialyse a query for searchInstance
     * use for legacy search
     * @param core_kernel_persistence_smoothsql_SmoothModel $model
     * @param array $classUri
     * @param array $propertyFilters
     * @param boolean $and
     * @param boolean $isLikeOperator
     * @param string $lang
     * @param integer $offset
     * @param integer $limit
     * @param string $order
     * @param string $orderDir
     * @return string
     */
    public function getQuery(core_kernel_persistence_smoothsql_SmoothModel $model, $classUri, array $propertyFilters, $and = true, $isLikeOperator = true, $lang = '', $offset = 0, $limit = 0, $order = '', $orderDir = 'ASC')
    {
        $query = $this->getGateway()->query()->setLimit( $limit )->setOffset($offset );
        
        if(!empty($order)) {
            $query->sort([$order => strtolower($orderDir)]);
        }
        
        $this->setLanguage($query , $lang);

        $criteria = $query->newQuery()
                ->add('http://www.w3.org/1999/02/22-rdf-syntax-ns#type')
                ->in($classUri);
        
        $query->setCriteria($criteria);
        $count     = 0;
		$propertyFilters = FilterFactory::buildFilters($propertyFilters, $isLikeOperator);
		$maxLength = count($propertyFilters);
        foreach ($propertyFilters as $filter) {
        	$this->validValue($filter->getValue());

			$criteria->addCriterion($filter->getKey(), $filter->getOperator(), $this->parseValue($filter->getValue()));

			$orValues = $filter->getOrConditionValues();

			foreach ($orValues as $val) {
				$criteria->addOr($this->parseValue($val));
			}

			$count++;
			if($and === false && $maxLength > $count) {
				$criteria = $query->newQuery()
					->add('http://www.w3.org/1999/02/22-rdf-syntax-ns#type')
					->in($classUri);
				$query->setOr($criteria);
			}
		}

        $queryString = $this->getGateway()->serialyse($query)->getQuery();

        return $queryString;
    }
    
}
