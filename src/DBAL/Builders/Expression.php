<?php

namespace Stitch\DBAL\Builders;

use Closure;

/**
 * Class Where
 * @package Stitch\DBAL\Builders
 */
class Expression
{
    /**
     * @var array
     */
    protected $items = [];

    /**
     * @var array
     */
    protected $aliases = [
        'where' => 'and',
        'whereRaw' => 'andRaw',
        'orWhere' => 'or',
        'orWhereRaw' => 'orRaw'
    ];

    /**
     * @param array ...$arguments
     * @return Expression
     */
    public function and(...$arguments)
    {
        return $this->add('AND', $this->constraint(...$arguments));
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
     * @param array ...$arguments
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
     * @param $method
     * @param $arguments
     */
    public function __call($method, $arguments)
    {
        if ($this->aliases[$method] ?? false) {
            $this->{$this->aliases[$method]}(...$arguments);
        }
    }

    /**
     * @param string $operator
     * @param $constraint
     * @return $this
     */
    public function add(string $operator, $constraint)
    {
        $this->items[] = [
            'operator' => $operator,
            'constraint' => $constraint
        ];

        return $this;
    }

    /**
     * @param array ...$arguments
     * @return Condition|Expression
     */
    protected function constraint(...$arguments)
    {
        if ($arguments[0] instanceof Expression) {
            return $arguments[0];
        }

        if ($arguments[0] instanceof Closure) {
            $expression = new static();
            $arguments[0]($expression);

            return $expression;
        }

        return new Condition(...$arguments);
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
