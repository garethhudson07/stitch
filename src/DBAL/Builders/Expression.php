<?php

namespace Stitch\DBAL\Builders;

use Closure;

class Expression
{
    protected $items = [];

    protected $aliases = [
        'where' => 'and',
        'whereRaw' => 'andRaw',
        'or' => 'orWhere',
        'orRaw' => 'orWhereRaw'
    ];

    /**
     * @param array ...$arguments
     * @return Expression
     */
    public function and(...$arguments)
    {
        return $this->add(
            'AND',
            $arguments[0] instanceof Expression ? $arguments[0] : $this->constraint(...$arguments)
        );
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
        return $this->add(
            'OR',
            $arguments[0] instanceof Expression ? $arguments[0] : $this->constraint(...$arguments)
        );
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
        if (array_key_exists($method, $this->aliases)) {
            $this->{$this->aliases[$method]}(...$arguments);
        }
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
     * @param array ...$arguments
     * @return Condition|Expression
     */
    protected function constraint(...$arguments)
    {
        if ($arguments[0] instanceof Closure) {
            $expression = $this->newInstance();
            $arguments[0]($expression);

            return $expression;
        }

        return $this->condition(...$arguments);
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

    /**
     * @param array ...$arguments
     * @return Condition
     */
    protected function condition(...$arguments)
    {
        return new Condition(...$arguments);
    }

    /**
     * @return static
     */
    protected function newInstance()
    {
        return new static();
    }
}