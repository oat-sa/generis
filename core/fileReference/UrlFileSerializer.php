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
     * {@inheritDoc}
     * @see FileReferenceSerializer::serialize()
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
     * {@inheritDoc}
     * @see FileReferenceSerializer::unserialize()
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
     * {@inheritDoc}
     * @see FileReferenceSerializer::unserializeFile()
     */
    public function unserializeFile($serial)
    {
        $parts = explode('/', substr($serial, strpos($serial, '://')+3), 2);
        if (count($parts) != 2) {
            throw new FileSerializerException('Unsupported dir in '.__CLASS__);
        }
        return $this->getRootDirectory(urldecode($parts[0]))->getFile(urldecode($parts[1]));
    }

    /**
     * {@inheritDoc}
     * @see FileReferenceSerializer::unserializeDirectory()
     */
    public function unserializeDirectory($serial)
    {
        $parts = explode('/', substr($serial, strpos($serial, '://')+3), 2);
        if (count($parts) != 2) {
            throw new FileSerializerException('Unsupported dir in '.__CLASS__);
        }
        return $this->getRootDirectory(urldecode($parts[0]))->getDirectory(urldecode($parts[1]));
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
     * {@inheritDoc}
     * @see FileReferenceSerializer::cleanUp()
     */
    public function cleanUp($serial)
    {
        // nothing to do
        return true;
    }

}