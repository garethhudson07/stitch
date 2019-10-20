<?php

namespace Stitch\DBAL\Statements\Select;

use Stitch\DBAL\Builders\Query as Builder;
use Stitch\DBAL\Statements\Select\Operations\OrderBy;
use Stitch\DBAL\Statements\Statement;
use Stitch\DBAL\Syntax\Select\Select as Syntax;

/**
 * Class Query
 * @package Stitch\DBAL\Statements\Select
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
            $this->builder->hasLimit() || $this->builder->hasOffset() ?
                new Limited($this->syntax, $this->builder) :
                new Unlimited($this->syntax, $this->builder)
        )->push(
            new OrderBy($this->syntax, $this->builder->getSorter())
        );
    }
}
