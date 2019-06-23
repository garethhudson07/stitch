<?php

namespace Stitch\DBAL\Builders;

class Column
{
    protected $name;

    protected $alias;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function alias(string $alias)
    {
        $this->alias = $alias;

        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getAlias()
    {
        return $this->alias;
    }
}