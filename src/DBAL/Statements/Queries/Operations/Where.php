<?php

namespace Stitch\DBAL\Statements\Queries\Operations;

use Stitch\DBAL\Builders\Expression as Builder;
use Stitch\DBAL\Statements\Queries\Fragments\Expression;
use Stitch\DBAL\Statements\Statement;

/**
 * Class Where
 * @package Stitch\DBAL\Statements\Queries\Operations
 */
class Where extends Statement
{
    /**
     * @var Builder
     */
    protected $builder;

    /**
     * Where constructor.
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
        if ($this->builder->count()) {
            $this->push('WHERE')->push(
                new Expression($this->builder)
            );
        }
    }
}
