<?php

namespace Stitch\DBAL\Statements\Queries\Operations;

use Stitch\DBAL\Builders\Query as Builder;
use Stitch\DBAL\Statements\Statement;

/**
 * Class From
 * @package Stitch\DBAL\Statements\Queries\Operations
 */
class From extends Statement
{
    /**
     * @var Builder
     */
    protected $builder;

    /**
     * From constructor.
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
            "FROM {$this->builder->getSchema()->getName()}"
        );
    }
}
