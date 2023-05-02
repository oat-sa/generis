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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 */

namespace oat\oatbox\task;

/**
 * @deprecated since version 7.10.0, to be removed in 8.0. Use \oat\tao\model\taskQueue\Task\TaskInterface instead.
 */
interface Task
{
    public const STATUS_CREATED = 'created';
    public const STATUS_STARTED = 'started';
    public const STATUS_RUNNING = 'running';
    public const STATUS_FINISHED = 'finished';
    public const STATUS_ARCHIVED = 'archived';

    public const TASK_CLASS = 'http://www.tao.lu/Ontologies/generis.rdf#TaskQueueTask';
    public const PROPERTY_LINKED_RESOURCE = 'http://www.tao.lu/Ontologies/generis.rdf#LinkedResource';
    public const PROPERTY_REPORT = 'http://www.tao.lu/Ontologies/generis.rdf#Report';

    /**
     * @deprecated since version 7.10.0, to be removed in 8.0.
     */
    public function getLabel();

    /**
     * @deprecated since version 7.10.0, to be removed in 8.0.
     *
     * @param mixed $type
     */
    public function setType($type);

    /**
     * @deprecated since version 7.10.0, to be removed in 8.0.
     */
    public function getType();

    /**
     * @deprecated since version 7.10.0, to be removed in 8.0.
     *
     * @param mixed $label
     */
    public function setLabel($label);

    /**
     * @deprecated since version 7.10.0, to be removed in 8.0.
     */
    public function getCreationDate();

    /**
     * @deprecated since version 7.10.0, to be removed in 8.0.
     *
     * @param mixed $creationDate
     */
    public function setCreationDate($creationDate);

    /**
     * @deprecated since version 7.10.0, to be removed in 8.0.
     */
    public function getStatus();

    /**
     * @deprecated since version 7.10.0, to be removed in 8.0.
     *
     * @param mixed $status
     */
    public function setStatus($status);

    /**
     * @deprecated since version 7.10.0, to be removed in 8.0.
     */
    public function getOwner();

    /**
     * @deprecated since version 7.10.0, to be removed in 8.0.
     *
     * @param mixed $owner
     */
    public function setOwner($owner);

    /**
     * @deprecated since version 7.10.0, to be removed in 8.0.
     */
    public function getId();

    /**
     * @deprecated since version 7.10.0, to be removed in 8.0.
     *
     * @param mixed $id
     */
    public function setId($id);

    /**
     * @deprecated since version 7.10.0, to be removed in 8.0.
     */
    public function getInvocable();

    /**
     * @deprecated since version 7.10.0, to be removed in 8.0.
     *
     * @param mixed $invocable
     */
    public function setInvocable($invocable);

    /**
     * @deprecated since version 7.10.0, to be removed in 8.0.
     */
    public function getParameters();

    /**
     * @deprecated since version 7.10.0, to be removed in 8.0.
     */
    public function setParameters(array $params);

    /**
     * @deprecated since version 7.10.0, to be removed in 8.0.
     */
    public function getReport();

    /**
     * @deprecated since version 7.10.0, to be removed in 8.0.
     *
     * @param mixed $report
     */
    public function setReport($report);

    /**
     * @deprecated since version 7.10.0, to be removed in 8.0.
     */
    public static function restore(array $data);
}
