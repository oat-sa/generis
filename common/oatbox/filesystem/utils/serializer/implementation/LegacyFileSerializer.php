<?php

namespace oat\oatbox\filesystem\utils\serializer\implementation;

use oat\oatbox\filesystem\Directory;
use oat\oatbox\filesystem\FileSystemService;
use oat\oatbox\filesystem\utils\serializer\FileSerializer;
use oat\oatbox\service\ServiceManager;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

class LegacyFileSerializer implements FileSerializer, ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    public function __construct()
    {
        $this->setServiceLocator(ServiceManager::getServiceManager());
    }

    public function serialize($filesystemUri, $filepath)
    {



    }

    /**
     * @param $fsUri
     * @param $filePath
     * @param $fileName
     * @return Directory
     */
    public function unserialize($fsUri, $filePath, $fileName)
    {
        $fullPath = trim($filePath, '\\/') . '/' . trim($fileName, '\\/');
        return $this->getRootDirectory($fsUri)->getDirectory($fullPath);
    }

    /**
     * @param $fsUri
     * @param $path
     * @return Directory
     */
    public function unserializeDirectory($fsUri, $path)
    {
        return $this->getRootDirectory($fsUri)->getDirectory($path);
    }

    /**
     * @return Directory
     */
    protected function getRootDirectory($uri)
    {
        return $this->getServiceLocator()->get(FileSystemService::SERVICE_ID)->getDirectory($uri);
    }
}