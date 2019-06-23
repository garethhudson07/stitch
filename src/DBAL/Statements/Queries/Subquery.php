<?php

namespace Stitch\DBAL\Statements\Queries;

use Stitch\DBAL\Statements\Component;
use Stitch\DBAL\Statements\Statement;

class Subquery extends Statement
{
    protected $statement;

    protected $alias;

    public function __construct(Statement $statement, string $alias)
    {
        $this->statement = $statement;
        $this->alias = $alias;

        parent::__construct();
    }

    protected function evaluate()
    {
        $this->assembler->push(
            (new Component($this->statement))->isolate()
        )->push(
            new Component('AS ' . $this->alias)
        );
    }
}