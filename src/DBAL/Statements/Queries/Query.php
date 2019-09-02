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
        $this->builder->limited() ? $this->limited() : $this->unlimited();

        $this->push(
            new OrderBy($this->builder->getSorter())
        );
    }

    /**
     * @return void
     */
    protected function limited()
    {
        $this->push(
            new Limited($this->builder)
        );
    }

    /**
     * @return void
     */
    protected function unlimited()
    {
        $this->push(
            new Unlimited($this->builder)
        );
    }
}
