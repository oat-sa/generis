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

use common_persistence_SqlPersistence;
use core_kernel_classes_ClassIterator;
use core_kernel_classes_Resource;
use Doctrine\DBAL\Connection;
use Iterator;
use oat\generis\model\GenerisRdf;
use oat\generis\model\OntologyAwareTrait;
use oat\generis\model\OntologyRdf;
use PDO;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * Iterates over the resources of file
 *
 * @author Aleksej Tikhanovich <aleksej@taotesting.com>
 */
class ResourceFileIterator implements Iterator
{
    use ServiceLocatorAwareTrait;
    use OntologyAwareTrait;

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

    /**
     * @var int
     */
    private $cacheSize;

    /**
     * @var array
     */
    public $failedResources = [];

    /**
     * @var int
     */
    private $lastId = 0;

    /**
     * @var int[]
     */
    private $corruptFileIds = [];

    /**
     * ResourceFileIterator constructor.
     * @param $classes
     * @param int $cacheSize
     * @throws \common_exception_Error
     */
    public function __construct($classes, $cacheSize = self::CACHE_SIZE)
    {
        $this->classIterator = new core_kernel_classes_ClassIterator($classes);
        $this->cacheSize = $cacheSize;
        $this->ensureNotEmpty();
    }

    /**
     * @inheritdoc
     * @throws \common_exception_Error
     */
    public function rewind()
    {
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

        $instance = $this->instanceCache[$this->currentInstance] ?: null;

        if (isset($instance['id'])) {
            $this->lastId = $instance['id'];
        }

        return $instance;
    }

    /**
     * @inheritdoc
     */
    public function key() {
        return $this->classIterator->key().'#'.$this->currentInstance;
    }

    /**
     * @inheritdoc
     * @throws \common_exception_Error
     */
    public function next()
    {
        $this->unmoved = false;
        if ($this->valid()) {
            $this->currentInstance++;
            if (!isset($this->instanceCache[$this->currentInstance])) {
                // try to load next block (unless we know it's empty)
                $remainingInstances = !$this->endOfClass && $this->load();

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
     * @throws \common_exception_Error
     */
    public function valid()
    {
        if ($this->instanceCache === null) {
            $this->ensureNotEmpty();
        }

        return $this->classIterator->valid();
    }

    /**
     * Ensure the class iterator is pointin to a non empty class
     * Loads the first resource block to test this
     * @throws \common_exception_Error
     */
    protected function ensureNotEmpty()
    {
        $this->currentInstance = 0;
        while ($this->classIterator->valid() && !$this->load()) {
            $this->classIterator->next();
        }
    }

    /**
     * Load instances into cache
     *
     * @return boolean
     * @throws \common_exception_Error
     */
    protected function load()
    {
        $results = $this->loadResources();
        $this->instanceCache = [];
        $this->currentInstance = 0;
        foreach ($results as $resourceData) {
            $this->instanceCache[] = $resourceData;
        }

        $this->endOfClass = count($results) < $this->cacheSize;

        return count($results) > 0;
    }

    /**
     * Load resources from storage
     *
     * @return mixed[][]
     * @throws \common_exception_Error
     */
    protected function loadResources()
    {
        $fileResources = $this->getFileResources();
        $parentData = $this->getFileParentResources($fileResources);

        $resourcesData = [];
        foreach ($fileResources as $resourceUri => $fileResource) {
            $resourcesData[$resourceUri]['resource'] = new core_kernel_classes_Resource($resourceUri);
            $resourcesData[$resourceUri]['id'] = $fileResource['id'];
            if (!isset($parentData[$resourceUri])) {
                $this->corruptFileIds[] = $fileResource['id'];
                continue;
            }
            $resourcesData[$resourceUri]['property'] = $parentData[$resourceUri]['predicate'];
            $resourcesData[$resourceUri]['parent'] = $parentData[$resourceUri]['subject'];
        }

        return $resourcesData;
    }

    /**
     * Get the file resources
     *
     * @return mixed[]
     */
    private function getFileResources()
    {
        /** @var common_persistence_SqlPersistence $persistence */
        $persistence = $this->getModel()->getPersistence();
        $platform = $persistence->getPlatForm();
        $subSelect = $platform->getQueryBuilder()
            ->select('DISTINCT id, subject')
            ->from('statements')
            ->where('predicate = :rdf_type')
            ->andWhere('object = :rdf_file_class')
            ->andWhere('id > ' . $this->lastId);

        if (!empty($this->corruptFileIds)) {
            $subSelect->andWhere('id NOT IN(:corrupt_ids)');
        }

        $select = $platform->getQueryBuilder()
            ->select('subject, id')
            ->from(sprintf('(%s)', $subSelect->getSQL()), 'unionq')
            ->setParameters([
                'rdf_type' => OntologyRdf::RDF_TYPE,
                'rdf_file_class' => GenerisRdf::CLASS_GENERIS_FILE
            ])
            ->groupBy('id, subject HAVING COUNT(*) >=1')->setMaxResults($this->cacheSize);

        if (!empty($this->corruptFileIds)) {
            $select->setParameter('corrupt_ids', array_keys($this->corruptFileIds), Connection::PARAM_INT_ARRAY);
        }

        return $select->execute()->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_UNIQUE);
    }

    /**
     * Get the parent resources for the loaded file resources
     *
     * @param mixed[] $fileResources
     * @return mixed[]
     */
    private function getFileParentResources($fileResources)
    {
        /** @var common_persistence_SqlPersistence $persistence */
        $persistence = $this->getModel()->getPersistence();
        $platform = $persistence->getPlatForm();
        $select = $platform->getQueryBuilder()
            ->select('object, predicate, subject, id')
            ->from('statements')
            ->andWhere('object IN(:resources) ')
            ->setParameter('resources',  array_keys($fileResources), Connection::PARAM_STR_ARRAY);

        return $select->execute()->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_UNIQUE);
    }
}
