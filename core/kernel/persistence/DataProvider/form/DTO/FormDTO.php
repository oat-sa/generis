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
 * Copyright (c) 2023 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */

namespace oat\generis\model\kernel\persistence\DataProvider\form\DTO;

class FormDTO
{
    /** @var array<FormPropertyDTO>  */
    private array $properties = [];

    public function __construct(array $data)
    {
        foreach ($data as $propertyData) {
            $this->properties[$propertyData['property']] = new FormPropertyDTO(...array_values($propertyData));
        }
    }

    public function getProperty(string $propertyUri): FormPropertyDTO
    {
        if (!array_key_exists($propertyUri, $this->properties)) {
            throw new \RuntimeException(sprintf('Uri %s was not found in properties map', $propertyUri));
        }

        return $this->properties[$propertyUri];
    }

    public function getProperties(): array
    {
        return $this->properties;
    }
}
