<?php

namespace oat\generis\model\kernel\persistence\smoothsql\search\filter;

class FilterFactory
{
	/**
	 * @param array $filters
	 * @param bool $isLikeOperator
	 * @return Filter[]
	 */
	public static function buildFilters(array $filters, $isLikeOperator)
	{
		$resultFilters = [];

		foreach ($filters as $keyFilter => $filterValue)
		{
			if (!$filterValue instanceof Filter)
			{
				$firstValue = $filterValue;
				$orConditionValues  = [];

				if(is_array($filterValue))
				{
					$firstValue = array_shift($filterValue);
					$orConditionValues  = $filterValue;
				}

				$operator = ($isLikeOperator) ? FilterOperator::createLike() : FilterOperator::createEqual();

				$resultFilters[] = new Filter($keyFilter, $firstValue, $operator, $orConditionValues);

				continue;
			}

			$resultFilters[] = $filterValue;
		}

		return $resultFilters;
	}
}