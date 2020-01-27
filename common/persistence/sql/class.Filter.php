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
 * Copyright (c) 2019 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 */

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use oat\generis\model\kernel\persistence\smoothsql\search\filter\Filter;

/**
 * Handles the application of filters.
 *
 * @author Martijn Swinkels <martijn@taotesting.com>
 */
class common_persistence_sql_Filter
{
    const OP_EQ  = '=';
    const OP_NEQ = '!=';
    const OP_LT  = '<';
    const OP_LTE = '<=';
    const OP_GT  = '>';
    const OP_GTE = '>=';
    const OP_LIKE = 'LIKE';
    const OP_NOT_LIKE = 'NOT LIKE';
    const OP_IN = 'IN';
    const OP_NOT_IN = 'NOT IN';

    /**
     * @var array
     */
    private $filters = [];

    /**
     * @var int
     */
    private $limit;

    /**
     * @var int
     */
    private $offset;

    /**
     * @var string
     */
    private $sortBy;

    /**
     * @var string
     */
    private $sortOrder;

    /**
     * @return mixed
     */
    public function getSortBy()
    {
        return $this->sortBy;
    }

    /**
     * @param mixed $sortBy
     * @return common_persistence_sql_Filter
     */
    public function setSortBy($sortBy)
    {
        $this->sortBy = $sortBy;
        return $this;
    }

    /**
     * @return string
     */
    public function getSortOrder()
    {
        return $this->sortOrder;
    }

    /**
     * @param string $sortOrder
     * @return common_persistence_sql_Filter
     */
    public function setSortOrder($sortOrder)
    {
        $this->sortOrder = $sortOrder;
        return $this;
    }

    /**
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @param int $limit
     * @return common_persistence_sql_Filter
     */
    public function setLimit($limit)
    {
        $this->limit = max(0, $limit);
        return $this;
    }

    /**
     * @return int
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * @param int $offset
     * @return common_persistence_sql_Filter
     */
    public function setOffset($offset)
    {
        $this->offset = max(0, $offset);
        return $this;
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * Add a filter.
     *
     * @param string $field
     * @param string $operator
     * @param mixed $value
     * @return common_persistence_sql_Filter
     */
    public function addFilter($field, $operator, $value)
    {
        $this->assertValidOperator($operator);

        $this->filters[] =  [
            'column' => (string) $field,
            'valuePlaceholder' => uniqid(':' . $field),
            'operator' => $operator,
            'value' => $value
        ];

        return $this;
    }

    /**
     * Apply the filters
     *
     * @param QueryBuilder $qb
     * @return QueryBuilder
     */
    public function applyFilters(QueryBuilder $qb)
    {
        foreach ($this->getFilters() as $filter) {
            $type = null;
            $placeholder = $filter['valuePlaceholder'];
            if (is_array($filter['value'])) {
                $type = Connection::PARAM_STR_ARRAY;
                $filter['valuePlaceholder'] = '(' . $placeholder . ')';
            }

            $qb->andWhere($filter['column'] . ' ' . $filter['operator'] . ' ' . $filter['valuePlaceholder'])
                ->setParameter($placeholder, $filter['value'], $type);
        }

        return $qb;
    }

    /**
     * Add an "equals" filter
     *
     * @param string $field
     * @param mixed $value
     * @return common_persistence_sql_Filter
     */
    public function eq($field, $value)
    {
        return $this->addFilter($field, self::OP_EQ, $value);
    }

    /**
     * Add a "not equals" filter
     *
     * @param string $field
     * @param mixed $value
     * @return common_persistence_sql_Filter
     */
    public function neq($field, $value)
    {
        return $this->addFilter($field, self::OP_NEQ, $value);
    }

    /**
     * Add a "lower than" filter
     *
     * @param string $field
     * @param mixed $value
     * @return common_persistence_sql_Filter
     */
    public function lt($field, $value)
    {
        return $this->addFilter($field, self::OP_LT, $value);
    }

    /**
     * Add a "lower than or equals" filter
     *
     * @param string $field
     * @param mixed $value
     * @return common_persistence_sql_Filter
     */
    public function lte($field, $value)
    {
        return $this->addFilter($field, self::OP_LTE, $value);
    }

    /**
     * Add a "greater than" filter
     *
     * @param string $field
     * @param mixed $value
     * @return common_persistence_sql_Filter
     */
    public function gt($field, $value)
    {
        return $this->addFilter($field, self::OP_GT, $value);
    }

    /**
     * Add a "greater than or equals" filter
     *
     * @param string $field
     * @param mixed $value
     * @return common_persistence_sql_Filter
     */
    public function gte($field, $value)
    {
        return $this->addFilter($field, self::OP_GTE, $value);
    }

    /**
     * Add a "like" filter
     *
     * @param string $field
     * @param mixed $value
     * @return common_persistence_sql_Filter
     */
    public function like($field, $value)
    {
        return $this->addFilter($field, self::OP_LIKE, $value);
    }

    /**
     * Add a "not like" filter
     *
     * @param string $field
     * @param mixed $value
     * @return common_persistence_sql_Filter
     */
    public function notLike($field, $value)
    {
        return $this->addFilter($field, self::OP_NOT_LIKE, $value);
    }

    /**
     * Add an "in" filter
     *
     * @param string $field
     * @param array $value
     * @return common_persistence_sql_Filter
     */
    public function in($field, array $value)
    {
        return $this->addFilter($field, self::OP_IN, $value);
    }

    /**
     * Add a "not in" filter
     *
     * @param string $field
     * @param array $value
     * @return common_persistence_sql_Filter
     */
    public function notIn($field, array $value)
    {
        return $this->addFilter($field, self::OP_NOT_IN, $value);
    }

    /**
     * @param string $op
     * @throws InvalidArgumentException
     */
    private function assertValidOperator($op)
    {
        $operators = [
            self::OP_EQ,
            self::OP_NEQ,
            self::OP_LT,
            self::OP_LTE,
            self::OP_GT,
            self::OP_GTE,
            self::OP_LIKE,
            self::OP_NOT_LIKE,
            self::OP_IN,
            self::OP_NOT_IN,
        ];

        if (!in_array(strtoupper($op), $operators, true)) {
            throw new InvalidArgumentException('Operator "' . $op . '" is not a valid operator.');
        }
    }
}
