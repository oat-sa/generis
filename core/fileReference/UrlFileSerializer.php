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

namespace oat\generis\model\fileReference;

use oat\oatbox\filesystem\Directory;
use oat\oatbox\filesystem\File;
use oat\oatbox\filesystem\FileSystemService;
use oat\oatbox\service\ConfigurableService;

class UrlFileSerializer extends ConfigurableService implements FileReferenceSerializer
{
    /**
     * (non-PHPdoc)
     * @see \oat\generis\model\kernel\fileSystem\FileReferenceSerializer::serialize()
     */
    public function serialize($abstraction)
    {
        if ($abstraction instanceof File) {
            $baseDir = $this->getRootDirectory($abstraction->getFileSystemId());
            return 'file://'.urlencode($abstraction->getFileSystemId()).'/'.urlencode($baseDir->getRelPath($abstraction));
        } elseif ($abstraction instanceof Directory) {
            $baseDir = $this->getRootDirectory($abstraction->getFileSystemId());
            return 'dir://'.urlencode($abstraction->getFileSystemId()).'/'.urlencode($baseDir->getRelPath($abstraction));
        } else {
            throw new FileSerializerException(__CLASS__ . '::' . __FUNCTION__ . ' expects parameter to be an instance of Directory or File');
        }
    }

    /**
     * (non-PHPdoc)
     * @see \oat\generis\model\kernel\fileSystem\FileReferenceSerializer::unserialize()
     */
    public function unserialize($serial)
    {
        $type = substr($serial, 0, strpos($serial, ':'));
        if ($type == 'file') {
            return $this->unserializeFile($serial);
        } elseif ($type == 'dir') {
            return $this->unserializeDirectory($serial);
        } else {
            throw new FileSerializerException('Unsupported type "'.$type.'" in '.__CLASS__);
        }
    }
    
    /**
     * (non-PHPdoc)
     * @see \oat\generis\model\kernel\fileSystem\FileReferenceSerializer::unserializeFile()
     */
    public function unserializeFile($serial)
    {
        list($dir, $path) = explode('/', substr($serial, strpos($serial, '://')+3), 2);
        return $this->getRootDirectory(urldecode($dir))->getFile(urldecode($path));
    }

    /**
     * (non-PHPdoc)
     * @see \oat\generis\model\kernel\fileSystem\FileReferenceSerializer::unserializeDirectory()
     */
    public function unserializeDirectory($serial)
    {
        list($dir, $path) = explode('/', substr($serial, strpos($serial, '://')+3), 2);
        return $this->getRootDirectory(urldecode($dir))->getDirectory(urldecode($path));
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
     * (non-PHPdoc)
     * @see \oat\generis\model\kernel\fileSystem\FileReferenceSerializer::cleanUp()
     */
    public function cleanUp($serial)
    {
        // nothing to do
    }

}