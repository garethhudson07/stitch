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
     */
    public function __construct(Statement $statement)
    {
        $this->statement = $statement;
    }

    /**
     * @param string $alias
     * @return $this
     */
    public function alias(string $alias)
    {
        $this->alias = $alias;

        return $this;
    }

    /**
     * @return void
     */
    public function evaluate()
    {
        $this->push(
            $this->component($this->statement)->isolate()
        );

        if ($this->alias) {
            $this->push("as {$this->alias}");
        }
    }
}
