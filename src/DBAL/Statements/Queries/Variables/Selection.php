<?php

namespace Stitch\DBAL\Statements\Queries\Variables;

use Closure;
use Stitch\DBAL\Builders\Query as QueryBuilder;
use Stitch\DBAL\Statements\Component;
use Stitch\DBAL\Statements\Statement;

/**
 * Class Selection
 * @package Stitch\DBAL\Statements\Queries\Variables
 */
class Selection extends Statement
{
    /**
     * @var QueryBuilder
     */
    protected $queryBuilder;

    /**
     * Selection constructor.
     * @param QueryBuilder $queryBuilder
     */
    public function __construct(QueryBuilder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;

        parent::__construct();
    }

    /**
     * @return void
     */
    protected function evaluate()
    {
        $this->assembler->push(
            new Component(implode(', ', $this->variables($this->queryBuilder)))
        );
    }

    /**
     * @param $builder
     * @param null $parentBuilder
     * @return array
     */
    protected function variables($builder, $parentBuilder = null)
    {
        $keys = $this->generateKeys($builder);
        $variables = [];

        foreach ($builder->getJoins() as $join) {
            $variables = array_merge($variables, $this->variables($join, $builder));
        }

        $variables[] = $this->alias(
            $this->assignment(
                $keys['variables']['row_number'],
                $this->ternary(
                    $this->equality($keys['variables']['column'], $keys['schema']['column']),
                    $keys['variables']['row_number'],
                    function() use ($keys, $parentBuilder)
                    {
                        if ($parentBuilder) {
                            $parentKeys = $this->generateKeys($parentBuilder);

                            return $this->ternary(
                                $this->equality($parentKeys['variables']['column'], $parentKeys['schema']['column']),
                                "{$keys['variables']['row_number']} + 1",
                                '1'
                            );
                        } else {
                            return " {$keys['variables']['row_number']} + 1";
                        }
                    }
                )
            ),
            $keys['schema']['row_number']
        );

        $variables[] = $this->assignment($keys['variables']['column'], $keys['schema']['column']);

        return $variables;
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @return array
     */
    protected function generateKeys($builder)
    {
        $schema = $builder->getSchema();

        $keys = [
            'schema' => [
                'table' => $schema->getName()
            ],
            'variables' => []
        ];

        $keys['schema']['column'] = "{$keys['schema']['table']}_{$schema->getPrimaryKey()->getName()}";
        $keys['schema']['row_number'] = "{$keys['schema']['table']}_row_num";
        $keys['variables']['column'] = "@{$keys['schema']['column']}";
        $keys['variables']['row_number'] = "@{$keys['schema']['row_number']}";

        return $keys;
    }

    /**
     * @param mixed ...$arguments
     * @return string
     */
    protected function ternary(...$arguments)
    {
        $expression = array_shift($arguments);
        $ternary = "if({$expression}, ";

        $ternary .= implode(', ', array_map(function ($argument)
        {
            return $argument instanceof Closure ? $argument() : $argument;
        }, $arguments));

        $ternary .= ')';

        return $ternary;
    }

    /**
     * @param $variable
     * @param $value
     * @return string
     */
    protected function assignment($variable, $value)
    {
        return "$variable := $value";
    }

    /**
     * @param $value
     * @param $name
     * @return string
     */
    protected function alias($value, $name)
    {
        return "$value as $name";
    }

    /**
     * @param $left
     * @param $right
     * @return string
     */
    protected function equality($left, $right)
    {
        return "$left = $right";
    }
}
