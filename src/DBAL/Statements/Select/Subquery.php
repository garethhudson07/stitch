<?php

namespace Stitch\DBAL\Statements\Select;

use Stitch\DBAL\Statements\Statement;
use Stitch\DBAL\Syntax\Select as Syntax;

/**
 * Class Subquery
 * @package Stitch\DBAL\Statements\Select
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
        parent::__construct();

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
            $this->statement->isolate()
        );

        if ($this->alias) {
            $this->push(
                Syntax::alias($this->alias)
            );
        }
    }
}
