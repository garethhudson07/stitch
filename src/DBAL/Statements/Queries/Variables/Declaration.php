<?php

namespace Stitch\DBAL\Statements\Queries\Variables;

use Stitch\DBAL\Builders\Query as QueryBuilder;
use Stitch\DBAL\Statements\Component;
use Stitch\DBAL\Statements\Statement;

/**
 * Class Declaration
 * @package Stitch\DBAL\Statements\Queries\Variables
 */
class Declaration extends Statement
{
    /**
     * @var QueryBuilder
     */
    protected $queryBuilder;

    /**
     * Declaration constructor.
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
            new Component('SET')
        )->push(
            new Component(
                implode(', ', $this->variables($this->queryBuilder)) . ';'
            )
        );
    }

    /**
     * @param QueryBuilder $queryBuilder
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