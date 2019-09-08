<?php

namespace Stitch\DBAL\Statements\Queries\Operations;

use Stitch\DBAL\Builders\Query as Builder;
use Stitch\DBAL\Statements\Statement;
use Stitch\Grammar\Sql;

/**
 * Class Select
 * @package Stitch\DBAL\Statements\Queries\Operations
 */
class Select extends Statement
{
    /**
     * @var Builder
     */
    protected $builder;

    protected $columns;

    /**
     * Select constructor.
     * @param Builder $builder
     */
    public function __construct(Builder $builder)
    {
        $this->builder = $builder;
    }

    /**
     * @return void
     */
    public function evaluate()
    {
        $this->push(
            Sql::selectColumns(array_map(function ($column)
            {
                return sql::columnPath($column->getSchema()) . ' ' . Sql::alias(Sql::columnAlias($column->getSchema()));
            }, $this->builder->resolveSelection()->getColumns()))
        );
    }
}
