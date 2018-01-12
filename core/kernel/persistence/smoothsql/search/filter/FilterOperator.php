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
 * Copyright (c) 2018 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */
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
    public static function createIsNotNull()
    {
        return new self(static::IS_NOT_NULL);
    }

    /**
     * @return FilterOperator
     */
    public static function createDifferent()
    {
        return new self(static::DIFFERENT);
    }

    /**
     * @return FilterOperator
     */
    public static function createGreaterThan()
    {
        return new self(static::GREATER_THAN);
    }

    /**
     * @return FilterOperator
     */
    public static function createLessThan()
    {
        return new self(static::LESSER_THAN);
    }

    /**
     * @return FilterOperator
     */
    public static function createLessThanEqual()
    {
        return new self(static::LESSER_THAN_EQUAL);
    }

    /**
     * @return FilterOperator
     */
    public static function createBetween()
    {
        return new self(static::BETWEEN);
    }

    /**
     * @return FilterOperator
     */
    public static function createIn()
    {
        return new self(static::IN);
    }

    /**
     * @return FilterOperator
     */
    public static function createNotIn()
    {
        return new self(static::NOT_IN);
    }

    /**
     * @return FilterOperator
     */
    public static function createMatch()
    {
        return new self(static::MATCH);
    }

    /**
     * @return FilterOperator
     */
    public static function createNotMatch()
    {
        return new self(static::NOT_MATCH);
    }

    /**
     * @return FilterOperator
     */
    public static function createBeginBy()
    {
        return new self(static::BEGIN_BY);
    }

    /**
     * @return FilterOperator
     */
    public static function createEndingBy()
    {
        return new self(static::ENDING_BY);
    }

    /**
     * @return FilterOperator
     */
    public static function createIsNull()
    {
        return new self(static::IS_NULL);
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