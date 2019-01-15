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

use ArrayIterator;
use common_persistence_SqlPersistence;
use core_kernel_classes_ClassIterator;
use core_kernel_classes_Resource;
use Countable;
use IteratorAggregate;
use oat\generis\Helper\Filter;
use oat\generis\model\OntologyAwareTrait;
use oat\generis\model\OntologyRdf;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * Collection for resources.
 *
 * @author Martijn Swinkels <martijn@taotesting.com>
 */
class ResourceCollection implements IteratorAggregate, Countable
{

    use ServiceLocatorAwareTrait;
    use OntologyAwareTrait;

    /**
     * @var core_kernel_classes_Resource[]
     */
    private $resources;

    /**
     * @var Filter
     */
    private $filter;

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
    private $endOfClass;

    /**
     * ResourceFileIterator constructor.
     *
     * @param int $cacheSize
     */
    public function __construct($cacheSize = 100)
    {
        $this->cacheSize = $cacheSize;
        $this->filter = new Filter();
    }

    /**
     * Load instances into cache
     *
     * @return bool
     */
    protected function load()
    {
        if ($this->resources !== null) {
            return count($this->resources) > 0;
        }

        $this->resources = [];
        $this->loadResources();

        if ($this->cacheSize > 0) {
            $this->endOfClass = count($this->resources) < $this->cacheSize;
        }

        return count($this->resources) > 0;
    }

    /**
     * Load resources from storage
     */
    private function loadResources()
    {
        /** @var common_persistence_SqlPersistence $persistence */
        $persistence = $this->getModel()->getPersistence();
        $platform = $persistence->getPlatForm();
        $query = $platform->getQueryBuilder()
            ->select('*')
            ->from('statements');
        $query = $this->filter->applyFilters($query);
        $query->andWhere('id > ' . $this->lastId);

        if ($this->cacheSize > 0) {
            $query->setMaxResults($this->cacheSize);
        }

        $results = $query->execute()->fetchAll();

        foreach ($results as $result) {
            $this->resources[$result['subject']] = $result;
            $this->lastId = $result['id'];
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
        $this->filter->addFilter('predicate', Filter::OP_EQ, OntologyRdf::RDF_TYPE);
        $this->filter->addFilter('object', Filter::OP_EQ, $type);
        return $this;
    }

    /**
     * Return an array containing the uris of the found resources
     *
     * @return string[]
     */
    public function getUris()
    {
        $this->load();
        return array_keys($this->resources);
    }

    /**
     * @inheritdoc
     */
    public function getIterator()
    {
        $this->load();
        return new ArrayIterator($this->resources);
    }

    /**
     * @inheritdoc
     */
    public function count()
    {
        $this->load();
        return count($this->resources);
    }

    /**
     * Checks if there are more items available.
     *
     * @return bool
     */
    public function endReached()
    {
        $this->load();
        return $this->endOfClass;
    }

    /**
     * Load the next block in the collection
     */
    public function nextBlock()
    {
        $this->resources = null;
        $this->load();
    }
}
