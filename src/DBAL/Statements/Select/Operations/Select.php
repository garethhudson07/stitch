<?php

namespace Stitch\DBAL\Statements\Select\Operations;

use Stitch\DBAL\Builders\Query as Builder;
use Stitch\DBAL\Builders\Column as ColumnBuilder;
use Stitch\DBAL\Statements\Statement;
use Stitch\DBAL\Paths\Resolver as PathResolver;
use Stitch\DBAL\Syntax\Select as Syntax;

/**
 * Class Where
 * @package Stitch\DBAL\Statements\Select\Operations
 */
class Select extends Statement
{
    /**
     * @var Builder
     */
    protected $builder;

    protected $paths;

    /**
     * Where constructor.
     * @param Builder $builder
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
        $selection = $this->builder->getSelection();

        if (!$selection->count() && !count($this->builder->getJoins())) {
            $this->push(
                Syntax::selectAll()
            );

            return;
        }

        $this->push(
            Syntax::selectColumns(
                array_map(function (ColumnBuilder $column)
                {
                    $path = $this->paths->column($column);

                    return $path->qualifiedName() . ' ' . Syntax::alias($path->alias());
                }, $this->builder->resolveSelection()->getColumns())
            )
        );
    }
}
