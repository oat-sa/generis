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

use oat\generis\model\data\Model;

/**
 * transitory model for the smooth sql implementation
 * 
 * @author joel bout <joel@taotesting.com>
 * @package generis
 */
class core_kernel_persistence_smoothsql_SmoothModel
    implements Model
{
    /**
     * Id of the persistence to be used for this data-model
     * Currently unused
     * 
     * @var string
     */
    private $persistanceId;
    
    /**
     * Persistence to use for the smoothmodel
     * 
     * @var common_persistence_SqlPersistence
     */
    private $persistance;
    
    
    private static $readableSubModels = null;
    
    private static $updatableSubModels = null;
    
    /**
     * Constructor of the smooth model, expects a persistence in the configuration
     * 
     * @param array $configuration
     * @throws common_exception_MissingParameter
     */
    public function __construct($configuration) {
        if (!isset($configuration['persistence'])) {
            throw new common_exception_MissingParameter('persistence', __CLASS__);
        }
        $this->persistanceId = $configuration['persistence']; 
        $this->persistance = common_persistence_SqlPersistence::getPersistence($configuration['persistence']);
    }
    
    /**
     * (non-PHPdoc)
     * @see \oat\generis\model\data\Model::getConfig()
     */
    public function getConfig() {
        return array(
            'persistence' => $this->persistanceId
        );
    }
    
    /**
     * (non-PHPdoc)
     * @see \oat\generis\model\data\Model::getRdfInterface()
     */
    public function getRdfInterface() {
        return new core_kernel_persistence_smoothsql_SmoothRdf($this->persistance);
    }
    
    /**
     * (non-PHPdoc)
     * @see \oat\generis\model\data\Model::getRdfsInterface()
     */
    public function getRdfsInterface() {
        return new core_kernel_persistence_smoothsql_SmoothRdfs();
    }
    
    // Manage the sudmodels of the smooth mode
    
    /**
     * Returns the submodel ids that are readable
     * 
     * @return array()
     */
    public static function getReadableModelIds() {
        if (is_null(self::$readableSubModels)) {
            self::loadReadableModelIds();
        }
        return self::$readableSubModels;
    }
    
    /**
     * Returns the submodel ids that are updatable
     * 
     * @return array()
     */
    public static function getUpdatableModelIds() {
        if (is_null(self::$updatableSubModels)) {
            $extensionManager = common_ext_ExtensionsManager::singleton();
            self::$updatableSubModels = array_keys($extensionManager->getUpdatableModels());
        }
        return self::$updatableSubModels;
    }
    
    /**
     * @ignore
     */
    private static function loadReadableModelIds()
    {
        $extensionManager = common_ext_ExtensionsManager::singleton();
        common_ext_NamespaceManager::singleton()->reset();
        
        $uris = array(LOCAL_NAMESPACE.'#');
        foreach ($extensionManager->getModelsToLoad() as $subModelUri){
            if(!preg_match("/#$/", $subModelUri)){
                $subModelUri .= '#';
            }
            $uris[] = $subModelUri;
        }

        $ids = array();
        foreach(common_ext_NamespaceManager::singleton()->getAllNamespaces() as $namespace){
            if(in_array($namespace->getUri(), $uris)){
                $ids[] = $namespace->getModelId();
            }
        }

        self::$readableSubModels = array_unique($ids);
    }
    
    /**
     * For hardification we need to ba able to bypass the model restriction
     * 
     * @param array $ids
     */
    public static function forceUpdatableModelIds($ids)
    {
        self::$updatableSubModels = $ids;
    }
    
    public static function forceReloadModelIds() {
        self::$updatableSubModels = null;
        self::$readableSubModels = null;
    }
    
    public function applyDiff(helpers_RdfDiff $diff) {
        $rdf = $this->getRdfInterface();
        foreach ($diff->getTriplesToRemove() as $triple) {
            $rdf->remove($triple);
        }
        foreach ($diff->getTriplesToAdd() as $triple) {
            $rdf->add($triple);
        }
    }
}