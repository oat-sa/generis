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
 * Copyright (c) (original work) 2017 Open Assessment Technologies SA
 *
 */
namespace oat\generis\test\integration\model\persistence\smoothsql\search\filter;

use oat\generis\model\kernel\persistence\smoothsql\search\filter\FilterOperator;
use oat\generis\test\TestCase;

class FilterOperatorTest extends TestCase
{
	public function testOperatorsCreateSuccessfully()
	{
		$this->assertInstanceOf(FilterOperator::class, FilterOperator::createEqual());
		$this->assertSame(FilterOperator::EQUAL, FilterOperator::createEqual()->getValue());

		$this->assertInstanceOf(FilterOperator::class, FilterOperator::createGreaterThanEqual());
		$this->assertSame(FilterOperator::GREATER_THAN_EQUAL, FilterOperator::createGreaterThanEqual()->getValue());

		$this->assertInstanceOf(FilterOperator::class, FilterOperator::createLike());
		$this->assertSame(FilterOperator::CONTAIN, FilterOperator::createLike()->getValue());
	}
}