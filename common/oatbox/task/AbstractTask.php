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
 * Copyright (c) 2016-2017 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */

namespace oat\oatbox\task;

/**
 * Class SyncTask
 *
 * Basic implementation of `Task` interface
 *
 * @package oat\oatbox\task\implementation
 * @author Aleh Hutnikau, <huntikau@1pt.com>
 *
 * @deprecated since version 7.10.0, to be removed in 8.0. Use \oat\tao\model\taskQueue\Task\AbstractTask instead.
 */
abstract class AbstractTask implements Task , \JsonSerializable
{

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
     * SyncTask constructor.
     * @param Action|string $invocable
     * @param array $params
     */
    public function __construct($invocable = null, $params  = null)
    {
        $this->id = \common_Utils::getNewUri();
        $this->setOwner(\common_session_SessionManager::getSession()->getUser()->getIdentifier());
        $this->setInvocable($invocable);
        $this->setParameters($params);
        $this->setStatus(self::STATUS_CREATED);
    }

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

    // Serialization

    /**
     * (non-PHPdoc)
     * @see JsonSerializable::jsonSerialize()
     */
    public function jsonSerialize()
    {
        $invocable = $this->getInvocable();
        if (is_object($invocable) && !$invocable instanceof \JsonSerializable) {
            $invocable = get_class($invocable);
        }
        return [
            'invocable' => $invocable,
            'params'    => $this->getParameters(),
            'id'        => $this->getId(),
            'status'    => $this->getStatus(),
            'report'    => $this->getReport(),
            'label'     => $this->getLabel(),
            'type'     => $this->getType(),
            'added'     => $this->getCreationDate(),
            'owner'     => $this->getOwner(),
        ];
    }

    /**
     * Restore a task
     *
     * @param array $data
     * @return Task
     */
    public static function restore(array $data)
    {
        if (!isset($data['invocable'], $data['params'])){
            return null;
        }
        /**
         * @var $task Task
         */
        $class = self::class;
        $task = new $class();
        if (isset($data['report'])) {
            $task->setReport($data['report']);
        }
        if (isset($data['status'])) {
            $task->setStatus($data['status']);
        }
        if (isset($data['id'])) {
            $task->setId($data['id']);
        }
        if (isset($data['added'])) {
            $task->setCreationDate($data['added']);
        }
        if (isset($data['owner'])) {
            $task->setOwner($data['owner']);
        }
        if (isset($data['label'])) {
            $task->setLabel($data['label']);
        }
        if (isset($data['type'])) {
            $task->setType($data['type']);
        }
        if (isset($data['added'])) {
            $task->setType($data['added']);
        }
        if(isset($data['invocable'])) {
            $task->setInvocable($data['invocable']);
        }
        if (isset($data['params'])) {
            $task->setParameters($data['params']);
        }
        return $task;
    }
    
}
