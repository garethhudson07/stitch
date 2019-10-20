<?php

namespace Stitch\DBAL\Statements\Select\Fragments;

use Stitch\DBAL\Builders\Condition as Builder;
use Stitch\DBAL\Builders\Column as ColumnBuilder;
use Stitch\DBAL\Statements\Binder;
use Stitch\DBAL\Statements\Statement;
use Stitch\DBAL\Syntax\Select\Select as Syntax;
/**
 * Class Condition
 * @package Stitch\DBAL\Statements\Select\Fragments
 */
class Condition extends Statement
{
    /**
     * @var Builder
     */
    protected $builder;

    /**
     * Expression constructor.
     * @param Builder $builder
     */
    public function __construct(Syntax $syntax, Builder $builder)
    {
        parent::__construct($syntax);

        $this->builder = $builder;
    }

    /**
     * @return void
     */
    public function evaluate()
    {
        $column = $this->builder->getColumn()->getSchema();
        $operator = $this->builder->getOperator();
        $value = $this->builder->getValue();

        if ($value instanceof ColumnBuilder) {
            $this->push($this->syntax->condition($column, $operator, $value->getSchema()));

            return;
        }

        $syntax = $this->syntax->condition($column, $operator, $value);

        if (!is_null($value)) {
            $syntax = (new Binder($syntax))->add($value);
        }

        $this->push($syntax);
    }
}
