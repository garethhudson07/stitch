<?php

namespace Stitch\DBAL\Builders;

class Raw
{
    protected $sql;

    protected $bindings;

    public function __construct(string $sql, array $bindings = [])
    {
        $this->sql = $sql;
        $this->bindings = $bindings;
    }

    public function sql($sql)
    {
        $this->sql = $sql;

        return $this;
    }

    public function getSql()
    {
        return $this->sql;
    }

    public function bind($value)
    {
        $this->bindings[] = $value;
    }

    public function bindMany(array $values)
    {
        $this->bindings = array_merge($this->bindings, $values);
    }

    public function getBindings()
    {
        return $this->bindings;
    }
}