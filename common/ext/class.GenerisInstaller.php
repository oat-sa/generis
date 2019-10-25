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
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 				 2013-2014 (update and modification) Open Assessment Technologies SA;
 */

use core_kernel_api_ModelFactory as ModelFactory;
use oat\generis\model\data\ModelManager;
use oat\generis\model\kernel\persistence\smoothsql\search\ComplexSearchService;
use oat\oatbox\service\ServiceManager;

/**
 * Custom extension installer for generis
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package generis
 *         
 */
class common_ext_GenerisInstaller extends common_ext_ExtensionInstaller
{

    /**
     * Setup the ontology configuration
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return mixed
     */
    public function install()
    {
        if ($this->extension->getId() != 'generis') {
            throw new common_ext_ExtensionException('Tried to install "' . $this->extension->getId() . '" extension using the GenerisInstaller');
        }
 
        $this->installLoadDefaultConfig();

        // Id of the writable model.
        $modelFactory = $this->getServiceManager()->get(ModelFactory::SERVICE_ID);
        $writableModelId = $modelFactory->getModelId(LOCAL_NAMESPACE);
        
        $model = new \core_kernel_persistence_smoothsql_SmoothModel(array(
            \core_kernel_persistence_smoothsql_SmoothModel::OPTION_PERSISTENCE => 'default',
            \core_kernel_persistence_smoothsql_SmoothModel::OPTION_READABLE_MODELS => [$writableModelId],
            \core_kernel_persistence_smoothsql_SmoothModel::OPTION_WRITEABLE_MODELS => [$writableModelId],
            \core_kernel_persistence_smoothsql_SmoothModel::OPTION_NEW_TRIPLE_MODEL => $writableModelId,
            \core_kernel_persistence_smoothsql_SmoothModel::OPTION_SEARCH_SERVICE => ComplexSearchService::SERVICE_ID,
            \core_kernel_persistence_smoothsql_SmoothModel::OPTION_CACHE_SERVICE => common_cache_Cache::SERVICE_ID
        ));
        $model->setServiceLocator($this->getServiceManager());
        ModelManager::setModel($model);
        
        $this->installOntology();
        // $this->installLocalData();
        // $this->installModuleModel();
        $this->installRegisterExt();
        
        common_cache_FileCache::singleton()->purge();
        
        $this->log('d', 'Installing custom script for extension ' . $this->extension->getId());
        $this->installCustomScript();
    }
}
