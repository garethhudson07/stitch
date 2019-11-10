<?php

namespace Stitch\DBAL\Statements\Select;

use Stitch\DBAL\Builders\Expression as ExpressionBuilder;
use Stitch\DBAL\Builders\Query as QueryBuilder;
use Stitch\DBAL\Builders\Table as TableBuilder;
use Stitch\DBAL\Statements\Select\Operations\Limit;
use Stitch\DBAL\Statements\Select\Operations\Where;
use Stitch\DBAL\Statements\Statement;
use Stitch\DBAL\Syntax\Select as Syntax;
use Stitch\DBAL\Paths\Resolver as PathResolver;

/**
 * Class Limited
 * @package Stitch\DBAL\Statements\Select
 */
class Limited extends Statement
{
    /**
     * @var QueryBuilder
     */
    protected $builder;

    protected $paths;

    /**
     * Limited constructor.
     * @param QueryBuilder $builder
     */
    public function __construct(QueryBuilder $builder, PathResolver $paths)
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
        $this->builder->getJoins() ? $this->numbered() : $this->default();
    }

    /**
     * @return void
     */
    protected function numbered()
    {
        $this->push(Syntax::selectSubquery())
            ->push(
                (new Subquery(
                    new Numbered($this->builder, $this->paths)
                ))->alias('numbered')
            )->push(
                new Where(
                    $this->conditions(
                        $this->builder,
                        new ExpressionBuilder()
                    ),
                    $this->paths
                )
            );
    }

    /**
     * @param TableBuilder $builder
     * @param ExpressionBuilder $conditions
     * @return ExpressionBuilder
     */
    protected function conditions(TableBuilder $builder, ExpressionBuilder $conditions)
    {
        $limit = $builder->getLimit();
        $offset = $builder->getOffset();
        $column = Syntax::rowNumber($this->paths->table($builder));

        if ($offset !== null) {
            $conditions->andRaw(
                Syntax::greaterThan($column, $limit)
            );
        }

        if ($limit !== null) {
            $limit += $offset;

            $conditions->andRaw(
                Syntax::lessThanOrEqual($column, $limit)
            );
        }

        foreach ($builder->getJoins() as $join) {
            $this->conditions($join, $conditions);
        }

        return $conditions;
    }

    /**
     * @return void
     */
    protected function default()
    {
        $this->push(
            new Unlimited($this->builder, $this->paths)
        )->push(
            new Limit($this->builder)
        );
    }
}
