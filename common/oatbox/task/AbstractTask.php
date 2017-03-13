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

namespace oat\oatbox\task;

use oat\generis\model\fileReference\ResourceFileSerializer;
use oat\oatbox\filesystem\FileSystemService;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use oat\oatbox\service\ServiceManager as ServiceManager;

/**
 * Class SyncTask
 *
 * Basic implementation of `Task` interface
 *
 * @package oat\oatbox\task\implementation
 * @author Aleh Hutnikau, <huntikau@1pt.com>
 */
abstract class AbstractTask implements Task
{

    use ServiceLocatorAwareTrait;

    const FILE_DIR = 'taskQueue';

    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $label;

    /**
     * @var
     */
    protected $invocable;

    /**
     * @var
     */
    protected $status;

    /**
     * @var array
     */
    protected $params;

    /**
     * @var string
     */
    protected $type;
    /**
     * Task execution report
     * @var null|array
     */
    protected $report;

    protected $creationDate;

    protected $owner;

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $label
     */
    public function setLabel($label) {
        $this->label = $label;
    }

    /**
     * @return string
     */
    public function getLabel() {
        return $this->label;
    }

    /**
     * @param string $id
     * @return string
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return Action|string
     */
    public function getInvocable()
    {
        return $this->invocable;
    }

    /**
     * Set action to invoke
     */
    public function setInvocable($invocable)
    {
        $this->invocable = $invocable;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->params;
    }

    /**
     * @param array $params
     */
    public function setParameters(array $params)
    {
        $this->params = $params;
    }

    /**
     * @return array|null
     */
    public function getReport()
    {
        return $this->report;
    }

    /**
     * @param $report
     */
    public function setReport($report)
    {
        $this->report = $report;
    }

    /**
     * @return mixed
     */
    public function getCreationDate()
    {
        return $this->creationDate;
    }

    /**
     * @param mixed $creationDate
     */
    public function setCreationDate($creationDate)
    {
        $this->creationDate = $creationDate;
    }

    /**
     * @return mixed
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * @param mixed $owner
     */
    public function setOwner($owner)
    {
        $this->owner = $owner;
    }

    /**
     *
     * @throws common_exception_Error
     * @return ServiceManager
     */
    public function getServiceManager()
    {
        $serviceManager = $this->getServiceLocator();
        if (!$serviceManager instanceof ServiceManager) {
            throw new common_exception_Error('Alternate service locator not compatible with '.__CLASS__);
        }
        return $serviceManager;
    }

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
}
