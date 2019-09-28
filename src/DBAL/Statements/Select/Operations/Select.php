<?php

namespace Stitch\DBAL\Statements\Select\Operations;

use Stitch\DBAL\Builders\Query as Builder;
use Stitch\DBAL\Builders\Column as ColumnBuilder;
use Stitch\DBAL\Statements\Statement;
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

    /**
     * Where constructor.
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
        if ($this->builder->crossTable()) {
            $this->push(
                $this->syntax->selectColumns(
                    array_map(function (ColumnBuilder $column)
                    {
                        $schema = $column->getSchema();

                        return implode(' ', [
                            $this->syntax->columnPath($schema),
                            $this->syntax->alias(
                                $this->syntax->columnAlias($schema)
                            )
                        ]);
                    }, $this->builder->resolveSelection()->getColumns())
                )
            );

            return;
        }

        $this->push(
            $this->syntax->selectAll()
        );
    }
}
