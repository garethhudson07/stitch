<?php

namespace Stitch\DBAL\Statements\Select;

use Stitch\DBAL\Builders\Table as TableBuilder;
use Stitch\DBAL\Builders\Query as QueryBuilder;
use Stitch\DBAL\Builders\Column as ColumnBuilder;
use Stitch\DBAL\Builders\Sorter;
use Stitch\DBAL\Statements\Select\Operations\OrderBy;
use Stitch\DBAL\Statements\Select\Variables\Selection as VariableSelection;
use Stitch\DBAL\Statements\Statement;
use Stitch\DBAL\Paths\Resolver as PathResolver;
use Stitch\DBAL\Syntax\Select as Syntax;

/**
 * Class Numbered
 * @package Stitch\DBAL\Statements\Select
 */
class Numbered extends Statement
{
    /**
     * @var QueryBuilder
     */
    protected $builder;

    protected $paths;

    protected $sorter;

    /**
     * Numbered constructor.
     * @param QueryBuilder $builder
     */
    public function __construct(QueryBuilder $builder, PathResolver $paths)
    {
        parent::__construct();

        $this->builder = $builder;
        $this->paths = $paths;
        $this->sorter = new Sorter();
    }

    /**
     * @return void
     */
    public function evaluate()
    {
        $this->populateSorter($this->builder);

        $this->push(Syntax::selectAllAnd())
            ->push(
                new VariableSelection($this->builder, $this->paths)
            )
            ->push(
                Syntax::from()
            )
            ->push(
                (new Subquery(
                    new Unlimited($this->builder, $this->paths)
                ))->alias('selection')
            )->push(
                new OrderBy($this->sorter, $this->paths)
            );
    }

    /**
     * @param $builder
     */
    protected function populateSorter(TableBuilder $builder)
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
