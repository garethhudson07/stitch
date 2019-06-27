<?php

namespace Stitch\DBAL\Statements\Queries\Operations;

use Stitch\DBAL\Builders\Query as QueryBuilder;
use Stitch\DBAL\Statements\Assembler;
use Stitch\DBAL\Statements\Component;
use Stitch\DBAL\Statements\Queries\Fragments\Expression;
use Stitch\DBAL\Statements\Statement;

/**
 * Class Where
 * @package Stitch\DBAL\Statements\Queries\Operations
 */
class Where extends Statement
{
    /**
     * @var QueryBuilder
     */
    protected $queryBuilder;

    /**
     * @var Assembler
     */
    protected $expressionAssembler;

    /**
     * Where constructor.
     * @param QueryBuilder $queryBuilder
     */
    public function __construct(QueryBuilder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
        $this->expressionAssembler = (new Assembler())->glue(' AND ');

        parent::__construct();
    }

    /**
     *
     */
    protected function evaluate()
    {
        $this->conditions($this->queryBuilder);

        if ($this->expressionAssembler->count()) {
            $this->assembler->push(
                new Component('WHERE')
            )->push(
                $this->expressionAssembler
            );
        }
    }

    /**
     * @param QueryBuilder $queryBuilder
     */
    protected function conditions(QueryBuilder $queryBuilder)
    {
        $conditions = $queryBuilder->getWhereConditions();

        if ($conditions->count()) {
            $this->expressionAssembler->push(
                new Expression($conditions)
            );
        }

        foreach ($queryBuilder->getJoins() as $join) {
            $this->conditions($join);
        }
    }
}