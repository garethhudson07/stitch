<?php

namespace Stitch\DBAL\Statements\Queries;

use Stitch\DBAL\Statements\Component;
use Stitch\DBAL\Statements\Statement;

/**
 * Class Subquery
 * @package Stitch\DBAL\Statements\Queries
 */
class Subquery extends Statement
{
    /**
     * @var Statement
     */
    protected $statement;

    /**
     * @var string
     */
    protected $alias;

    /**
     * Subquery constructor.
     * @param Statement $statement
     * @param string $alias
     */
    public function __construct(Statement $statement, string $alias)
    {
        $this->statement = $statement;
        $this->alias = $alias;

        parent::__construct();
    }

    /**
     * @return void
     */
    protected function evaluate()
    {
        $this->assembler->push(
            (new Component($this->statement))->isolate()
        )->push(
            new Component('AS ' . $this->alias)
        );
    }
}