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
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under
 * the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project
 * TAO-SUSTAIN & TAO-DEV);
 *
 */

use oat\generis\model\data\ModelIdManager;
use oat\generis\model\data\ModelIdNotFoundException;
use oat\generis\model\data\ModelManager;

/**
 * Uninstall of extensions
 *
 * @access public
 * @author lionel.lecaque@tudor.lu
 * @package common
 * @see @license  GNU General Public (GPL) Version 2 http://www.opensource.org/licenses/gpl-2.0.php
 * @subpackage ext
 */
class common_ext_ExtensionUninstaller extends common_ext_ExtensionHandler
{

    /**
     * uninstall an extension
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     *
     * @return boolean
     *
     * @throws ModelIdNotFoundException
     * @throws common_Exception
     * @throws common_ext_ManifestNotFoundException
     */
    public function uninstall()
    {
        common_Logger::i('Uninstalling '.$this->extension->getId(), 'UNINSTALL');

        // uninstall possible
        if (is_null($this->extension->getManifest()->getUninstallData())) {
            throw new common_Exception(
                'Problem uninstalling extension ' . $this->extension->getId() . ' : Uninstall not supported'
            );
        }

        // installed?
        if (!common_ext_ExtensionsManager::singleton()->isInstalled($this->extension->getId())) {
            throw new common_Exception(
                'Problem uninstalling extension ' . $this->extension->getId() . ' : Not installed'
            );
        }

        // check dependcies
        if (helpers_ExtensionHelper::isRequired($this->extension)) {
            throw new common_Exception(
                'Problem uninstalling extension ' . $this->extension->getId() .' : Still required'
            );
        };

        common_Logger::d('uninstall script for ' . $this->extension->getId());
        $this->uninstallScripts();

        $modelId = $this->getModelId();

        $this->uninstallRdf($modelId);
        $this->cleanUpPermissions($modelId);
        $this->unregisterModelId($this->extension->getId());

        // hook
        $this->extendedUninstall();

        common_Logger::d('unregister extension ' . $this->extension->getId());
        $this->unregister();

        // we purge the whole cache.
        $cache = common_cache_FileCache::singleton();
        $cache->purge();

        common_Logger::i('Uninstalled ' . $this->extension->getId());
        return true;
    }

    /**
     * Unregisters the Extension from the extensionManager
     *
     * @access protected
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     *
     * @throws common_exception_Error
     * @throws common_ext_ExtensionException
     */
    protected function unregister()
    {
        common_ext_ExtensionsManager::singleton()->unregisterExtension($this->extension);
    }

    /**
     * Executes uninstall scripts
     * specified in the Manifest
     *
     * @access protected
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     *
     * @throws common_ext_InstallationException
     * @throws common_ext_ManifestNotFoundException
     */
    protected function uninstallScripts()
    {
        $data = $this->extension->getManifest()->getUninstallData();
        if (!is_null($data) && isset($data['php']) && is_array($data['php'])) {
            foreach ($data['php'] as $script) {
                $this->runExtensionScript($script);
            }
        }
    }

    /**
     * Hook to extend the uninstall procedure
     */
    public function extendedUninstall()
    {
        return;
    }

    /**
     * @param int $modelId
     */
    public function uninstallRdf($modelId)
    {
        common_Logger::d('Uninstall RDF. modelId = ' . $modelId);
        try {
            ModelManager::getModel()->getRdfInterface()->removeByModelId($modelId);
        } catch (common_exception_InconsistentData $e) {
            common_Logger::e($e->getMessage());
        }
    }

    /**
     * Remove modelId from readable and writable list.
     *
     * @param int $modelId
     */
    public function cleanUpPermissions($modelId)
    {
        common_Logger::d('Cleaning up permissions. modelId = ' . $modelId);
        try {
            ModelManager::getModel()->cleanUpPermissions($modelId);
        } catch (common_exception_InconsistentData $e) {
            common_Logger::e($e->getMessage());
        }
    }

    /**
     * Get modelId for specified extension.
     *
     * @return int
     *
     * @throws ModelIdNotFoundException
     */
    protected function getModelId()
    {
        /** @var ModelIdManager $modelIdManager */
        $modelIdManager = $this->getServiceManager()->get(ModelIdManager::SERVICE_ID);

        return $modelIdManager->getModelId($this->extension->getId());
    }

    /**
     * @param string $extensionId
     *
     * @throws common_Exception
     */
    protected function unregisterModelId($extensionId)
    {
        /** @var ModelIdManager $modelIdManager */
        $modelIdManager = $this->getServiceManager()->get(ModelIdManager::SERVICE_ID);

        $modelIdManager->removeKey($extensionId);
    }
}
