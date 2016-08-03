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

use \League\Flysystem\Filesystem as FlyFileSystem;

/**
 * Class Filesystem
 *
 * @method bool             has(string $path)
 * @method string|false     read(string $path)
 * @method resource|false   readStream(string $path)
 * @method array            listContents(string $directory = '', bool $recursive = false)
 * @method array|false      getMetadata(string $path)
 * @method int|false        getSize(string $path)
 * @method string|false     getMimetype(string $path)
 * @method string|false     getTimestamp(string $path)
 * @method string|false     getVisibility(string $path)
 * @method bool             write(string $path, string $contents, array $config = [])
 * @method bool             writeStream(string   $path, resource $resource, array $config = [])
 * @method bool             update(string   $path, string $contents, array $config = [])
 * @method bool             updateStream(string   $path, resource $resource, array $config = [])
 * @method bool             rename(string $path, string $newpath)
 * @method bool             copy(string $path, string $newpath)
 * @method bool             delete(string $path)
 * @method bool             deleteDir(string $dirname)
 * @method bool             createDir(string $dirname, array $config = [])
 * @method bool             setVisibility(string $path, $visibility)
 * @method bool             put(string $path, string $contents, array $config = [])
 * @method bool             putStream(string $path, resource $resource, array $config = [])
 * @method string|false     readAndDelete(string  $path)
 * @method void             assertPresent(string $path)
 * @method void             assertAbsent(string $path)
 * @method array            getWithMetadata(string $path, array $metadata)
 * @method bool             forceCopy(string $path, string $newpath)
 * @method bool             forceRename(string $path, string $newpath)
 * @method array            listFiles(string $path = '', boolean $recursive = false)
 * @method array            listPaths(string $path = '', boolean $recursive = false)
 * @method array            listWith(array $keys = [], $directory = '', $recursive = false)
 *
 * @method \League\Flysystem\Handler                get(string  $path, \League\Flysystem\Handler $handler = null)
 * @method \League\Flysystem\FilesystemInterface    addPlugin(\League\Flysystem\PluginInterface $plugin)
 * @method \League\Flysystem\AdapterInterface       getAdapter()
 */
class FileSystem
{
    protected $id;

    protected $filesystem;

    /**
     * Filesystem constructor.
     *
     * @param $id
     * @param $adapter
     */
    public function __construct($id, $adapter)
    {
        $this->id = $id;
        $this->filesystem = new FlyFileSystem($adapter);
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \League\Flysystem\Filesystem
     * @throws \common_Exception
     */
    protected function getFileSystem()
    {
        if (! $this->filesystem) {
            throw new \common_Exception('Unable to find filesystem.');
        }

        return $this->filesystem;
    }

    public function __call($method, array $arguments)
    {
        if (! method_exists($this, $method)) {
            return call_user_func_array(
                [$this->getFileSystem(), $method],
                $arguments
            );
        }
    }

}