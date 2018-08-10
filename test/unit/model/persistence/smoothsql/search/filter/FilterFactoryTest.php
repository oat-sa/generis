<?php

namespace oat\generis\test\integration\model\persistence\smoothsql\search\filter;

use oat\generis\model\kernel\persistence\smoothsql\search\filter\Filter;
use oat\generis\model\kernel\persistence\smoothsql\search\filter\FilterFactory;
use oat\generis\test\TestCase;

class FilterFactoryTest extends TestCase
{
	public function testBuildFiltersReturnArrayOfFilters()
	{
		$filterMock = $this->getMockBuilder(Filter::class)->disableOriginalConstructor()->getMock();
		$filtersRaw = [
			'another rdf key' => 'another rdf value',
			$filterMock,
		];

		$filters = FilterFactory::buildFilters($filtersRaw, $isLikeOperatorDefaultForLegacy = true);

		$this->assertCount(2, $filters);
		$this->assertInstanceOf(Filter::class, $filters[0]);
		$this->assertInstanceOf(Filter::class, $filters[1]);
	}

	public function testBuildFiltersSupportsLegacyWayWithLike()
	{
		$filtersRaw = [
			'another rdf key' => 'another rdf value'
		];

		$filters = FilterFactory::buildFilters($filtersRaw, $isLikeOperator = true);
		$this->assertCount(1, $filters);

		$filter = $filters[0];
		$this->assertSame('another rdf key', $filter->getKey());
		$this->assertSame('another rdf value', $filter->getValue());
		$this->assertSame('contains', $filter->getOperator());
		$this->assertEquals([], $filter->getOrConditionValues());
	}


	public function testBuildFiltersSupportsLegacyWayWithEquals()
	{
		$filtersRaw = [
			'another rdf key' => 'another rdf value'
		];

		$filters = FilterFactory::buildFilters($filtersRaw, $isLikeOperator = false);
		$this->assertCount(1, $filters);

		$filter = $filters[0];
		$this->assertSame('another rdf key', $filter->getKey());
		$this->assertSame('another rdf value', $filter->getValue());
		$this->assertSame('equals', $filter->getOperator());
		$this->assertEquals([], $filter->getOrConditionValues());
	}

	public function testBuildFiltersSupportsLegacyWayWithOrConditions()
	{
		$filtersRaw = [
			'another rdf key' => ['another rdf value', 'value1', 'value2']
		];

		$filters = FilterFactory::buildFilters($filtersRaw, $isLikeOperatorDefaultForLegacy = false);
		$this->assertCount(1, $filters);

		$filter = $filters[0];
		$this->assertSame('another rdf key', $filter->getKey());
		$this->assertSame('another rdf value', $filter->getValue());
		$this->assertSame('equals', $filter->getOperator());
		$this->assertEquals(['value1', 'value2'], $filter->getOrConditionValues());
	}

}