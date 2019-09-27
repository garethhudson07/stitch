<?php

namespace Stitch\DBAL\Statements\Queries;

use Stitch\DBAL\Builders\Query as Builder;
use Stitch\DBAL\Statements\Queries\Operations\OrderBy;
use Stitch\DBAL\Statements\Statement;

/**
 * Class Query
 * @package Stitch\DBAL\Statements\Queries
 */
class Query extends Statement
{
    /**
     * @var Builder
     */
    protected $builder;

    /**
     * Query constructor.
     * @param Builder $builder
     */
    public function __construct(Builder $builder)
    {
        $this->builder = $builder;
    }

    /**
     * @return void
     */
    public function evaluate()
    {
        $this->push(
            $this->builder->hasLimit() || $this->builder->hasOffset() ? new Sliced($this->builder) : new Selection($this->builder)
        )->push(
            new OrderBy($this->builder->getSorter())
        );
    }
}
