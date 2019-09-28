<?php

namespace Stitch\DBAL\Statements;

use Stitch\DBAL\Statements\Contracts\HasBindings;

class Binder implements HasBindings
{
    protected $statement;

    protected $bindings = [];

    /**
     * Binding constructor.
     * @param $statement
     */
    public function __construct($statement)
    {
        $this->statement = $statement;
    }

    /**
     * @param $binding
     * @return Binder
     */
    public function add($binding)
    {
        return is_array($binding) ? $this->many($binding) : $this->one($binding);
    }

    /**
     * @param $value
     * @return $this
     */
    public function one($binding)
    {
        $this->bindings[] = $binding;

        return $this;
    }

    /**
     * @param array $bindings
     * @return $this
     */
    public function many(array $bindings)
    {
        $this->bindings = array_merge($this->bindings, $bindings);

        return $this;
    }

    /**
     * @return mixed
     */
    public function statement()
    {
        return $this->statement;
    }

    /**
     * @return mixed
     */
    public function __toString()
    {
        return $this->statement();
    }

    /**
     * @return array
     */
    public function bindings()
    {
        return $this->bindings;
    }
}