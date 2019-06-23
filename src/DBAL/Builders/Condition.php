<?php

namespace Stitch\DBAL\Builders;

class Condition
{
    protected $column;

    protected $operator = '=';

    protected $value;

    public function __construct(...$arguments)
    {
        if ($arguments) {
            $this->column = array_shift($arguments);

            if (count($arguments) == 1) {
                $this->value = $arguments[0];
            } else {
                $this->operator = $arguments[0];
                $this->value = $arguments[1];
            }
        }
    }

    public function column(string $column)
    {
        $this->column = $column;

        return $this;
    }

    public function operator(string $operator)
    {
        $this->operator = $operator;

        return $this;
    }

    public function value($value)
    {
        $this->value = $value;

        return $this;
    }

    public function getColumn()
    {
        return $this->column;
    }

    public function getOperator()
    {
        return $this->operator;
    }

    public function getValue()
    {
        return $this->value;
    }
}