<?php

namespace Stitch\DBAL\Statements\Queries;

use Stitch\DBAL\Builders\Query as QueryBuilder;
use Stitch\DBAL\Statements\Component;
use Stitch\DBAL\Statements\Queries\Variables\Selection as VariableSelection;
use Stitch\DBAL\Statements\Statement;

/**
 * Class Numbered
 * @package Stitch\DBAL\Statements\Queries
 */
class Numbered extends Statement
{
    /**
     * @var QueryBuilder
     */
    protected $queryBuilder;

    /**
     * Numbered constructor.
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
            new Component('SELECT *,')
        )->push(
            new VariableSelection($this->queryBuilder)
        )->push(
            new Component('FROM')
        )->push(
            new Subquery(new Unlimited($this->queryBuilder), 'selection')
        )->push(
            new Component('ORDER BY')
        )->push(
            new Component(implode(', ', array_map(function($item)
                {
                    return "{$item['column']->getAlias()} {$item['direction']}";
                }, $this->sort($this->queryBuilder, $this->queryBuilder->getSorter())))
            )
        );
    }

    /**
     * @param $builder
     * @param $sorter
     * @return array
     */
    protected function sort($builder, $sorter)
    {
        $schema = $builder->getSchema();
        $sort = [];

        foreach ($sorter->getItems() as $item) {
            if ($schema === $item['column']->getSchema()->getTable()) {
                $sort[] = $item;
                break;
            }
        }

        if (!$sort) {
            $sort[] = [
                'column' => $schema->getPrimaryKey(),
                'direction' => 'ASC'
            ];
        }

        foreach ($builder->getJoins() as $join) {
            $sort = array_merge($sort, $this->sort($join, $sorter));
        }

        return $sort;
    }

}