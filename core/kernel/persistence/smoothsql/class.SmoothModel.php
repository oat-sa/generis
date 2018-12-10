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
use oat\oatbox\service\ConfigurableService;
use oat\generis\model\kernel\persistence\smoothsql\search\ComplexSearchService;
use oat\generis\model\data\Ontology;

/**
 * transitory model for the smooth sql implementation
 * 
 * @author joel bout <joel@taotesting.com>
 * @package generis
 */
class core_kernel_persistence_smoothsql_SmoothModel extends ConfigurableService
    implements Ontology
{
    const OPTION_PERSISTENCE = 'persistence';
    const OPTION_READABLE_MODELS = 'readable';
    const OPTION_WRITEABLE_MODELS = 'writeable';
    const OPTION_NEW_TRIPLE_MODEL = 'addTo';
    const OPTION_SEARCH_SERVICE = 'search';

    /**
     * Cache service to use
     * @var string
     */
    const OPTION_CACHE_SERVICE = 'cache';

    /**
     * Persistence to use for the smoothmodel
     * 
     * @var common_persistence_SqlPersistence
     */
    private $persistence;
    
    private $cache;

    private static $readableSubModels = null;
    
    private static $updatableSubModels = null;
    
    function getResource($uri) {
        $resource = new \core_kernel_classes_Resource($uri);
        $resource->setModel($this);
        return $resource;
    }

    function getClass($uri) {
        $class = new \core_kernel_classes_Class($uri);
        $class->setModel($this);
        return $class;
    }

    function getProperty($uri) {
        $property = new \core_kernel_classes_Property($uri);
        $property->setModel($this);
        return $property;
    }

    /**
     * @return common_persistence_SqlPersistence
     */
    public function getPersistence() {
        if (is_null($this->persistence)) {
            $this->persistence = $this->getServiceLocator()->get(common_persistence_Manager::SERVICE_ID)->getPersistenceById($this->getOption(self::OPTION_PERSISTENCE));
        }
        return $this->persistence;
    }

    /**
     * @return common_cache_Cache
     */
    public function getCache() {
        if (is_null($this->cache)) {
            $this->cache = $this->getServiceLocator()->get($this->getOption(self::OPTION_CACHE_SERVICE));
        }
        return $this->cache;
    }

    /**
     * (non-PHPdoc)
     * @see \oat\generis\model\data\Model::getRdfInterface()
     */
    public function getRdfInterface() {
        return new core_kernel_persistence_smoothsql_SmoothRdf($this);
    }
    
    /**
     * (non-PHPdoc)
     * @see \oat\generis\model\data\Model::getRdfsInterface()
     */
    public function getRdfsInterface() {
        return new core_kernel_persistence_smoothsql_SmoothRdfs($this);
    }
    
    /**
     * @return ComplexSearchService
     */
    public function getSearchInterface() {
        $search = $this->getServiceManager()->get($this->getOption(self::OPTION_SEARCH_SERVICE));
        $search->setModel($this);
        return $search;
    }

    // Manage the sudmodels of the smooth mode
    
    /**
     * Returns the id of the model to add to
     * 
     * @return string
     */
    public function getNewTripleModelId() {
        return $this->getOption(self::OPTION_NEW_TRIPLE_MODEL);
    }
    
    public function getReadableModels() {
        return $this->getOption(self::OPTION_READABLE_MODELS);
    }

    public function getWritableModels() {
        return $this->getOption(self::OPTION_WRITEABLE_MODELS);
    }
    
    /**
     * Defines a model as readable
     *
     * @param string $id
     */
    public function addReadableModel($id) {
    
        common_Logger::i('ADDING MODEL '.$id);
    
        $readables = $this->getOption(self::OPTION_READABLE_MODELS);
        $this->setOption(self::OPTION_READABLE_MODELS, array_unique(array_merge($readables, array($id))));
    
        // update in persistence
        ModelManager::setModel($this);
    }
    
    //
    // Deprecated functions
    // 
    
    /**
     * Returns the submodel ids that are readable
     * 
     * @deprecated
     * @return array()
     */
    public static function getReadableModelIds() {
        $model = ModelManager::getModel();
        if (!$model instanceof self) {
            throw new common_exception_Error(__FUNCTION__.' called on '.get_class($model).' model implementation');
        }
        return $model->getReadableModels();
    }
    
    /**
     * Returns the submodel ids that are updatable
     * 
     * @deprecated
     * @return array()
     */
    public static function getUpdatableModelIds() {
        $model = ModelManager::getModel();
        if (!$model instanceof self) {
            throw new common_exception_Error(__FUNCTION__.' called on '.get_class($model).' model implementation');
        }
        return $model->getWritableModels();
    }
}