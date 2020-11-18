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
 * Copyright (c) (original work) 2015 Open Assessment Technologies SA
 *
 */

namespace oat\generis\test\unit\model\persistence\smoothsql;

use oat\generis\test\TestCase;
use Prophecy\Argument;
use Prophecy\Promise\ReturnPromise;

class SmoothModelIteratorTest extends TestCase
{

    public function setUp(): void
    {
    }


    private function createIterator()
    {

        $persistenceProphecy = $this->prophesize('common_persistence_SqlPersistence');

        $statementProphecy = $this->prophesize('PDOStatement');
        $statementValue = [
            "modelid"    => 1,
            "subject"    => '#subject',
            "predicate"  => '#predicate',
            "object"     => 'obb',
            "id"         => 898,
            "l_language" => 'en-US',
            "author"     => 'testauthor'
        ];
        $statementValue2 = [
            "modelid"    => 1,
            "subject"    => '#subject2',
            "predicate"  => '#predicate2',
            "object"     => 'ob2',
            "id"         => 899,
            "l_language" => 'en-US',
            "author"     => 'testauthor'
        ];
        $return = new ReturnPromise([
            $statementValue,
            $statementValue2,
            false
        ]);

        $statementProphecy->fetch()->will($return);

        $plop = $statementProphecy->reveal();

        $query = 'SELECT * FROM statements ORDER BY id';
        $finalQuery = $query . ' LIMIT 100';

        $platformProphecy = $this->prophesize('common_persistence_sql_Platform');
        $platformProphecy->limitStatement($query, 100, 0)->willReturn($finalQuery);
        $platform = $platformProphecy->reveal();

        $persistenceProphecy->getPlatForm()->willReturn($platform);
        $persistenceProphecy
            ->query($finalQuery, Argument::type('array'), Argument::type('array'))
            ->willReturn($plop);

        $iterator = new \core_kernel_persistence_smoothsql_SmoothIterator($persistenceProphecy->reveal());
        return $iterator;
    }

    public function testConstruct()
    {
        $this->assertInstanceOf('core_kernel_persistence_smoothsql_SmoothIterator', $this->createIterator());
    }

    /**
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testCurrent()
    {

        $iterator = $this->createIterator();
        $this->assertTrue($iterator->valid());

        $current = $iterator->current();
        $this->assertInstanceOf('core_kernel_classes_Triple', $current);
        $this->assertSame(1, $current->modelid);
        $this->assertSame('#subject', $current->subject);
        $this->assertSame('#predicate', $current->predicate);
        $this->assertSame('obb', $current->object);
        $this->assertSame(898, $current->id);
        $this->assertSame('en-US', $current->lg);
    }

    /**
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testNext()
    {
        $iterator = $this->createIterator();
        $iterator->next();
        $this->assertTrue($iterator->valid());
        $current = $iterator->current();
        $this->assertInstanceOf('core_kernel_classes_Triple', $current);
        $this->assertSame(1, $current->modelid);
        $this->assertSame('#subject2', $current->subject);
        $this->assertSame('#predicate2', $current->predicate);
        $this->assertSame('ob2', $current->object);
        $this->assertSame(899, $current->id);
        $this->assertSame('en-US', $current->lg);
    }
}
