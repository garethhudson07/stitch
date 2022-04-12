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
    protected $target;

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
            $this->target(array_shift($arguments));

            if (count($arguments) == 1) {
                $this->value($arguments[0]);
            } else {
                $this->operator($arguments[0]);
                $this->value($arguments[1]);
            }
        }
    }

    /**
     * @param $target
     * @return $this
     */
    public function target($target)
    {
        $this->target = $target;

        return $this;
    }

    /**
     * @param string $operator
     * @return $this
     */
    public function operator(string $operator)
    {
        $this->operator = strtoupper($operator);

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
    public function getTarget()
    {
        return $this->target;
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
