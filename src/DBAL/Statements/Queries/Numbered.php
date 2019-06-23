<?php

namespace Stitch\DBAL\Statements\Queries;

use Stitch\DBAL\Builders\Query as QueryBuilder;
use Stitch\DBAL\Statements\Component;
use Stitch\DBAL\Statements\Statement;
use Stitch\DBAL\Statements\Queries\Variables\Selection as VariableSelection;

class Numbered extends Statement
{
    protected $queryBuilder;

    public function __construct(QueryBuilder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;

        parent::__construct();
    }

    protected function evaluate()
    {
        $this->assembler->push(
            new Component('SELECT *,')
        )->push(
            new VariableSelection($this->queryBuilder)
        )->push(
            new Component('FROM')
        )->push(
            new Subquery(new Unlimited($this->queryBuilder), 'selection')
        );
    }
}