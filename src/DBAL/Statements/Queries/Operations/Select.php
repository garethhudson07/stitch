<?php

namespace Stitch\DBAL\Statements\Queries\Operations;

use Stitch\DBAL\Builders\Query as Builder;
use Stitch\DBAL\Statements\Assembler;
use Stitch\DBAL\Statements\Queries\Fragments\Column;
use Stitch\DBAL\Statements\Statement;

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
        $this->columns = (new Assembler())->glue(', ');
    }

    /**
     * @return void
     */
    public function evaluate()
    {
        $this->push('SELECT')->push($this->columns);

        foreach ($this->builder->resolveSelection()->getColumns() as $column) {
            $this->columns->push(
                (new Column($column))->path()->alias()
            );
        }
    }
}
