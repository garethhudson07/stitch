<?php

namespace Stitch\DBAL\Statements\Queries\Fragments;

use Stitch\DBAL\Builders\Condition as ConditionBuilder;
use Stitch\DBAL\Statements\Component;
use Stitch\DBAL\Statements\Statement;

/**
 * Class Condition
 * @package Stitch\DBAL\Statements\Queries\Fragments
 */
class Condition extends Statement
{
    /**
     * @var ConditionBuilder
     */
    protected $conditionBuilder;

    /**
     * Condition constructor.
     * @param ConditionBuilder $conditionBuilder
     */
    public function __construct(ConditionBuilder $conditionBuilder)
    {
        $this->conditionBuilder = $conditionBuilder;

        parent::__construct();
    }

    /**
     * @return void
     */
    protected function evaluate()
    {
        $column = $this->conditionBuilder->getColumn();
        $operator = $this->conditionBuilder->getOperator();
        $value = $this->conditionBuilder->getValue();

        if (strtolower($operator) === 'in') {
            $placeholders = implode(',', array_replace($value, array_fill(0, count($value), '?')));

            $this->assembler->push(
                (new Component("$column IN($placeholders)"))->bindMany($value)
            );
        } else {
            $this->assembler->push(
                (new Component("$column $operator ?"))->bind($value)
            );
        }
    }
}