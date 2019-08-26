<?php

namespace Stitch\DBAL\Statements\Queries\Fragments;

use Stitch\DBAL\Builders\Column as Builder;
use Stitch\DBAL\Statements\Statement;

/**
 * Class Condition
 * @package Stitch\DBAL\Statements\Queries\Fragments
 */
class Column extends Statement
{
    /**
     * @var Builder
     */
    protected $builder;

    protected $path = false;

    protected $alias = false;

    /**
     * Condition constructor.
     * @param Builder $builder
     */
    public function __construct(Builder $builder)
    {
        $this->builder = $builder;
    }

    /**
     * @return $this
     */
    public function path()
    {
        $this->path = true;

        return $this;
    }

    /**
     * @return $this
     */
    public function alias()
    {
        $this->alias = true;

        return $this;
    }

    /**
     * @return void
     */
    public function evaluate()
    {
        $schema = $this->builder->getSchema();
        $name = $column = $schema->getName();
        $table = $schema->getTable()->getName();

        if ($this->path) {
            $this->push("{$table}.{$name}");
        }

        if ($this->path && $this->alias) {
            $this->push('as');
        }

        if ($this->alias) {
            $this->push("{$table}_{$name}");
        }
    }
}
