<?php

namespace Stitch\DBAL\Statements\Select;

use Stitch\DBAL\Builders\Expression as ExpressionBuilder;
use Stitch\DBAL\Builders\Table as Builder;
use Stitch\DBAL\Statements\Select\Operations\Limit;
use Stitch\DBAL\Statements\Select\Operations\Where;
use Stitch\DBAL\Statements\Statement;
use Stitch\DBAL\Syntax\Select as Syntax;

/**
 * Class Limited
 * @package Stitch\DBAL\Statements\Select
 */
class Limited extends Statement
{
    /**
     * @var Builder
     */
    protected $builder;

    /**
     * Limited constructor.
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
        $this->builder->getJoins() ? $this->numbered() : $this->default();
    }

    /**
     * @return void
     */
    protected function numbered()
    {
        $this->push($this->syntax->selectSubquery())
            ->push(
                (new Subquery(
                    $this->syntax,
                    new Numbered($this->syntax, $this->builder)
                ))->alias('numbered')
            )->push(
                new Where(
                    $this->syntax,
                    $this->conditions(
                        $this->builder,
                        new ExpressionBuilder()
                    )
                )
            );
    }

    /**
     * @param Builder $builder
     * @param ExpressionBuilder $conditions
     * @return ExpressionBuilder
     */
    protected function conditions(Builder $builder, ExpressionBuilder $conditions)
    {
        $limit = $builder->getLimit();

        if ($limit !== null) {
            $conditions->andRaw(
                $this->syntax->lessThanOrEqual(
                    $this->syntax->rowNumberColumn($builder->getSchema()),
                    $limit
                )
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
            new Unlimited($this->syntax, $this->builder)
        )->push(
            new Limit($this->syntax, $this->builder)
        );
    }
}
