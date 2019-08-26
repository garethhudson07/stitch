<?php

namespace Stitch\DBAL\Statements\Queries\Operations;

use Stitch\DBAL\Builders\Query as Builder;
use Stitch\DBAL\Statements\Statement;

/**
 * Class Limit
 * @package Stitch\DBAL\Statements\Queries\Operations
 */
class Limit extends Statement
{
    /**
     * @var Builder
     */
    protected $builder;

    /**
     * Limit constructor.
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
        $limit = $this->builder->getLimit();

        if ($limit) {
            $this->push("LIMIT $limit");
        }
    }
}