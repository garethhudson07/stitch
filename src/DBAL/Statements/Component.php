<?php

namespace Stitch\DBAL\Statements;

use Stitch\DBAL\Statements\Contracts\Assemblable;

class Component implements Assemblable
{
    protected $value;

    protected $isolate = false;

    protected $bindings = [];

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function resolve()
    {
        return $this->isolate ? "($this->value)" : $this->value;
    }

    public function isolate()
    {
        $this->isolate = true;

        return $this;
    }

    public function bind($value)
    {
        $this->bindings[] = $value;

        return $this;
    }

    public function bindMany(array $values)
    {
        $this->bindings = array_merge($this->bindings, $values);

        return $this;
    }

    public function getBindings(): array
    {
        return $this->value instanceof Assemblable
            ? array_merge($this->bindings, $this->value->getBindings())
            : $this->bindings;
    }

    public function __toString(): string
    {
        return $this->resolve();
    }
}