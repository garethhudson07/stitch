<?php

namespace Stitch\DBAL\Statements\Select;

use Stitch\DBAL\Builders\Query as Builder;
use Stitch\DBAL\Statements\Select\Operations\OrderBy;
use Stitch\DBAL\Statements\Statement;
use Stitch\DBAL\Syntax\Select as Syntax;

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
        $this->builder->limited() ? $this->limited() : $this->unlimited();

        $this->push(
            new OrderBy($this->syntax, $this->builder->getSorter())
        );
    }

    /**
     * @return void
     */
    protected function limited()
    {
        $this->push(
            new Limited($this->syntax, $this->builder)
        );
    }

    /**
     * @return void
     */
    protected function unlimited()
    {
        $this->push(
            new Unlimited($this->syntax, $this->builder)
        );
    }
}
