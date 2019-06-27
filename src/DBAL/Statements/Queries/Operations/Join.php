<?php

namespace Stitch\DBAL\Statements\Queries\Operations;

use Stitch\DBAL\Builders\Join as JoinBuilder;
use Stitch\DBAL\Statements\Component;
use Stitch\DBAL\Statements\Queries\Fragments\Expression;
use Stitch\DBAL\Statements\Statement;

/**
 * Class Join
 * @package Stitch\DBAL\Statements\Queries\Operations
 */
class Join extends Statement
{
    /**
     * @var JoinBuilder
     */
    protected $joinBuilder;

    /**
     * Join constructor.
     * @param JoinBuilder $joinBuilder
     */
    public function __construct(JoinBuilder $joinBuilder)
    {
        $this->joinBuilder = $joinBuilder;

        parent::__construct();
    }

    /**
     *
     */
    protected function evaluate()
    {
        $this->assembler->push(
            new Component("{$this->joinBuilder->getType()} JOIN {$this->joinBuilder->getTable()}")
        )->push(
            new Component('ON')
        );

        $this->assembler->push(
            new Expression($this->joinBuilder->getOnConditions())
        );

        foreach ($this->joinBuilder->getJoins() as $join) {
            $this->assembler->push(new static($join));
        }
    }
}