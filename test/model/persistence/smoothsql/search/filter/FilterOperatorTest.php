<?php

namespace oat\generis\test\model\persistence\smoothsql\search\filter;

use oat\generis\model\kernel\persistence\smoothsql\search\filter\FilterOperator;
use PHPUnit_Framework_TestCase;

class FilterOperatorTest extends PHPUnit_Framework_TestCase
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