<?php

namespace Stitch\DBAL\Statements\Select\Operations;

use Stitch\DBAL\Builders\Join as Builder;
use Stitch\DBAL\Statements\Select\Fragments\Expression;
use Stitch\DBAL\Statements\Statement;
use Stitch\DBAL\Syntax\Select as Syntax;

/**
 * Class Join
 * @package Stitch\DBAL\Statements\Select\Operations
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
            $this->syntax->join(
                $this->builder->getType(),
                $this->builder->getSchema()
            )
        );

        $this->push(
            new Expression($this->syntax, $this->builder->getConditions())
        );

        foreach ($this->builder->getJoins() as $join) {
            $this->push(
                new static($this->syntax, $join)
            );
        }
    }
}
