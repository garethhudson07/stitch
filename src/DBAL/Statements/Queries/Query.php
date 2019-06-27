<?php

namespace Stitch\DBAL\Statements\Queries;

use Stitch\DBAL\Builders\Query as QueryBuilder;
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
     *
     */
    protected function evaluate()
    {
        $this->queryBuilder->limited() ?
            $this->assembler->push(new Limited($this->queryBuilder)) :
            $this->assembler->push(new Unlimited($this->queryBuilder));
    }
}