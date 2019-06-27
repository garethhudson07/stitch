<?php

namespace Stitch\DBAL\Builders;

use Closure;

/**
 * Class Expression
 * @package Stitch\DBAL\Builders
 */
class Expression
{
    /**
     * @var array
     */
    protected $items = [];

    /**
     * @param mixed ...$arguments
     * @return Expression
     */
    public function and(...$arguments)
    {
        return $this->add('AND', $this->constraint(...$arguments));
    }

    /**
     * @param string $operator
     * @param $constraint
     * @return $this
     */
    protected function add(string $operator, $constraint)
    {
        $this->items[] = [
            'operator' => $operator,
            'constraint' => $constraint
        ];

        return $this;
    }

    /**
     * @param mixed ...$arguments
     * @return Condition|Expression
     */
    protected function constraint(...$arguments)
    {
        if ($arguments[0] instanceof Closure) {
            $expression = new static();
            $arguments[0]($expression);

            return $expression;
        }

        return new Condition(...$arguments);
    }

    /**
     * @param string $sql
     * @param array $bindings
     * @return Expression
     */
    public function andRaw(string $sql, array $bindings = [])
    {
        return $this->add('AND', new Raw($sql, $bindings));
    }

    /**
     * @param mixed ...$arguments
     * @return Expression
     */
    public function or(...$arguments)
    {
        return $this->add('OR', $this->constraint(...$arguments));
    }

    /**
     * @param string $sql
     * @param array $bindings
     * @return Expression
     */
    public function orRaw(string $sql, array $bindings = [])
    {
        return $this->add('OR', new Raw($sql, $bindings));
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->items);
    }

    /**
     * @return array
     */
    public function getItems()
    {
        return $this->items;
    }
}