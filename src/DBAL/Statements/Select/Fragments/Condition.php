<?php

namespace Stitch\DBAL\Statements\Select\Fragments;

use Stitch\DBAL\Builders\Condition as Builder;
use Stitch\DBAL\Builders\Column as ColumnBuilder;
use Stitch\DBAL\Statements\Binder;
use Stitch\DBAL\Statements\Statement;
use Stitch\DBAL\Paths\Resolver as PathResolver;
use Stitch\DBAL\Syntax\Select as Syntax;
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

    protected $paths;

    /**
     * Condition constructor.
     * @param Builder $builder
     * @param PathResolver $paths
     * @internal param Syntax $syntax
     */
    public function __construct(Builder $builder, PathResolver $paths)
    {
        parent::__construct();

        $this->builder = $builder;
        $this->paths = $paths;
    }

    /**
     * @return void
     */
    public function evaluate()
    {
        $column = $this->paths->column($this->builder->getColumn());
        $operator = $this->builder->getOperator();
        $value = $this->builder->getValue();

        if ($value instanceof ColumnBuilder) {
            $this->push(Syntax::condition($column, $operator, $this->paths->column($value)));

            return;
        }

        $syntax = Syntax::condition($column, $operator, $value);

        if (!is_null($value)) {
            $syntax = (new Binder($syntax))->add($value);
        }

        $this->push($syntax);
    }
}
