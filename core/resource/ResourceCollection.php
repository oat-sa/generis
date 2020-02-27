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
 * Copyright (c) 2019 (original work) Open Assessment Technologies SA;
 */

namespace oat\generis\model\resource;

use common_persistence_SqlPersistence;
use core_kernel_classes_Class;
use Countable;
use Iterator;
use common_persistence_sql_Filter as Filter;
use oat\generis\model\OntologyAwareTrait;
use oat\generis\model\OntologyRdf;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * Collection for resources.
 *
 * @author Martijn Swinkels <martijn@taotesting.com>
 */
class ResourceCollection implements Iterator, Countable
{
    use ServiceLocatorAwareTrait;
    use OntologyAwareTrait;

    const CACHE_SIZE = 100;

    /**
     * @var string[]
     */
    private $resources;

    /**
     * @var int
     */
    private $index = 0;

    /**
     * @var Filter
     */
    private $filter;

    /**
     * @var bool
     */
    private $endOfClass;

    /**
     * @var core_kernel_classes_Class
     */
    private $class;

    /**
     * @var int
     */
    private $cacheSize;

    /**
     * @var int
     */
    private $lastId = 0;

    /**
     * @var bool
     */
    private $classFilterSet = false;

    /**
     * @var int
     */
    private $limit;

    /**
     * ResourceCollection constructor.
     *
     * @param null|string|core_kernel_classes_Class $class
     * @param int $cacheSize
     */
    public function __construct($class = null, $cacheSize = self::CACHE_SIZE)
    {
        if ($class !== null) {
            $class = $this->getClass($class);
        }
        $this->class = $class;
        $this->filter = new Filter();
        $this->cacheSize = $cacheSize;
    }

    /**
     * Load a collection of resources
     *
     * @return bool
     */
    protected function load()
    {
        if ($this->resources !== null) {
            return $this->count() > 0;
        }

        $this->resources = null;
        $this->index = 0;
        $this->loadResources();

        return $this->count() > 0;
    }

    /**
     * Load resources from storage
     */
    private function loadResources()
    {
        if ($this->class !== null && $this->classFilterSet === false) {
            $this->addClassFilter();
        }

        /** @var common_persistence_SqlPersistence $persistence */
        $persistence = $this->getModel()->getPersistence();
        $platform = $persistence->getPlatForm();
        $query = $platform->getQueryBuilder()
            ->select('*')
            ->from('statements')
            ->andWhere('id > ' . $this->lastId)
            ->orderBy('id');

        if ($this->cacheSize > 0) {
            $query->setMaxResults($this->cacheSize);
        }

        $query = $this->filter->applyFilters($query);
        $results = $query->execute();

        if ($this->cacheSize > 0) {
            $this->endOfClass = $results->rowCount() < $this->cacheSize;
        }

        foreach ($results->fetchAll() as $result) {
            $this->resources[] = $result;
            $this->lastId = $result['id'] > $this->lastId ? $result['id'] : $this->lastId;
        }
    }

    /**
     * Add a filter to filter the resources we should iterate over.
     *
     * @param string $column
     * @param string $operator
     * @param mixed $value
     * @return ResourceCollection
     */
    public function addFilter($column, $operator, $value)
    {
        $this->filter->addFilter($column, $operator, $value);
        return $this;
    }

    /**
     * Add a type resource filter.
     *
     * @param string $type
     * @return ResourceCollection
     */
    public function addTypeFilter($type)
    {
        $this->filter->eq('predicate', OntologyRdf::RDF_TYPE)
                     ->eq('object', $type);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function count()
    {
        if (!is_array($this->resources) && !$this->resources instanceof Countable) {
            return 0;
        }

        return count($this->resources);
    }

    /**
     * @inheritdoc
     */
    public function current()
    {
        return $this->resources[$this->index];
    }

    /**
     * @inheritdoc
     */
    public function next()
    {
        $this->index++;
    }

    /**
     * @inheritdoc
     */
    public function key()
    {
        return $this->index;
    }

    /**
     * @inheritdoc
     */
    public function valid()
    {
        if ($this->resources === null) {
            return $this->load();
        }

        if ($this->endOfClass === false && !isset($this->resources[$this->index])) {
            if ($this->isLimitReached()) {
                $this->resources = null;
                return false;
            }
            $this->resources = null;
            return $this->load();
        }

        return isset($this->resources[$this->index]);
    }

    /**
     * @inheritdoc
     */
    public function rewind()
    {
        $this->index = 0;
    }

    /**
     * Activate a limitation of how many resources should be processed. This is different from cache size, since it
     * limits the amount of items that are processed in e.g. a foreach loop without loading remaining resources.
     */
    public function useLimit()
    {
        $this->limit = $this->cacheSize;
    }

    /**
     * Check if we reached the limit of the amount of items we should process.
     *
     * @return bool
     */
    private function isLimitReached()
    {
        return $this->limit !== null && $this->count() >= $this->limit;
    }

    /**
     * Check if the end of the current class is reached (no more records available)
     * @return bool
     */
    public function getEndReached()
    {
        return $this->endOfClass;
    }

    /**
     * Adds a filter for a class
     */
    private function addClassFilter()
    {
        $this->addTypeFilter($this->class->getUri());
        $this->classFilterSet = true;
    }
}
