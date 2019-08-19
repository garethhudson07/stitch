<?php

namespace Stitch\DBAL\Statements\Queries;

use Stitch\DBAL\Builders\Query as QueryBuilder;
use Stitch\DBAL\Statements\Queries\Operations\OrderBy;
use Stitch\DBAL\Statements\Statement;

/**
 * Class Query
 * @package Stitch\DBAL\Statements\Queries
 */
class Query extends Statement
{
    /**
     * @var QueryBuilder
     */
    protected $queryBuilder;

    /**
     * Query constructor.
     * @param QueryBuilder $queryBuilder
     */
    public function __construct(QueryBuilder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;

        parent::__construct();
    }

    /**
     * @return void
     */
    protected function evaluate()
    {
        $this->queryBuilder->limited() ? $this->limited() : $this->unlimited();

        $this->assembler->push(
            new OrderBy($this->queryBuilder)
        );
    }

    /**
     * @return void
     */
    protected function limited()
    {
        $this->assembler->push(new Limited($this->queryBuilder));
    }

    /**
     * @return void
     */
    protected function unlimited()
    {
        $this->assembler->push(new Unlimited($this->queryBuilder));
    }
}
