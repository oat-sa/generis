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
 * Copyright (c) 2023 (original work) Open Assessment Technologies SA;
 *
 */

namespace oat\generis\tests\unit\helpers;

use PHPUnit\Framework\TestCase;

class PhpToolsTest extends TestCase
{
    public function testNamespaceAndClass(): void
    {
        $info = \helpers_PhpTools::getClassInfo(__FILE__);

        $this->assertEquals('\oat\generis\tests\unit\helpers', $info['ns'], 'Namespace is wrong.');
        $this->assertEquals('PhpToolsTest', $info['class'], 'Class is wrong.');
    }

    public function testNamespaceAndClassLegacy(): void
    {
        $reflectionHelper = new \ReflectionClass(\helpers_PhpTools::class);
        $info = \helpers_PhpTools::getClassInfo($reflectionHelper->getFileName());

        $this->assertEquals('', $info['ns'], 'Namespace is wrong.');
        $this->assertEquals('helpers_PhpTools', $info['class'], 'Class is wrong.');
    }
}
