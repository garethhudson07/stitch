<?php

namespace Stitch\DBAL\Statements;

use Stitch\DBAL\Statements\Contracts\Assemblable;

/**
 * Class Component
 * @package Stitch\DBAL\Statements
 */
class Component implements Assemblable
{
    /**
     * @var
     */
    protected $value;

    /**
     * @var bool
     */
    protected $isolate = false;

    /**
     * @var array
     */
    protected $bindings = [];

    /**
     * Component constructor.
     * @param $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * @return $this
     */
    public function isolate()
    {
        $this->isolate = true;

        return $this;
    }

    /**
     * @param $value
     * @return $this
     */
    public function bind($value)
    {
        $this->bindings[] = $value;

        return $this;
    }

    /**
     * @param array $values
     * @return $this
     */
    public function bindMany(array $values)
    {
        $this->bindings = array_merge($this->bindings, $values);

        return $this;
    }

    /**
     * @return array
     */
    public function getBindings(): array
    {
        return $this->value instanceof Assemblable
            ? array_merge($this->bindings, $this->value->getBindings())
            : $this->bindings;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->resolve();
    }

    /**
     * @return string
     */
    public function resolve()
    {
        return $this->isolate ? "($this->value)" : $this->value;
    }
}