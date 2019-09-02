<?php

namespace Stitch\DBAL\Statements\Queries;

use Stitch\DBAL\Builders\Query as Builder;
use Stitch\DBAL\Builders\Column as ColumnBuilder;
use Stitch\DBAL\Builders\Sorter;
use Stitch\DBAL\Statements\Queries\Operations\OrderBy;
use Stitch\DBAL\Statements\Queries\Variables\Selection as VariableSelection;
use Stitch\DBAL\Statements\Statement;

/**
 * Class Numbered
 * @package Stitch\DBAL\Statements\Queries
 */
class Numbered extends Statement
{
    /**
     * @var Builder
     */
    protected $builder;

    protected $sorter;

    /**
     * Numbered constructor.
     * @param Builder $builder
     */
    public function __construct(Builder $builder)
    {
        $this->builder = $builder;
        $this->sorter = new Sorter();

        $this->populateSorter($builder);
    }

    /**
     * @return void
     */
    public function evaluate()
    {
        $this->push('SELECT *,')
            ->push(
                new VariableSelection($this->builder)
            )
            ->push('FROM')
            ->push(
                (new Subquery(new Unlimited($this->builder)))->alias('selection')
            )->push(
                new OrderBy($this->sorter)
            );
    }

    /**
     * @param $builder
     */
    protected function populateSorter($builder)
    {
        $schema = $builder->getSchema();
        $added = false;

        foreach ($this->builder->getSorter()->getItems() as $item) {
            if ($schema === $item['column']->getSchema()->getTable()) {
                $this->sorter->add($item['column'], $item['direction']);
                $added = true;
                break;
            }
        }

        if (!$added) {
            $this->sorter->add(new ColumnBuilder($schema->getPrimaryKey()));
        }

        foreach ($builder->getJoins() as $join) {
            $this->populateSorter($join);
        }
    }
}
