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
 * Copyright (c) 2020 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 * @license GPLv2
 */

namespace oat\generis\model\kernel\persistence\newsql;

use core_kernel_classes_Resource;
use core_kernel_persistence_smoothsql_Resource;

/**
 * NewSQL rdfs Resource service
 */
class NewSqlResource extends core_kernel_persistence_smoothsql_Resource
{
    public function setPropertiesValues(core_kernel_classes_Resource $resource, $properties)
    {
        $added = 0;
        $errors = 0;
        // @TODO Use the multi insert SQL capabilities to reduce the nr of queries
        if (is_array($properties) && count($properties) > 0) {
            foreach ($properties as $propertyUri => $value) {
                $property = $this->getModel()->getProperty($propertyUri);
                $formattedValues = [];

                if ($value instanceof core_kernel_classes_Resource) {
                    $formattedValues[] = $value->getUri();
                } elseif (is_array($value)) {
                    foreach ($value as $val) {
                        $formattedValues[] = $val instanceof core_kernel_classes_Resource
                        ? $val->getUri()
                        : $val;
                    }
                } else {
                    $formattedValues[] = ($value == null) ? '' : $value;
                }

                foreach ($formattedValues as $object) {
                    $success = $this->setPropertyValue($resource, $property, $object);
                    if ($success) {
                        $added++;
                    } else {
                        $errors++;
                    }
                }
            }
        }
        return $errors = 0 && $added > 0;
    }
}
