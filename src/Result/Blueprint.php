<?php

namespace Stitch\Result;

use Stitch\Queries\Joins\Collection as Joins;
use Stitch\Queries\Query;
use Stitch\Relations\Relation;
use Stitch\DBAL\Paths\Resolver as PathResolver;
use Stitch\DBAL\Paths\Table;

class Blueprint
{
    protected $table;

    protected $factory;

    protected $columnMap;

    protected $relations = [];

    /**
     * Blueprint constructor.
     * @param Table $table
     * @param $factory
     */
    public function __construct(Table $table, $factory)
    {
        $this->table = $table;
        $this->factory = $factory;
    }

    /**
     * @param Query $query
     * @param PathResolver $paths
     * @return Blueprint
     */
    public static function make(Query $query, PathResolver $paths)
    {
        $instance = (new static(
            $paths->table($query->getBuilder()),
            $query->getModel()
        ))->resolveJoins($query->getJoins(), $paths);

        return $instance;
    }

    /**
     * @param Joins $joins
     * @param PathResolver $paths
     * @return $this
     */
    public function resolveJoins(Joins $joins, PathResolver $paths)
    {
        foreach ($joins->all() as $key => $join) {
            $this->relations[$key] = (new static(
                $paths->table($join->getBuilder()),
                $join->getRelation()
            ))->resolveJoins($join->getJoins(), $paths);
        }

        return $this;
    }

    /**
     * @return Table
     */
    public function table()
    {
        return $this->table;
    }

    /**
     * @return array
     */
    public function columns()
    {
        return $this->table->getColumns();
    }

    /**
     * @return array
     */
    public function relations()
    {
        return $this->relations;
    }

    /**
     * @return Record|Set
     */
    public function result()
    {
        if ($this->factory instanceof Relation) {
            return $this->factory->associatesOne() ? $this->resultRecord() : $this->resultSet();
        }

        return $this->resultSet();
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

    /**
     * @return mixed
     */
    public function activeRecord(array $attributes = [])
    {
        return $this->factory->record($attributes);
    }

    /**
     * @return mixed
     */
    public function activeRecordCollection()
    {
        return $this->factory->collection();
    }
}
