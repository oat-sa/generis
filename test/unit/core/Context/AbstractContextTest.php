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
 * Copyright (c) 2022 (original work) Open Assessment Technologies SA.
 */

declare(strict_types=1);

namespace oat\generis\test\unit\core\Context;

use InvalidArgumentException;
use oat\generis\test\TestCase;
use oat\generis\model\Context\AbstractContext;

class AbstractContextTest extends TestCase
{
    private const PARAM_TEST = 'test';

    /** @var AbstractContext */
    private $sut;

    protected function setUp(): void
    {
        $contextData = [
            self::PARAM_TEST => ['value'],
        ];

        $this->sut = new class ($contextData) extends AbstractContext {
            protected function getSupportedParameters(): array
            {
                return [
                    'test',
                ];
            }

            protected function validateParameter(string $parameter, $parameterValue): void
            {
                if ($parameter === 'test' && !is_array($parameterValue)) {
                    throw new InvalidArgumentException(
                        sprintf(
                            'Context parameter %s is not valid. It must be an array.',
                            $parameter
                        )
                    );
                }
            }
        };
    }

    public function testHasParameter(): void
    {
        $this->assertFalse($this->sut->hasParameter('invalidParameter'));
        $this->assertTrue($this->sut->hasParameter(self::PARAM_TEST));
    }

    public function testGetParameter(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Context parameter invalidParameter is not supported.');
        $this->sut->getParameter('invalidParameter');

        $this->assertEquals(['testValue'], $this->sut->hasParameter(self::PARAM_TEST));
    }

    public function testSetParameter(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Context parameter invalidParameter is not supported.');
        $this->sut->setParameter('invalidParameter', 'invalidValue');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Context parameter test is not valid. It must be an array.');
        $this->sut->setParameter(self::PARAM_TEST, 'invalidValue');

        $this->assertEquals(['value'], $this->sut->getParameter(self::PARAM_TEST));

        $this->sut->setParameter(self::PARAM_TEST, ['anotherValue']);
        $this->assertEquals(['anotherValue'], $this->sut->getParameter(self::PARAM_TEST));
    }
}
