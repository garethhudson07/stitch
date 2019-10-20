<?php

namespace Stitch\DBAL\Statements\Select;

use Stitch\DBAL\Builders\Query as Builder;
use Stitch\DBAL\Statements\Select\Operations\Join;
use Stitch\DBAL\Statements\Select\Operations\Select;
use Stitch\DBAL\Statements\Select\Operations\Where;
use Stitch\DBAL\Statements\Statement;
use Stitch\DBAL\Syntax\Select\Select as Syntax;


/**
 * Class Unlimited
 * @package Stitch\DBAL\Statements\Select
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
        $this->push(
            new Select($this->syntax, $this->builder)
        )->push(
            $this->syntax->from()
        )->push(
            $this->syntax->tablePath($this->builder->getSchema())
        );

        foreach ($this->builder->getJoins() as $join) {
            $this->push(
                new Join($this->syntax, $join)
            );
        }

        $this->push(
            new Where($this->syntax, $this->builder->getConditions())
        );
    }
}
