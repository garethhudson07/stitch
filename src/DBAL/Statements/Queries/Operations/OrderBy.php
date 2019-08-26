<?php

namespace Stitch\DBAL\Statements\Queries\Operations;

use Stitch\DBAL\Builders\Sorter as Builder;
use Stitch\DBAL\Statements\Assembler;
use Stitch\DBAL\Statements\Queries\Fragments\Column;
use Stitch\DBAL\Statements\Statement;

/**
 * Class OrderBy
 * @package Stitch\DBAL\Statements\Queries\Operations
 */
class OrderBy extends Statement
{
    /**
     * @var Builder
     */
    protected $builder;

    protected $columns;

    /**
     * OrderBy constructor.
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
        if ($this->builder->count()) {
            $this->push('ORDER BY')->push($this->columns);

            foreach ($this->builder->getItems() as $item) {
                $column = (new Column($item['column']))->alias();

                $this->columns->push(
                    "$column {$item['direction']}"
                );
            }
        }
    }
}
