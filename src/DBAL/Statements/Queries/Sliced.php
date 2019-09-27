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
class Sliced extends Statement
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
    public function evaluate()
    {
        $this->builder->getJoins() ? $this->numbered() : $this->default();
    }

    /**
     * @return void
     */
    protected function numbered()
    {
        $this->push('SELECT * FROM')
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
        $offset = $builder->getOffset();
        $limit = $builder->getLimit();

        if ($offset !== null) {
            $this->conditions->andRaw("{$table}_row_num > $offset");
        }

        if ($limit !== null) {
            $limit += $offset;
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
            new Selection($this->builder)
        )->push(
            new Limit($this->builder)
        );
    }
}
