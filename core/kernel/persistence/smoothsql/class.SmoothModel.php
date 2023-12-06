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
 * Copyright (c) (original work) 2015 Open Assessment Technologies SA
 *
 */

use oat\generis\model\data\ModelManager;
use oat\generis\model\data\Ontology;
use oat\generis\model\kernel\persistence\Cacheable;
use oat\generis\model\kernel\persistence\smoothsql\install\SmoothRdsModel;
use oat\generis\model\kernel\persistence\smoothsql\search\ComplexSearchService;
use oat\generis\persistence\sql\SchemaCollection;
use oat\generis\persistence\sql\SchemaProviderInterface;
use oat\oatbox\cache\PropertyCache;
use oat\oatbox\cache\SimpleCache;
use oat\oatbox\service\ConfigurableService;

/**
 * transitory model for the smooth sql implementation
 *
 * @author joel bout <joel@taotesting.com>
 * @package generis
 */
class core_kernel_persistence_smoothsql_SmoothModel extends ConfigurableService implements
    Ontology,
    SchemaProviderInterface,
    Cacheable
{
    public const OPTION_PERSISTENCE = 'persistence';
    public const OPTION_READABLE_MODELS = 'readable';
    public const OPTION_WRITEABLE_MODELS = 'writeable';
    public const OPTION_NEW_TRIPLE_MODEL = 'addTo';
    public const OPTION_SEARCH_SERVICE = 'search';

    public const DEFAULT_WRITABLE_MODEL = 1;
    public const DEFAULT_READ_ONLY_MODEL = 2;

    /**
     * Persistence to use for the smoothmodel
     *
     * @var common_persistence_SqlPersistence
     */
    private $persistence;

    public function getResource($uri)
    {
        $resource = new \core_kernel_classes_Resource($uri);
        $resource->setModel($this);
        return $resource;
    }

    public function getClass($uri)
    {
        $class = new \core_kernel_classes_Class($uri);
        $class->setModel($this);
        return $class;
    }

    public function getProperty($uri)
    {
        $property = new \core_kernel_classes_Property($uri);
        $property->setModel($this);
        return $property;
    }

    public function isWritable(core_kernel_classes_Resource $resource): bool
    {
        $writableModels = $this->getWritableModels();

        /** @var core_kernel_classes_Triple $triple */
        foreach ($resource->getRdfTriples() as $triple) {
            if (!in_array((int)$triple->modelid, $writableModels, true)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return common_persistence_SqlPersistence
     */
    public function getPersistence()
    {
        if (is_null($this->persistence)) {
            $this->persistence = $this->getServiceLocator()
                ->get(common_persistence_Manager::SERVICE_ID)
                ->getPersistenceById($this->getOption(self::OPTION_PERSISTENCE));
        }
        return $this->persistence;
    }

    public function getCache(): SimpleCache
    {
        return $this->getServiceLocator()->get(PropertyCache::SERVICE_ID);
    }

    /**
     * (non-PHPdoc)
     * @see \oat\generis\model\data\Model::getRdfInterface()
     */
    public function getRdfInterface()
    {
        return new core_kernel_persistence_smoothsql_SmoothRdf($this);
    }

    /**
     * (non-PHPdoc)
     * @see \oat\generis\model\data\Model::getRdfsInterface()
     */
    public function getRdfsInterface()
    {
        return new core_kernel_persistence_smoothsql_SmoothRdfs($this);
    }

    /**
     * @return ComplexSearchService
     */
    public function getSearchInterface()
    {
        $search = $this->getServiceLocator()->get($this->getOption(self::OPTION_SEARCH_SERVICE));
        $search->setModel($this);
        return $search;
    }

    // Manage the sudmodels of the smooth mode

    /**
     * Returns the id of the model to add to
     *
     * @return string
     */
    public function getNewTripleModelId()
    {
        return $this->getOption(self::OPTION_NEW_TRIPLE_MODEL);
    }

    public function getReadableModels()
    {
        return $this->getOption(self::OPTION_READABLE_MODELS);
    }

    public function getWritableModels()
    {
        return $this->getOption(self::OPTION_WRITEABLE_MODELS);
    }

    //
    // Deprecated functions
    //

    /**
     * Defines a model as readable
     *
     * @param string $id
     */
    public function addReadableModel($id)
    {

        common_Logger::i('ADDING MODEL ' . $id);

        $readables = $this->getOption(self::OPTION_READABLE_MODELS);
        $this->setOption(self::OPTION_READABLE_MODELS, array_unique(array_merge($readables, [$id])));

        // update in persistence
        ModelManager::setModel($this);
    }

    /**
     * Returns the submodel ids that are readable
     *
     * @deprecated
     * @return array()
     */
    public static function getReadableModelIds()
    {
        $model = ModelManager::getModel();
        if (!$model instanceof self) {
            throw new common_exception_Error(
                __FUNCTION__ . ' called on ' . get_class($model) . ' model implementation'
            );
        }
        return $model->getReadableModels();
    }

    /**
     * Returns the submodel ids that are updatable
     *
     * @deprecated
     * @return array()
     */
    public static function getUpdatableModelIds()
    {
        $model = ModelManager::getModel();
        if (!$model instanceof self) {
            throw new common_exception_Error(
                __FUNCTION__ . ' called on ' . get_class($model) . ' model implementation'
            );
        }
        return $model->getWritableModels();
    }

    public function provideSchema(SchemaCollection $schemaCollection)
    {
        $schema = $schemaCollection->getSchema($this->getOption(self::OPTION_PERSISTENCE));
        SmoothRdsModel::addSmoothTables($schema);
    }
}
