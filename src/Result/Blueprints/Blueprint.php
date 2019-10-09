<?php

namespace Stitch\Result\Blueprints;

use Stitch\DBAL\Builders\Selection;
use Stitch\DBAL\Syntax\Select as SelectSyntax;
use Stitch\Queries\Joins\Collection as Joins;
use Stitch\Queries\Query;
use Stitch\Result\Record;
use Stitch\Result\Set;

abstract class Blueprint
{
    protected $recordFactory;

    protected $columnMap;

    protected $joins = [];

    /**
     * Blueprint constructor.
     * @param $recordFactory
     */
    public function __construct($recordFactory)
    {
        $this->recordFactory = $recordFactory;
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
            $this->joins[$key] = (new static(
                $join->getBlueprint()
            ))->map($table, $selection, $joins, $syntax);
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
     * @return Set
     */
    public function resultSet()
    {
        return new Set($this);
    }

    /**
     * @return Record
     */
    public function resultRecord()
    {
        return new Record($this);
    }

    public function activeRecord()
    {

    }

    abstract public function getSchema();

    abstract public function getJoins();

    abstract public function result();
    
    abstract public function activeRecordCollection();
}
