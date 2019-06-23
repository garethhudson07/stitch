<?php

namespace Stitch\DBAL\Builders;

class Record
{
    protected $table;

    protected $attributes = [];

    protected $primaryKey;

    public function __construct(string $table)
    {
        $this->table = $table;
    }

    public function attribute(string $name, $value)
    {
        $this->attributes[$name] = $value;

        return $this;
    }

    public function primaryKey(string $name)
    {
        $this->primaryKey = $name;

        return $this;
    }

    public function getTable()
    {
        return $this->table;
    }

    public function getAttributes()
    {
        return $this->attributes;
    }

    public function getPrimaryKey()
    {
        return $this->primaryKey;
    }
}