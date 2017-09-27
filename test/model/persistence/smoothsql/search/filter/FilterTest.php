<?php

namespace oat\generis\test\model\persistence\smoothsql\search\filter;

use oat\generis\model\kernel\persistence\smoothsql\search\filter\Filter;
use oat\generis\model\kernel\persistence\smoothsql\search\filter\FilterOperator;
use PHPUnit_Framework_TestCase;

class FilterTest extends PHPUnit_Framework_TestCase
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
