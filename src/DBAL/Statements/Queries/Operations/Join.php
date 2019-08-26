<?php

namespace Stitch\DBAL\Statements\Queries\Operations;

use Stitch\DBAL\Builders\Join as Builder;
use Stitch\DBAL\Statements\Queries\Fragments\Expression;
use Stitch\DBAL\Statements\Statement;

/**
 * Class Join
 * @package Stitch\DBAL\Statements\Queries\Operations
 */
class Join extends Statement
{
    /**
     * @var Builder
     */
    protected $builder;

    /**
     * Join constructor.
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
            "{$this->builder->getType()} JOIN {$this->builder->getSchema()->getName()} ON"
        );

        $this->push(
            new Expression($this->builder->getConditions())
        );

        foreach ($this->builder->getJoins() as $join) {
            $this->assembler->push(
                new static($join)
            );
        }
    }
}
