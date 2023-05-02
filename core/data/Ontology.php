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
 * Copyright (c) 2018 (original work) 2014 Open Assessment Technologies SA
 */

namespace oat\generis\model\data;

use core_kernel_classes_Class;
use core_kernel_classes_Property;
use core_kernel_classes_Resource;

/**
 * Interface to manage the instantiating of resources
 *
 * @author Joel Bout <joel@taotesting.com>
 */
interface Ontology extends Model
{
    public const SERVICE_ID = 'generis/ontology';

    /**
     * @param string $uri
     *
     * @return core_kernel_classes_Resource
     */
    public function getResource($uri);

    /**
     * @param string $uri
     *
     * @return core_kernel_classes_Class
     */
    public function getClass($uri);

    /**
     * @param string $uri
     *
     * @return core_kernel_classes_Property
     */
    public function getProperty($uri);
}
