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
namespace oat\oatbox\filesystem;

use League\Flysystem\Directory as FlyDirectory;
use League\Flysystem\FilesystemInterface;

class Directory {
    
    private $fileSystem;
    
    private $prefix;

    public function __construct($fileSystem, $prefix)
    {
        $this->fileSystem = $fileSystem;
        $this->prefix = $prefix;
    }
    
    public function getDirectory($path)
    {
        return new self($this->getFileSystem(), $this->getFullPath($path));
    }
    
    
    public function getFile($path)
    {
        return new File($this->getFileSystem(), $this->getFullPath($path));
    }
    
    public function getIterator($flags)
    {
    }
    
    public function getRelPath(File $file)
    {
    }
    
    private function getFullPath($path)
    {
        return $this->prefix.'/'.ltrim($path, '/');
    }
}