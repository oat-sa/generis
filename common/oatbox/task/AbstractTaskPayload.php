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

use oat\tao\model\datatable\DatatablePayload;
use oat\tao\model\datatable\DatatableRequest;

/**
 * Class AbstractTaskPayload
 *
 *
 * @package oat\oatbox\task
 * @author Antoine Robin, <antoine@taotesting.com>
 */
abstract class AbstractTaskPayload implements DatatablePayload
{

    /** @var DatatableRequest $request */
    protected $request;

    /**
     * Search task with specific filters
     * @return Task[]
     */
    abstract protected function search();

    /**
     * Count the number of tasks satisfying the filters
     * @return integer
     */
    abstract protected function count();

    /**
     * Return the payload to display to user
     * @return array
     */
    public function getPayload() {

        $iterator = $this->search();

        $taskList = [];

        foreach ($iterator as $task) {
            $taskList[] =
                [
                    "id"           => $task->getId(),
                    "label"        => $task->getLabel(),
                    "added"        => $task->getCreationDate(),
                    "status"       => $task->getStatus(),
                    "report"       => json_decode($task->getReport(), true),
                ];
        }
        $countTotal = $this->count();
        $rows = $this->request->getRows();
        $data = [
            'rows'    => $rows,
            'page'    => $this->request->getPage(),
            'amount' => count($taskList),
            'total'   => ceil($countTotal/$rows),
            'data' => $taskList,
        ];

        return $data;

    }

    public function jsonSerialize()
    {
        return $this->getPayload();
    }


}
