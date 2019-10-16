<?php

namespace Stitch\Result;

use Stitch\DBAL\Builders\Selection;
use Stitch\DBAL\Schema\Table;
use Stitch\DBAL\Syntax\Select as SelectSyntax;
use Stitch\Queries\Joins\Collection as Joins;
use Stitch\Queries\Query;

class Blueprint
{
    protected $factory;

    protected $columnMap;

    protected $joins = [];

    /**
     * Blueprint constructor.
     * @param $recordFactory
     */
    public function __construct($recordFactory)
    {
        $this->factory = new Factory($this, $recordFactory);
    }

    /**
     * @param Query $query
     * @param SelectSyntax $syntax
     * @return Blueprint
     */
    public static function make(Query $query, SelectSyntax $syntax)
    {
        $model = $query->getModel();

        return (new static($model))->map(
            $model->getTable(),
            $query->getBuilder()->resolveSelection(),
            $query->getJoins(),
            $syntax
        );
    }

    /**
     * @param Table $table
     * @param Selection $selection
     * @param Joins $joins
     * @param SelectSyntax $syntax
     * @return $this
     */
    public function map(Table $table, Selection $selection, Joins $joins, SelectSyntax $syntax)
    {
        $this->columnMap = (new ColumnMap($table))->build($selection, $syntax);

        foreach ($joins->all() as $key => $join) {
            $relation = $join->getRelation();

            $this->joins[$key] = (new static($relation))->map(
                $relation->getForeignModel()->getTable(),
                $selection,
                $join->getJoins(),
                $syntax
            );
        }

        return $this;
    }

    /**
     * @return array
     */
    public function joins()
    {
        return $this->joins;
    }

    /**
     * @return array
     */
    public function columnMap()
    {
        return $this->columnMap;
    }

    /**
     * @return Factory
     */
    public function factory()
    {
        return $this->factory;
    }
}
