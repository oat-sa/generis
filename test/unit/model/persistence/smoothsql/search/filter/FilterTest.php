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

use oat\generis\model\kernel\persistence\smoothsql\search\filter\Filter;
use oat\generis\model\kernel\persistence\smoothsql\search\filter\FilterOperator;
use oat\generis\test\TestCase;

class FilterTest extends TestCase
{
	public function testFilterConstructSuccessfully()
	{
		$filter = $this->getFilterObject('key', 'value', 'operator');
		$this->assertSame('key', $filter->getKey());
		$this->assertSame('value', $filter->getValue());
		$this->assertSame('operator', $filter->getOperator());
		$this->assertSame([], $filter->getOrConditionValues());
	}

	public function testFilterConstructWithOptionalParam()
	{
		$filter = $this->getFilterObject('key', 'value', 'operator', ['value1', 'value2']);

		$this->assertSame(['value1', 'value2'], $filter->getOrConditionValues());
	}

	protected function getFilterObject($key, $value, $operator, array $orConditions = [])
	{
		$operatorMock = $this->getMockBuilder(FilterOperator::class)->disableOriginalConstructor()->getMock();
		$operatorMock
			->method('getValue')
			->willReturn($operator);
		$filter = new Filter($key, $value, $operatorMock, $orConditions);

		return $filter;
	}
}
