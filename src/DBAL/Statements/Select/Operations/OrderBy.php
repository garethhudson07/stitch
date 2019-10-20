<?php

namespace Stitch\DBAL\Statements\Select\Operations;

use Stitch\DBAL\Builders\Sorter as Builder;
use Stitch\DBAL\Statements\Assembler;
use Stitch\DBAL\Statements\Statement;
use Stitch\DBAL\Syntax\Select\Select as Syntax;

/**
 * Class OrderBy
 * @package Stitch\DBAL\Statements\Select\Operations
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
    public function __construct(Syntax $syntax, Builder $builder)
    {
        parent::__construct($syntax);

        $this->builder = $builder;
        $this->columns = (new Assembler())->glue(', ');
    }

    /**
     * @return void
     */
    public function evaluate()
    {
        if ($this->builder->count()) {
            $this->push(
                $this->syntax->orderBy()
            )->push(
                $this->columns
            );

            foreach ($this->builder->getItems() as $item) {
                $this->columns->push(
                    $this->syntax->columnAlias($item['column']->getSchema()) . ' ' . $item['direction']
                );
            }
        }
    }
}
