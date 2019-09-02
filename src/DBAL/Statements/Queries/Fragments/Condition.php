<?php

namespace Stitch\DBAL\Statements\Queries\Fragments;

use Stitch\DBAL\Builders\Condition as Builder;
use Stitch\DBAL\Statements\Statement;

/**
 * Class Condition
 * @package Stitch\DBAL\Statements\Queries\Fragments
 */
class Condition extends Statement
{
    /**
     * @var Builder
     */
    protected $builder;

    protected CONST SUPPORTED_METHODS = [
        'IN',
        'NOT IN'
    ];

    /**
     * Condition constructor.
     * @param Builder $builder
     */
    public function __construct(Builder $builder)
    {
        $this->builder = $builder;
    }

    /**
     * @return void
     */
    public function evaluate()
    {
        $operator = $this->builder->getOperator();
        $value = $this->builder->getValue();

        $this->push(
            (new Column($this->builder->getColumn()))->database()->path()
        );

        switch ($operator) {
            case 'IN':
            case 'NOT IN':
            case 'BETWEEN':
                $this->compareMany($operator, $value);
                break;

            case 'IS NULL':
            case 'IS NOT NULL':
                $this->compareNull($operator);
                break;

            default:
                $this->compare($operator, $value);
        }
    }

    /**
     * @param string $operator
     * @param $value
     */
    public function compare(string $operator, $value)
    {
        $this->push(
            $this->component("$operator ?")->bind($value)
        );
    }

    /**
     * @param string $operator
     * @param array $values
     */
    public function compareMany(string $operator, array $values)
    {
        $placeholders = array_replace(
            $values,
            array_fill(0, count($values), '?')
        );

        if (in_array($operator, $this::SUPPORTED_METHODS)) {
            $this->push(
                $this->component('(' . implode(',', $placeholders) . ')')->bindMany($values)
            );

            return;
        }

        $this->push(
            $this->component($operator . implode(' AND ', $placeholders))->bindMany($values)
        );
    }

    /**
     * @param string $operator
     */
    public function compareNull(string $operator)
    {
        $this->push($operator);
    }
}
