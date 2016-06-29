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

namespace oat\oatbox\task\implementation;

use oat\oatbox\task\Task;
use oat\oatbox\action\Action;

/**
 * Class SyncTask
 *
 * Basic implementation of `Task` interface
 *
 * @package oat\oatbox\task\implementation
 * @author Aleh Hutnikau, <huntikau@1pt.com>
 */
class SyncTask implements Task
{

    /**
     * @var string
     */
    private $id;

    /**
     * @var
     */
    private $invocable;

    /**
     * @var
     */
    private $status;

    /**
     * @var array
     */
    private $params;

    /**
     * SyncTask constructor.
     * @param Action|string $invocable
     * @param array $params
     */
    public function __construct($invocable, $params)
    {
        $this->id = \common_Utils::getNewUri();
        $this->invocable = $invocable;
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

    /**
     * @return Action|string
     */
    public function getInvocable()
    {
        return $this->invocable;
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
}
