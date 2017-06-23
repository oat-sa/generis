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
 *
 */
namespace oat\oatbox\task;

interface Task
{
    const STATUS_CREATED  = 'created';
    const STATUS_STARTED  = 'started';
    const STATUS_RUNNING  = 'running';
    const STATUS_FINISHED = 'finished';
    const STATUS_ARCHIVED = 'archived';

    public function getLabel();

    public function setType($type);

    public function getType();

    public function setLabel($label);

    public function getCreationDate();

    public function setCreationDate($creationDate);

    public function getStatus();

    public function setStatus($status);

    public function getOwner();

    public function setOwner($owner);

    public function getId();

    public function setId($id);

    public function getInvocable();

    public function setInvocable($invocable);

    public function getParameters();

    public function setParameters(array $params);

    public function getReport();

    public function setReport($report);

    public static function restore(array $data);

}
