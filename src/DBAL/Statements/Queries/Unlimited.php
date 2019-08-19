<?php

namespace Stitch\DBAL\Statements\Queries;

use Stitch\DBAL\Builders\Query as QueryBuilder;
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
     * @var QueryBuilder
     */
    protected $queryBuilder;

    /**
     * Unlimited constructor.
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
        $this->assembler->push(
            new Select($this->queryBuilder)
        )->push(
            new From($this->queryBuilder)
        );

        foreach ($this->queryBuilder->getJoins() as $join) {
            $this->assembler->push(
                new Join($join)
            );
        }

        $this->assembler->push(
            new Where($this->queryBuilder)
        );
    }
}