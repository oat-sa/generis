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

namespace oat\generis\test\unit\core\data\event;

use core_kernel_classes_Class;
use oat\generis\model\data\event\ClassDeletedEvent;
use oat\generis\test\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class ClassDeletedEventTest extends TestCase
{
    /** @var ClassDeletedEvent */
    private $subject;

    /** @var core_kernel_classes_Class|MockObject */
    private $classMock;

    public function setUp(): void
    {
        $this->classMock = $this->createMock(core_kernel_classes_Class::class);
        $this->subject = new ClassDeletedEvent($this->classMock);
    }

    public function testGetName()
    {
        $this->assertEquals(ClassDeletedEvent::class, $this->subject->getName());
    }

    public function testGetClass()
    {
        $this->assertEquals($this->classMock, $this->subject->getClass());
    }
}
