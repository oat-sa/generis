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
 * Copyright (c) 2019 (original work) Open Assessment Technologies SA;
 */

namespace oat\generis\test\unit\oatbox\service;

use common_Utils as Utils;
use InvalidArgumentException;
use oat\generis\test\TestCase;
use oat\oatbox\service\EnvironmentVariable;

class EnvironmentVariableTest extends TestCase
{
    const VAR_NAME = 'That\'s the variable\'s name.';

    /** @var EnvironmentVariable */
    private $subject;

    public function setUp(): void
    {
        $this->subject = new EnvironmentVariable(self::VAR_NAME);
    }

    public function testConstructor()
    {
        $this->assertInstanceOf(EnvironmentVariable::class, $this->subject);

        $property = new \ReflectionProperty(EnvironmentVariable::class, 'name');
        $property->setAccessible(true);
        $this->assertSame(self::VAR_NAME, $property->getValue($this->subject));
    }

    public function testConstructorWithNonStringKeyThrowsException()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Environment variable name must be a string.');
        new EnvironmentVariable([]);
    }

    public function testToPhpCode()
    {
        $this->assertSame('new ' . EnvironmentVariable::class . '(' . Utils::toPHPVariableString(self::VAR_NAME) . ')', $this->subject->__toPhpCode());
    }

    public function testToString()
    {
        $_ENV[self::VAR_NAME] = 1012;

        $this->assertSame('1012', (string)$this->subject);
    }
}
