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
 * Copyright (c) 2021 (original work) Open Assessment Technologies SA;
 */

declare(strict_types=1);

namespace oat\generis\model\data\event;

use JsonSerializable;
use oat\oatbox\event\Event;
use core_kernel_classes_Class;

class ClassDeletedEvent implements Event, JsonSerializable
{
    /** @var core_kernel_classes_Class */
    private $class;

    /** @var core_kernel_classes_Class|null */
    private $selectedClass;

    /** @var core_kernel_classes_Class|null */
    private $parentClass;

    public function __construct(core_kernel_classes_Class $class)
    {
        $this->class = $class;
    }

    public function getName(): string
    {
        return self::class;
    }

    public function getClass(): core_kernel_classes_Class
    {
        return $this->class;
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
            'uri' => $this->class->getUri(),
        ];

        if ($this->selectedClass !== null && $this->selectedClass !== $this->class) {
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
