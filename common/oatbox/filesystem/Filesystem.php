<?php
/**
 * Created by PhpStorm.
 * User: siwane
 * Date: 03/08/2016
 * Time: 10:54
 */

namespace oat\oatbox\filesystem;

class Filesystem
{
    protected $id;

    protected $filesystem;

    public function __construct($id, $adapter)
    {
        $this->id = $id;
        $this->filesystem = new \League\Flysystem\Filesystem($adapter);
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