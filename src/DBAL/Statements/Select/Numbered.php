<?php

namespace Stitch\DBAL\Statements\Select;

use Stitch\DBAL\Builders\Table as Builder;
use Stitch\DBAL\Builders\Column as ColumnBuilder;
use Stitch\DBAL\Builders\Sorter;
use Stitch\DBAL\Statements\Select\Operations\OrderBy;
use Stitch\DBAL\Statements\Select\Variables\Selection as VariableSelection;
use Stitch\DBAL\Statements\Statement;
use Stitch\DBAL\Syntax\Select as Syntax;

/**
 * Class Numbered
 * @package Stitch\DBAL\Statements\Select
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
    public function __construct(Syntax $syntax, Builder $builder)
    {
        parent::__construct($syntax);

        $this->builder = $builder;
        $this->sorter = new Sorter();
    }

    /**
     * @return void
     */
    public function evaluate()
    {
        $this->populateSorter($this->builder);

        $this->push($this->syntax->selectAllAnd())
            ->push(
                new VariableSelection($this->syntax, $this->builder)
            )
            ->push(
                $this->syntax->from()
            )
            ->push(
                (new Subquery(
                    $this->syntax,
                    new Unlimited($this->syntax, $this->builder)
                ))->alias('selection')
            )->push(
                new OrderBy($this->syntax, $this->sorter)
            );
    }

    /**
     * @param $builder
     */
    protected function populateSorter(Builder $builder)
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
