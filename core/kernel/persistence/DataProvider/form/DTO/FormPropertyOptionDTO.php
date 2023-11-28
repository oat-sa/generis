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

class FormPropertyOptionDTO
{
    private string $uri;
    private ?string $level;
    private ?string $label;

    public function __construct(string $uri, ?string $level, ?string $label)
    {
        $this->uri = $uri;
        $this->level = $level;
        $this->label = $label;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function getLevel(): ?string
    {
        return $this->level;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }
}
