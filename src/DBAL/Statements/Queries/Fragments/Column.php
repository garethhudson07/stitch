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

    protected $database = false;

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
    public function database()
    {
        $this->database = true;

        return $this;
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
        $columnSchema = $this->builder->getSchema();
        $tableSchema = $columnSchema->getTable();
        $name = $column = $columnSchema->getName();
        $table = $tableSchema->getName();

        $fullPath = [];

        if ($this->database) {
            $fullPath[] = $tableSchema->getConnection()->getDatabase();
        }

        if ($this->path) {
            $fullPath[] = $table;
            $fullPath[] = $name;

            $this->push(implode('.', $fullPath));

            if ($this->alias) {
                $this->push('as');
            }
        }

        if ($this->alias) {
            $this->push("{$table}_{$name}");
        }
    }
}
