<?php

namespace Stitch\DBAL\Builders;

/**
 * Class Record
 * @package Stitch\DBAL\Builders
 */
class Record
{
    /**
     * @var string
     */
    protected $table;

    /**
     * @var array
     */
    protected $attributes = [];

    /**
     * @var string
     */
    protected $primaryKey;

    /**
     * Record constructor.
     * @param string $table
     */
    public function __construct(string $table)
    {
        $this->table = $table;
    }

    /**
     * @param string $name
     * @param $value
     * @return $this
     */
    public function attribute(string $name, $value)
    {
        $this->attributes[$name] = $value;

        return $this;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function primaryKey(string $name)
    {
        $this->primaryKey = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @return mixed
     */
    public function getPrimaryKey()
    {
        return $this->primaryKey;
    }
}