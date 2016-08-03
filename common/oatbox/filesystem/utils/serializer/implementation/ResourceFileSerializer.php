<?php

namespace oat\oatbox\filesystem\utils\serializer\implementation;

use oat\generis\model\OntologyAwareTrait;
use oat\oatbox\filesystem\Directory;
use oat\oatbox\filesystem\File;
use oat\oatbox\filesystem\FileSystemService;
use oat\oatbox\filesystem\utils\serializer\FileSerializer;
use oat\oatbox\service\ServiceManager;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

class ResourceFileSerializer implements FileSerializer, ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;
    use OntologyAwareTrait;

    public function __construct()
    {
        $this->setServiceLocator(ServiceManager::getServiceManager());
    }

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
     * This
     * @see parent::unserialize
     */
    public function unserialize($serial)
    {
        $file = $this->getResource($serial);

        $properties = $file->getPropertiesValues(array(
            $this->getProperty(PROPERTY_FILE_FILENAME),
            $this->getProperty(PROPERTY_FILE_FILEPATH),
            $this->getProperty(PROPERTY_FILE_FILESYSTEM)
        ));

        $fileName = current($properties[PROPERTY_FILE_FILENAME])->literal;
        $filePath = current($properties[PROPERTY_FILE_FILEPATH])->literal;
        $fileSystemProperty	=  current($properties[PROPERTY_FILE_FILESYSTEM]);
        if ($fileSystemProperty instanceof \core_kernel_classes_Resource) {
            $fileSystemUri = $fileSystemProperty->getUri();
        } else {
            $fileSystemUri = $fileSystemProperty->literal;
        }

        $fullPath = trim($filePath, '\\/') . '/' . trim($fileName, '\\/');
        return $this->getRootDirectory($fileSystemUri)->getFile($fullPath);
    }

    public function cleanUp($serialisedFile)
    {
    }

    /**
     * @see parent::unserializeDirectory
     */
    public function unserializeDirectory($serialisedFile)
    {
        $file = $this->getResource($serialisedFile);

        $properties = $file->getPropertiesValues(array(
            $this->getProperty(PROPERTY_FILE_FILEPATH),
            $this->getProperty(PROPERTY_FILE_FILESYSTEM)
        ));

        $filePath = current($properties[PROPERTY_FILE_FILEPATH])->literal;
        $fileSystemProperty	=  current($properties[PROPERTY_FILE_FILESYSTEM]);
        if ($fileSystemProperty instanceof \core_kernel_classes_Resource) {
            $fileSystemUri = $fileSystemProperty->getUri();
        } else {
            $fileSystemUri = $fileSystemProperty->literal;
        }

        $fullPath = trim($filePath, '\\/');
        return $this->getRootDirectory($fileSystemUri)->getDirectory($fullPath);
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
}