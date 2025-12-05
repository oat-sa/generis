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

namespace oat\generis\test\unit\common\oatbox\log\logger\extender;

use Exception;
use Throwable;
use PHPUnit\Framework\TestCase;
use oat\oatbox\log\logger\extender\ContextExtenderInterface;
use oat\oatbox\log\logger\extender\ExceptionContextExtender;

class ExceptionContextExtenderTest extends TestCase
{
    /** @var ExceptionContextExtender */
    private $sut;

    public function setUp(): void
    {
        $this->sut = new ExceptionContextExtender();
    }

    public function testExtend(): void
    {
        $this->assertSame(
            [
                ContextExtenderInterface::CONTEXT_EXCEPTION => '"Last error", code: 200, file: "'
                    . __FILE__ . '", line: 67, previous: "Original error", code: 100, file: "'
                    . __FILE__ . '", line: 70',
            ],
            $this->sut->extend(
                [
                    ContextExtenderInterface::CONTEXT_EXCEPTION => $this->createException(),
                ]
            )
        );
    }

    public function testExtendWithoutException(): void
    {
        $this->assertSame(
            [],
            $this->sut->extend([])
        );
    }

    private function createException(): Throwable
    {
        return new Exception(
            'Last error',
            200,
            new Exception(
                'Original error',
                100
            )
        );
    }
}
