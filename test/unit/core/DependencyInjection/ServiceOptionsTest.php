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
 *
 * @author Gabriel Felipe Soares <gabriel.felipe.soares@taotesting.com>
 */

declare(strict_types=1);

namespace oat\generis\test\unit\model\DependencyInjection;

use oat\generis\model\DependencyInjection\ServiceOptions;
use oat\generis\test\TestCase;

class ServiceOptionsTest extends TestCase
{
    /** @var ServiceOptions */
    private $subject;

    public function setUp(): void
    {
        $this->subject = new ServiceOptions(
            [
                'class1' => [
                    'option1' => 'option1Value',
                    'option2' => 'option2Value',
                ]
            ]
        );
    }

    public function testGet(): void
    {
        $this->assertSame('option1Value', $this->subject->get('class1', 'option1'));
        $this->assertSame('option2Value', $this->subject->get('class1', 'option2'));
        $this->assertSame('foo', $this->subject->get('class1', 'option3', 'foo'));
        $this->assertNull($this->subject->get('none', 'none'));
    }

    public function testSave(): void
    {
        $this->subject->save('class1', 'option1', 'foo');
        $this->subject->save('class1', 'option3', 'bar');

        $this->assertSame('foo', $this->subject->get('class1', 'option1'));
        $this->assertSame('bar', $this->subject->get('class1', 'option3'));
    }

    public function testRemove(): void
    {
        $this->subject->remove('class1', 'option1');

        $this->assertNull($this->subject->get('class1', 'option1'));
    }
}
