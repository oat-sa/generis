<?php

namespace oat\generis\model\kernel\persistence;

class Filter
{
    protected string $key;

    /** @var  string|object */
    protected $value;

    protected string $operator;

    protected array $orConditionValues = [];

    /**
     * @param string $key
     * @param  string|object $value
     * @param string $operator
     * @param array $orConditionValues
     */
    public function __construct(string $key, $value, string $operator, array $orConditionValues = [])
    {
        $this->key = $key;
        $this->value = $value;
        $this->operator = $operator;
        $this->orConditionValues = $orConditionValues;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @return string|object
     */
    public function getValue()
    {
        return $this->value;
    }

    public function getOperator(): string
    {
        return $this->operator;
    }

    public function getOrConditionValues(): array
    {
        return $this->orConditionValues;
    }
}
