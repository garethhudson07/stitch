<?php

namespace Stitch\DBAL\Statements\Queries;

use Stitch\DBAL\Builders\Expression as ExpressionBuilder;
use Stitch\DBAL\Builders\Query as Builder;
use Stitch\DBAL\Statements\Queries\Operations\Limit;
use Stitch\DBAL\Statements\Queries\Operations\Where;
use Stitch\DBAL\Statements\Statement;

/**
 * Class Limited
 * @package Stitch\DBAL\Statements\Queries
 */
class Limited extends Statement
{
    /**
     * @var Builder
     */
    protected $builder;

    protected $conditions;

    /**
     * Limited constructor.
     * @param Builder $builder
     */
    public function __construct(Builder $builder)
    {
        $this->builder = $builder;
        $this->conditions = new ExpressionBuilder();

        $this->populateConditions($builder);
    }

    /**
     * @return void
     */
    public function evaluate($syntax)
    {
        $this->builder->getJoins() ? $this->numbered() : $this->default();
    }

    /**
     * @return void
     */
    protected function numbered($syntax)
    {
        $this->push($syntax->selectSubquery())
            ->push(
                (new Subquery(
                    new Numbered($this->builder)
                ))->alias('numbered')
            )->push(
                new Where($this->conditions)
            );
    }

    /**
     * @param $builder
     * @return array
     */
    protected function populateConditions($builder)
    {
        $table = $builder->getSchema()->getName();
        $limit = $builder->getLimit();

        if ($limit !== null) {
            $this->conditions->andRaw("{$table}_row_num <= $limit");
        }

        foreach ($builder->getJoins() as $join) {
            $this->populateConditions($join);
        }
    }

    /**
     * @return void
     */
    protected function default()
    {
        $this->push(
            new Unlimited($this->builder)
        )->push(
            new Limit($this->builder)
        );
    }
}
