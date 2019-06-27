<?php

namespace Stitch\DBAL\Builders;

/**
 * Class Raw
 * @package Stitch\DBAL\Builders
 */
class Raw
{
    /**
     * @var string
     */
    protected $sql;

    /**
     * @var array
     */
    protected $bindings;

    /**
     * Raw constructor.
     * @param string $sql
     * @param array $bindings
     */
    public function __construct(string $sql, array $bindings = [])
    {
        $this->sql = $sql;
        $this->bindings = $bindings;
    }

    /**
     * @param $sql
     * @return $this
     */
    public function sql($sql)
    {
        $this->sql = $sql;

        return $this;
    }

    /**
     * @return string
     */
    public function getSql()
    {
        return $this->sql;
    }

    /**
     * @param $value
     */
    public function bind($value)
    {
        $this->bindings[] = $value;
    }

    /**
     * @param array $values
     */
    public function bindMany(array $values)
    {
        $this->bindings = array_merge($this->bindings, $values);
    }

    /**
     * @return array
     */
    public function getBindings()
    {
        return $this->bindings;
    }
}