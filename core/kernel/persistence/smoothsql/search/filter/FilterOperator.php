<?php

namespace oat\generis\model\kernel\persistence\smoothsql\search\filter;

use oat\search\helper\SupportedOperatorHelper;

class FilterOperator extends SupportedOperatorHelper
{
	/** @var string */
	protected $operator;

	/**
	 * @param string $operator
	 */
	protected function __construct($operator)
	{
		$this->operator = $operator;
	}

	/**
	 * @return FilterOperator
	 */
	public static function createGreaterThanEqual()
	{
		return new self(static::GREATER_THAN_EQUAL);
	}

	/**
	 * @return FilterOperator
	 */
	public static function createEqual()
	{
		return new self(static::EQUAL);
	}

	/**
	 * @return FilterOperator
	 */
	public static function createLike()
	{
		return new self(static::CONTAIN);
	}

	/**
	 * @return string
	 */
	public function getValue()
	{
		return $this->operator;
	}
}