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
use oat\oatbox\Configurable;

/**
 * transitory model for the smooth sql implementation
 * 
 * @author joel bout <joel@taotesting.com>
 * @package generis
 */
class core_kernel_persistence_smoothsql_SmoothModel extends Configurable
    implements Model
{
    /**
     * Persistence to use for the smoothmodel
     * 
     * @var common_persistence_SqlPersistence
     */
    private $persistence;
    
    private static $readableSubModels = null;
    
    private static $updatableSubModels = null;
    
    public function getPersistence() {
        if (is_null($this->persistence)) {
            $this->persistence = common_persistence_SqlPersistence::getPersistence($this->getOption('persistence'));
        }
        return $this->persistence;
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
    
    // Manage the sudmodels of the smooth mode
    
    /**
     * Returns the submodel ids that are readable
     * 
     * @return array()
     */
    public static function getReadableModelIds() {
        if (is_null(self::$readableSubModels)) {
            self::$readableSubModels = self::loadReadableModelIds();
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
            self::$updatableSubModels = self::loadWriteableModelIds();
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

        return array_unique($ids);
    }
    
    private static function loadWriteableModelIds()
    {
        $extensionManager = common_ext_ExtensionsManager::singleton();
        return array_keys($extensionManager->getUpdatableModels());
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