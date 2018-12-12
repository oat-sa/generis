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

namespace oat\oatbox\task;

use oat\generis\model\fileReference\ResourceFileSerializer;
use oat\oatbox\filesystem\FileSystemService;
use oat\oatbox\extension\AbstractAction;

/**
 * abstract base for extension actions
 *
 * @author Aleh Hutnikau <hutnikau@1pt.com>
 *
 * @deprecated since version 7.10.0, to be removed in 8.0. Use \oat\tao\model\taskQueue\Task\FilesystemAwareTrait instead.
 */
abstract class AbstractTaskAction extends AbstractAction
{

    const FILE_DIR = 'taskQueue';

    /**
     * Save and serialize file into task queue filesystem.
     *
     * @param string $path file path
     * @param string $name file name
     * @return string file reference uri
     */
    protected function saveFile($path, $name)
    {
        $filename = $this->getUniqueFilename($name);

        /** @var \oat\oatbox\filesystem\Directory $dir */
        $dir = $this->getServiceManager()->get(FileSystemService::SERVICE_ID)
            ->getDirectory(Queue::FILE_SYSTEM_ID);
        /** @var \oat\oatbox\filesystem\FileSystem $filesystem */
        $filesystem = $dir->getFileSystem();

        $stream = fopen($path, 'r+');
        $filesystem->writeStream($filename, $stream);
        fclose($stream);

        $file = $dir->getFile($filename);
        return $this->getFileReferenceSerializer()->serialize($file);
    }

    /**
     * Create a new unique filename based on an existing filename
     *
     * @param string $fileName
     * @return string
     */
    protected function getUniqueFilename($fileName)
    {
        $value = uniqid(md5($fileName));
        $ext = pathinfo($fileName, PATHINFO_EXTENSION);
        if (!empty($ext)){
            $value .= '.' . $ext;
        }
        return static::FILE_DIR . '/' . $value;
    }

    /**
     * Get serializer to persist filesystem object
     * @return ResourceFileSerializer
     */
    protected function getFileReferenceSerializer()
    {
        return $this->getServiceManager()->get(ResourceFileSerializer::SERVICE_ID);
    }

    /**
     * @return \core_kernel_classes_Class
     */
    protected static function getTaskClass()
    {
        return new \core_kernel_classes_Class(Task::TASK_CLASS);
    }
}
