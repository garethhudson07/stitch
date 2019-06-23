<?php

namespace Stitch\DBAL\Statements\Queries\Fragments;

use Stitch\DBAL\Builders\Condition as ConditionBuilder;
use Stitch\DBAL\Statements\Component;
use Stitch\DBAL\Statements\Statement;

class Condition extends Statement
{
    protected $conditionBuilder;

    public function __construct(ConditionBuilder $conditionBuilder)
    {
        $this->conditionBuilder = $conditionBuilder;

        parent::__construct();
    }

    protected function evaluate()
    {
        $column = $this->conditionBuilder->getColumn();
        $operator = $this->conditionBuilder->getOperator();
        $value = $this->conditionBuilder->getValue();

        if (strtolower($operator) === 'in') {
            $placeholders = implode(',', array_map(function ($item)
            {
                return '?';
            }, $value));

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