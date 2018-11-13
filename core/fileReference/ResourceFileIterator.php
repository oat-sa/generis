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
 * Copyright (c) 2018 (original work) Open Assessment Technologies SA;
 */

namespace oat\generis\model\fileReference;

use core_kernel_classes_Class;
use core_kernel_classes_ClassIterator;
use core_kernel_classes_Resource;
use Iterator;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * Iterates over the resources of file
 *
 * @author Aleksej Tikhanovich <aleksej@taotesting.com>
 */
class ResourceFileIterator implements Iterator
{
    use ServiceLocatorAwareTrait;

    const CACHE_SIZE = 100;

    /**
     * @var core_kernel_classes_ClassIterator
     */
    private $classIterator;

    /**
     * Id of the current instance
     *
     * @var int
     */
    private $currentInstance = 0;

    /**
     * List of resource uris currently being iterated over
     *
     * @var array
     */
    private $instanceCache = [];

    /**
     * Indicator whenever the end of  the current cache is also the end of the current class
     *
     * @var boolean
     */
    private $endOfClass = false;

    /**
     * Whenever we already moved the pointer, used to prevent unnecessary rewinds
     *
     * @var boolean
     */
    private $unmoved = true;

    private $cacheSize;

    private $isOffset;

    public $failedResources = [];

    /**
     * ResourceFileIterator constructor.
     * @param $classes
     * @param int $cacheSize
     * @param boolean $isOffset
     */
    public function __construct($classes, $cacheSize = self::CACHE_SIZE, $isOffset = true) {
        $this->classIterator = new core_kernel_classes_ClassIterator($classes);
        $this->ensureNotEmpty();
        $this->cacheSize = $cacheSize;
        $this->isOffset = $isOffset;
    }

    /**
     * @inheritdoc
     */
    public function rewind() {
        if (!$this->unmoved) {
            $this->classIterator->rewind();
            $this->ensureNotEmpty();
            $this->unmoved = true;
        }
    }

    /**
     * @inheritdoc
     * @throws \common_exception_Error
     */
    public function current()
    {
        if (empty($this->instanceCache)) {
            $this->ensureNotEmpty();
        }
        return isset($this->instanceCache[$this->currentInstance]) ?
            $this->createDocument(new core_kernel_classes_Resource($this->instanceCache[$this->currentInstance])) :
            null;
    }

    /**
     * @inheritdoc
     */
    public function key() {
        return $this->classIterator->key().'#'.$this->currentInstance;
    }

    /**
     * @inheritdoc
     */
    public function next() {
        $this->unmoved = false;
        if ($this->valid()) {
            $this->currentInstance++;
            if (!isset($this->instanceCache[$this->currentInstance])) {
                // try to load next block (unless we know it's empty)
                $remainingInstances = !$this->endOfClass && $this->load($this->classIterator->current(), $this->currentInstance);

                // endOfClass or failed loading
                if (!$remainingInstances) {
                    $this->classIterator->next();
                    $this->ensureNotEmpty();
                }
            }
        }
    }

    /**
     * While there are remaining classes there are instances to load
     *
     * @see Iterator::valid()
     */
    public function valid() {
        if ($this->instanceCache === null) {
            $this->ensureNotEmpty();
        }
        return $this->classIterator->valid();
    }

    /**
     * Ensure the class iterator is pointin to a non empty class
     * Loads the first resource block to test this
     */
    protected function ensureNotEmpty() {
        $this->currentInstance = 0;
        while ($this->classIterator->valid() && !$this->load($this->classIterator->current(), 0)) {
            $this->classIterator->next();
        }
    }

    /**
     * Load instances into cache
     *
     * @param core_kernel_classes_Class $class
     * @param int $offset
     * @return boolean
     */
    protected function load(core_kernel_classes_Class $class, $offset)
    {
        $results = $this->loadResources($class, $offset);
        $this->instanceCache = [];
        foreach ($results as $resource) {
            $this->instanceCache[$offset] = $resource->getUri();
            $offset++;
        }

        $this->endOfClass = count($results) < $this->cacheSize;

        return count($results) > 0;
    }

    /**
     * Load resources from storage
     *
     * @param core_kernel_classes_Class $class
     * @param integer $offset
     * @return core_kernel_classes_Resource[]
     */
    protected function loadResources(core_kernel_classes_Class $class, $offset)
    {
        return $class->searchInstances([], [
            'recursive' => false,
            'limit' => $this->cacheSize,
            'offset' => $this->isOffset ? $offset : 0,
        ]);
    }

    /**
     * @param core_kernel_classes_Resource $resource
     * @return array
     */
    protected function createDocument(core_kernel_classes_Resource $resource)
    {
        return [
            $resource->getUri() => [
                'properties' => $this->getPropertiesForResource($resource),
                'resource' => $resource
            ]
        ];
    }

    /**
     * Get the properties that are using this resource
     *
     * @param core_kernel_classes_Resource $fileResource
     * @return string[][]
     */
    public function getPropertiesForResource($fileResource)
    {
        $fileResourceUri = $fileResource->getUri();
        $sql = "SELECT predicate FROM statements WHERE object = '" . $fileResourceUri . "'";
        $persistence = $fileResource->getModel()->getPersistence();

        return $persistence->query($sql)->fetchAll();
    }
}
