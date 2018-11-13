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
 * Copyright (c) 2017 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */

namespace oat\generis\model\fileReference;

use oat\generis\model\GenerisRdf;
use oat\generis\model\OntologyAwareTrait;
use oat\oatbox\filesystem\Directory;
use oat\oatbox\filesystem\File;
use oat\oatbox\filesystem\FileSystemService;
use oat\oatbox\service\ConfigurableService;

class ResourceFileSerializer extends ConfigurableService implements FileReferenceSerializer
{
    use OntologyAwareTrait;

    const RESOURCE_FILE_FILESYSTEM_URI = 'fileSystemUri';
    const RESOURCE_FILE_PATH = 'path';
    const RESOURCE_FILE_NAME = 'fileName';

    /**
     * @see FileReferenceSerializer::serialize
     */
    public function serialize($abstraction)
    {
        $fileClass = $this->getClass(GenerisRdf::CLASS_GENERIS_FILE);

        if ($abstraction instanceof File) {
            $filename = $abstraction->getBasename();
            $filePath = dirname($abstraction->getPrefix());
        } elseif ($abstraction instanceof Directory) {
            $filename = '';
            $filePath = $abstraction->getPrefix();
        } else {
            throw new FileSerializerException(
                __CLASS__.'::'.__FUNCTION__.' expects parameter to be an instance of Directory or File'
            );
        }

        $resource = $fileClass->createInstanceWithProperties(
            [
                GenerisRdf::PROPERTY_FILE_FILENAME => $filename,
                GenerisRdf::PROPERTY_FILE_FILEPATH => $filePath,
                GenerisRdf::PROPERTY_FILE_FILESYSTEM => $this->getResource($abstraction->getFileSystemId()),
            ]
        );

        return $resource->getUri();
    }

    /**
     * This implementation uses resource URI as serial
     *
     * @see FileReferenceSerializer::unserialize
     */
    public function unserialize($serial)
    {
        $properties = $this->getResourceFilePropertiesValues($serial);
        $dir = $this->getRootDirectory($properties[self::RESOURCE_FILE_FILESYSTEM_URI]);

        return (isset($properties[self::RESOURCE_FILE_NAME]) && !empty($properties[self::RESOURCE_FILE_NAME]))
            ? $dir->getFile($properties[self::RESOURCE_FILE_PATH].'/'.$properties[self::RESOURCE_FILE_NAME])
            : $dir->getDirectory($properties[self::RESOURCE_FILE_PATH]);
    }

    /**
     * This implementation uses resource URI as serial
     *
     * @see FileReferenceSerializer::unserializeFile
     */
    public function unserializeFile($serial)
    {
        $properties = $this->getResourceFilePropertiesValues($serial);

        return $this->getRootDirectory($properties[self::RESOURCE_FILE_FILESYSTEM_URI])
            ->getFile($properties[self::RESOURCE_FILE_PATH].'/'.$properties[self::RESOURCE_FILE_NAME]);
    }

    /**
     * This implementation uses resource URI as serial
     *
     * @see FileReferenceSerializer::unserializeDirectory
     */
    public function unserializeDirectory($serial)
    {
        $properties = $this->getResourceFilePropertiesValues($serial);

        return $this->getRootDirectory($properties[self::RESOURCE_FILE_FILESYSTEM_URI])
            ->getDirectory($properties[self::RESOURCE_FILE_PATH]);
    }

    /**
     * This implementation uses resource URI as serial
     *
     * @see FileReferenceSerializer::cleanUp
     */
    public function cleanUp($serial)
    {
        $resourceFile = $this->getResource($serial);
        $file = new \core_kernel_classes_Resource($resourceFile);

        return $file->delete();
    }

    /**
     * Return root directory represented by the given uri
     *
     * @return Directory
     */
    protected function getRootDirectory($id)
    {
        return $this->getServiceLocator()->get(FileSystemService::SERVICE_ID)->getDirectory($id);
    }

    /**
     * Return an array with filesystem uri and path following serial
     * Serial is Resource file uri, data are extracted from database
     *
     * This implementation uses resource URI as serial
     *
     * @param $serial
     * @return array
     * @throws \common_exception_InvalidArgumentType
     * @throws FileSerializerException
     */
    protected function getResourceFilePropertiesValues($serial)
    {
        $file = $this->getResource($serial);

        if (!$file->exists()) {
            throw new \common_exception_NotFound('File reference serial "'. $serial .'" not exist as resource');
        }

        $properties = [];
        $propertiesDefinition = [
            $this->getProperty(GenerisRdf::PROPERTY_FILE_FILEPATH),
            $this->getProperty(GenerisRdf::PROPERTY_FILE_FILESYSTEM),
            $this->getProperty(GenerisRdf::PROPERTY_FILE_FILENAME),
        ];

        $propertiesValues = $file->getPropertiesValues($propertiesDefinition);
        $fileSystemProperty = current($propertiesValues[GenerisRdf::PROPERTY_FILE_FILESYSTEM]);
        $properties[self::RESOURCE_FILE_FILESYSTEM_URI] = $fileSystemProperty instanceof \core_kernel_classes_Resource
            ? $fileSystemProperty->getUri()
            : $fileSystemProperty->literal;

        $filePath = current($propertiesValues[GenerisRdf::PROPERTY_FILE_FILEPATH])->literal;
        $properties[self::RESOURCE_FILE_PATH] = trim(str_replace(DIRECTORY_SEPARATOR, '/', $filePath), '/');

        if (!empty($propertiesValues[GenerisRdf::PROPERTY_FILE_FILENAME])) {
            $fileName = current($propertiesValues[GenerisRdf::PROPERTY_FILE_FILENAME])->literal;
            $properties[self::RESOURCE_FILE_NAME] = ltrim(str_replace(DIRECTORY_SEPARATOR, '/', $fileName), '/');
        }

        return $properties;
    }
}