<?php

namespace Stitch\DBAL\Builders;

use Closure;

class Expression
{
    protected $items = [];

    public function and(...$arguments)
    {
        return $this->add('AND', $this->constraint(...$arguments));
    }

    public function andRaw(string $sql, array $bindings = [])
    {
        return $this->add('AND', new Raw($sql, $bindings));
    }

    public function or(...$arguments)
    {
        return $this->add('OR', $this->constraint(...$arguments));
    }

    public function orRaw(string $sql, array $bindings = [])
    {
        return $this->add('OR', new Raw($sql, $bindings));
    }

    protected function add(string $operator, $constraint)
    {
        $this->items[] = [
            'operator' => $operator,
            'constraint' => $constraint
        ];

        return $this;
    }

    protected function constraint(...$arguments)
    {
        if ($arguments[0] instanceof Closure) {
            $expression = new static();
            $arguments[0]($expression);

            return $expression;
        }

        return new Condition(...$arguments);
    }

    public function count()
    {
        return count($this->items);
    }

    public function getItems()
    {
        return $this->items;
    }
}