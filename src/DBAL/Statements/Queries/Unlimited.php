<?php

namespace Stitch\DBAL\Statements\Queries;

use Stitch\DBAL\Builders\Query as Builder;
use Stitch\DBAL\Statements\Queries\Operations\From;
use Stitch\DBAL\Statements\Queries\Operations\Join;
use Stitch\DBAL\Statements\Queries\Operations\Select;
use Stitch\DBAL\Statements\Queries\Operations\Where;
use Stitch\DBAL\Statements\Statement;


/**
 * Class Unlimited
 * @package Stitch\DBAL\Statements\Queries
 */
class Unlimited extends Statement
{
    /**
     * @var Builder
     */
    protected $builder;

    /**
     * Unlimited constructor.
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
            new Select($this->builder)
        )->push(
            new From($this->builder)
        );

        foreach ($this->builder->getJoins() as $join) {
            $this->push(
                new Join($join)
            );
        }

        $this->push(
            new Where($this->builder->getConditions())
        );
    }
}
