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
 * Copyright (c) 2024 (original work) Open Assessment Technologies SA.
 */

declare(strict_types=1);

namespace oat\generis\test\unit\core\kernel\uri;

use oat\generis\model\kernel\uri\Bin2HexUriProvider;
use PHPUnit\Framework\TestCase;

class Bin2HexUriProviderTest extends TestCase
{
    private const NAMESPACE = 'test#';

    private Bin2HexUriProvider $sut;

    protected function setUp(): void
    {
        $this->sut = new Bin2HexUriProvider(['namespace' => self::NAMESPACE]);
    }

    public function testProvide(): void
    {
        $uri = $this->sut->provide();

        $this->assertStringContainsString(self::NAMESPACE, $uri);
        $this->assertEquals(36, strlen(substr($uri, strlen(self::NAMESPACE))));
    }
}
