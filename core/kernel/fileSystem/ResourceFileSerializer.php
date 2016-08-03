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
 * Copyright (c) 2016 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */

namespace oat\generis\model\kernel\fileSystem;

use oat\generis\model\OntologyAwareTrait;
use oat\oatbox\filesystem\Directory;
use oat\oatbox\filesystem\File;
use oat\oatbox\filesystem\FileSystemService;
use oat\oatbox\service\ConfigurableService;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

class ResourceFileSerializer extends ConfigurableService
    implements FileReferenceSerializer, ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;
    use OntologyAwareTrait;

    const RESOURCE_FILE_PATH            = 'path';
    const RESOURCE_FILE_FILESYSTEM_URI  = 'fileSystemUri';

    /**
     * @see parent::serialize
     */
    public function serialize($abstraction)
    {
        $fileClass = $this->getClass(CLASS_GENERIS_FILE);

        if ($abstraction instanceof File) {
            $filesystem = $abstraction->getFileSystem();
            $filePath = $this->getRootDirectory($filesystem->getId())->getRelPath($abstraction);

            $resource = $fileClass->createInstanceWithProperties(array(
                PROPERTY_FILE_FILENAME => $abstraction->getBasename(),
                PROPERTY_FILE_FILEPATH => $filePath,
                PROPERTY_FILE_FILESYSTEM => $this->getResource($filesystem->getId())
            ));
        } elseif ($abstraction instanceof Directory) {
            $filesystem = $abstraction->getFileSystem();
            $filePath = $this->getRootDirectory($filesystem->getId())->getRelPath($abstraction);

            $resource = $fileClass->createInstanceWithProperties(array(
                PROPERTY_FILE_FILENAME => '',
                PROPERTY_FILE_FILEPATH => $filePath,
                PROPERTY_FILE_FILESYSTEM => $this->getResource($filesystem->getId())
            ));
        } else {
            throw new \common_Exception(__CLASS__ . '::' . __FUNCTION__ . ' expects parameter to be an instance of Directory or File');
        }

        return $resource->getUri();
    }

    /**
     * This implementation use \core_kernel_file_File URI as serial
     *
     * @see parent::unserialize
     */
    public function unserialize($serial)
    {
        $properties = $this->getResourceFilePropertiesValues($serial, true);

        return $this->getRootDirectory($properties[self::RESOURCE_FILE_FILESYSTEM_URI])
            ->getDirectory($properties[self::RESOURCE_FILE_PATH]);
    }

    /**
     * This implementation use \core_kernel_file_File URI as serial
     *
     * @see parent::unserializeDirectory
     */
    public function unserializeDirectory($serial)
    {
        $properties = $this->getResourceFilePropertiesValues($serial);

        return $this->getRootDirectory($properties[self::RESOURCE_FILE_FILESYSTEM_URI])
            ->getDirectory($properties[self::RESOURCE_FILE_PATH]);
    }

    /**
     * This implementation use \core_kernel_file_File URI as serial
     *
     * @see parent::cleanUp
     */
    public function cleanUp($serial)
    {
        $resourceFile = $this->getResource($serial);
        $file = new \core_kernel_file_File($resourceFile);
        return $file->delete();
    }

    /**
     * Return root directory represented by the given uri
     *
     * @return Directory
     */
    protected function getRootDirectory($uri)
    {
        return $this->getServiceLocator()->get(FileSystemService::SERVICE_ID)->getDirectory($uri);
    }

    /**
     * Return an array with filesystem uri and path following serial
     * Serial is Resource file uri, data are extracted from database
     *
     * This implementation use \core_kernel_file_File URI as serial
     *
     * @param $serial
     * @param bool $withFilename
     * @return array
     * @throws \common_exception_InvalidArgumentType
     */
    protected function getResourceFilePropertiesValues($serial, $withFilename=false)
    {
        $file = $this->getResource($serial);

        $propertiesDefinition = array(
            $this->getProperty(PROPERTY_FILE_FILEPATH),
            $this->getProperty(PROPERTY_FILE_FILESYSTEM)
        );

        if ($withFilename) {
            array_push($propertiesDefinition, $this->getProperty(PROPERTY_FILE_FILENAME));
        }

        $propertiesValues = $file->getPropertiesValues($propertiesDefinition);

        $properties = [];

        $fileSystemProperty	=  current($propertiesValues[PROPERTY_FILE_FILESYSTEM]);
        if ($fileSystemProperty instanceof \core_kernel_classes_Resource) {
            $properties[self::RESOURCE_FILE_FILESYSTEM_URI] = $fileSystemProperty->getUri();
        } else {
            $properties[self::RESOURCE_FILE_FILESYSTEM_URI] = $fileSystemProperty->literal;
        }

        $filePath = current($propertiesValues[PROPERTY_FILE_FILEPATH])->literal;
        $filePath = str_replace(DIRECTORY_SEPARATOR, '/', $filePath);
        $filePath = trim($filePath, '/');

        if ($withFilename) {
            $fileName = current($propertiesValues[PROPERTY_FILE_FILENAME])->literal;
            $fileName = str_replace(DIRECTORY_SEPARATOR, '/', $fileName);
            $fileName = ltrim($fileName, '/');

            $properties[self::RESOURCE_FILE_PATH] = $filePath . '/' . trim($fileName, '\\/');
        } else {
            $properties[self::RESOURCE_FILE_PATH] = $filePath;
        }

        return $properties;
    }
}