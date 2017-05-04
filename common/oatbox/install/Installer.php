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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */
namespace oat\oatbox\install;

use oat\oatbox\service\ConfigurableService;
use oat\oatbox\service\exception\InvalidService;
use oat\oatbox\service\ServiceConfigDriver;
use oat\oatbox\service\ServiceManager;
use oat\oatbox\filesystem\FileSystemService;
use oat\oatbox\service\ServiceNotFoundException;
use common_report_Report as Report;

/**
 * A service to install oatbox functionality
 * 
 * Sets up:
 *   configuration
 *   filesystems
 */
class Installer extends ConfigurableService
{
    /**
     * run the install
     */
    public function install()
    {
        $this->validateOptions();

        $this->setupServiceManager($this->getOption('config_path'));
        $this->installExtensionManager();
        $this->installFilesystem();

        return new Report(Report::TYPE_SUCCESS, 'Oatbox installed successfully');
    }

    /**
     * Setup the service manager with configuration driver associated to config path
     *
     * @param $configPath
     * @return ServiceManager
     * @throws \common_exception_Error
     */
    public function setupServiceManager($configPath)
    {
        if (is_null($this->getServiceManager())) {
            if (is_dir($configPath) && !\helpers_File::emptyDirectory($configPath, true)) {
                throw new \common_exception_Error('Unable to empty ' . $configPath . ' folder.');
            }
            $driver = new ServiceConfigDriver();
            $configService = $driver->connect('config', array(
                'dir' => $configPath,
                'humanReadable' => true
            ));

            $this->setServiceManager(new ServiceManager($configService));
        }

        return $this->getServiceManager();
    }

    /**
     * Install the extension manager if not already installed
     *
     * @throws InvalidService If installed extensionManager is not an \common_ext_ExtensionsManager
     */
    protected function installExtensionManager()
    {
        try{
            if(! ($this->getServiceManager()->get(\common_ext_ExtensionsManager::SERVICE_ID) instanceof \common_ext_ExtensionsManager)){
                throw new InvalidService('Your service must be a \common_ext_ExtensionsManager');
            }
        } catch(ServiceNotFoundException $e){
            $this->getServiceManager()->register(\common_ext_ExtensionsManager::SERVICE_ID, new \common_ext_ExtensionsManager());
        }
    }

    /**
     * Install the filesystem service if not already installed
     *
     * @throws InvalidService If installed filesystem is not a FileSystemService
     */
    protected function installFilesystem()
    {
        try{
            if(! ($this->getServiceManager()->get(FileSystemService::SERVICE_ID) instanceof FileSystemService)){
                throw new InvalidService('Your service must be a oat\oatbox\filesystem\FileSystemService');
            }
        } catch(ServiceNotFoundException $e){
            $fileSystemService = new FileSystemService(array(FileSystemService::OPTION_FILE_PATH => $this->getOption('file_path')));
            $this->getServiceManager()->register(FileSystemService::SERVICE_ID, $fileSystemService);
        }
    }

    /**
     * Validate require option e.q. file_path & config_path
     *
     * @throws \common_exception_MissingParameter
     */
    protected function validateOptions()
    {
        if (!$this->hasOption('file_path') || empty($this->getOption('file_path'))) {
            throw new \common_exception_MissingParameter('file_path', __CLASS__);
        }
        if (!$this->hasOption('config_path') || empty($this->getOption('config_path'))) {
            throw new \common_exception_MissingParameter('config_path', __CLASS__);
        }
    }

}
