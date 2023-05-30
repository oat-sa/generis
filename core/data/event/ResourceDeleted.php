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
 * Copyright (c) 2018-2021 (original work) Open Assessment Technologies SA.
 */

declare(strict_types=1);

namespace oat\generis\model\data\event;

use JsonSerializable;
use oat\oatbox\event\Event;
use core_kernel_classes_Class;
use core_kernel_classes_Resource;

class ResourceDeleted implements Event, JsonSerializable
{
    /** @var string */
    private $uri;

    /** @var core_kernel_classes_Class|null */
    private $selectedClass;

    /** @var core_kernel_classes_Resource|null */
    private $parentClass;

    /**
     * @param string $uri
     */
    public function __construct($uri)
    {
        $this->uri = $uri;
    }

    public function getId(): string
    {
        return $this->uri;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return __CLASS__;
    }

    public function setSelectedClass(?core_kernel_classes_Class $class): self
    {
        $this->selectedClass = $class;

        return $this;
    }

    public function setParentClass(?core_kernel_classes_Class $class): self
    {
        $this->parentClass = $class;

        return $this;
    }

    public function jsonSerialize(): array
    {
        $data = [
            'uri' => $this->uri,
        ];

        if ($this->selectedClass !== null) {
            $data['selectedClass'] = [
                'uri' => $this->selectedClass->getUri(),
                'label' => $this->selectedClass->getLabel(),
            ];
        }

        if ($this->parentClass !== null) {
            $data['parentClass'] = [
                'uri' => $this->parentClass->getUri(),
                'label' => $this->parentClass->getLabel(),
            ];
        }

        return $data;
    }
}
