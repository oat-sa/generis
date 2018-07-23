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
use oat\oatbox\service\exception\InvalidServiceManagerException;
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

        $this->setupServiceManager($this->getConfigPath());
        $this->installFilesystem();

        return new Report(Report::TYPE_SUCCESS, 'Oatbox installed successfully');
    }

    /**
     * Setup the service manager with configuration driver associated to config path
     *
     * @param $configPath
     * @return ServiceManager
     * @throws \common_exception_Error
     * @throws InvalidServiceManagerException
     */
    public function setupServiceManager($configPath)
    {
        try {
            $this->getServiceManager();
        } catch (InvalidServiceManagerException $e) {
            if (! \helpers_File::emptyDirectory($configPath, true)) {
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
     * Install the filesystem service if not already installed
     *
     * @throws InvalidService If installed filesystem is not a FileSystemService
     * @throws InvalidServiceManagerException
     * @throws \common_Exception
     */
    protected function installFilesystem()
    {
        try {
            if(! ($this->getServiceManager()->get(FileSystemService::SERVICE_ID) instanceof FileSystemService)) {
                throw new InvalidService('Your service must be a oat\oatbox\filesystem\FileSystemService');
            }
        } catch(ServiceNotFoundException $e){
            $fileSystemService = new FileSystemService(array(FileSystemService::OPTION_FILE_PATH => $this->getOption('file_path')));
            $this->getServiceManager()->register(FileSystemService::SERVICE_ID, $fileSystemService);
        }
    }

    /**
     * Validate require option e.q. file_path & root_path
     *
     * @throws \common_exception_MissingParameter
     */
    protected function validateOptions()
    {
        if (!$this->hasOption('root_path') || empty($this->getOption('root_path'))) {
            throw new \common_exception_MissingParameter('root_path', __CLASS__);
        }
        if (!$this->hasOption('file_path') || empty($this->getOption('file_path'))) {
            throw new \common_exception_MissingParameter('file_path', __CLASS__);
        }
    }

    /**
     * Get the path where to install config
     *
     * @return string
     */
    protected function getConfigPath()
    {
        if ($this->hasOption('config_path') && ! empty($this->getOption('config_path'))) {
            return $this->getOption('config_path');
        }
        return rtrim($this->getOption('root_path'), '/\\') . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR;
    }
    
}
