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

namespace oat\oatbox\task\TaskInterface;


use oat\tao\model\datatable\DatatablePayload;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use oat\tao\model\datatable\DatatableRequest as DatatableRequestInterface;

/**
 * @deprecated since version 7.10.0, to be removed in 8.0.
 */
interface TaskPayLoad extends DatatablePayload , ServiceLocatorAwareInterface
{

    public function __construct(TaskPersistenceInterface $persistence , $currentUserId = null , DatatableRequestInterface $request = null);

    /**
     * @deprecated since version 7.10.0, to be removed in 8.0.
     */
    public function count();

}