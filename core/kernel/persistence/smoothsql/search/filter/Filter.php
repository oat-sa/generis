<?php

namespace oat\generis\model\kernel\persistence\smoothsql\search\filter;

class Filter
{
	/** @var  string */
	protected $key;

	/** @var  string */
	protected $value;

	/** @var  FilterOperator */
	protected $operator;

	/** @var  array */
	protected $inValues;

	/** @var  array */
	protected $orConditionValues;

	/**
	 * @param string $key
	 * @param string $value
	 * @param FilterOperator $operator
	 * @param array $orConditionValues
	 * @internal param array $inOrConditionValues
	 */
	public function __construct($key, $value, FilterOperator $operator, array $orConditionValues = [])
	{
		$this->key = $key;
		$this->value = $value;
		$this->operator = $operator;
		$this->orConditionValues = $orConditionValues;
	}

	/**
	 * @return string
	 */
	public function getKey()
	{
		return $this->key;
	}

	/**
	 * @return string
	 */
	public function getValue()
	{
		return $this->value;
	}

	/**
	 * @return string
	 */
	public function getOperator()
	{
		return $this->operator->getValue();
	}

	/**
	 * @return array
	 */
	public function getOrConditionValues()
	{
		return $this->orConditionValues;
	}
}