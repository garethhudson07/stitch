<?php

namespace Stitch\DBAL\Statements\Queries\Variables;

use Stitch\DBAL\Builders\Query as Builder;
use Stitch\DBAL\Statements\Assembler;
use Stitch\DBAL\Statements\Statement;

/**
 * Class Declaration
 * @package Stitch\DBAL\Statements\Queries\Variables
 */
class Declaration extends Statement
{
    /**
     * @var Builder
     */
    protected $builder;

    /**
     * Declaration constructor.
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
        $this->push('SET')->push(
            implode(', ', $this->variables($this->builder)) . ';'
        );
    }

    /**
     * @param Builder $builder
     * @return array
     */
    protected function variables($builder)
    {
        $schema = $builder->getSchema();
        $table = $schema->getName();
        $variables = [];

        $variables[] = "@{$table}_{$schema->getPrimaryKey()->getName()} = NULL";
        $variables[] = "@{$table}_row_num = 0";

        foreach ($builder->getJoins() as $join) {
            $variables = array_merge($variables, $this->variables($join));
        }

        return $variables;
    }
}