<?php

namespace Stitch\DBAL\Builders;

/**
 * Class Condition
 * @package Stitch\DBAL\Builders
 */
class Condition
{
    /**
     * @var string
     */
    protected $column;

    /**
     * @var string
     */
    protected $operator = '=';

    /**
     * @var mixed
     */
    protected $value;

    /**
     * Condition constructor.
     * @param mixed ...$arguments
     */
    public function __construct(...$arguments)
    {
        if ($arguments) {
            $this->column(array_shift($arguments));

            if (count($arguments) == 1) {
                $this->value($arguments[0]);
            } else {
                $this->operator($arguments[0]);
                $this->value($arguments[1]);
            }
        }
    }

    /**
     * @param Column $column
     * @return $this
     */
    public function column(Column $column)
    {
        $this->column = $column;

        return $this;
    }

    /**
     * @param string $operator
     * @return $this
     */
    public function operator(string $operator)
    {
        $this->operator = $operator;

        return $this;
    }

    /**
     * @param $value
     * @return $this
     */
    public function value($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getColumn()
    {
        return $this->column;
    }

    /**
     * @return mixed|string
     */
    public function getOperator()
    {
        return $this->operator;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }
}