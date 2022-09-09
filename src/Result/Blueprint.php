<?php

namespace Stitch\Result;

use Stitch\Queries\Query;
use Stitch\Relations\Relation;
use Stitch\DBAL\Paths\Resolver as PathResolver;
use Stitch\DBAL\Paths\Table;
use Stitch\Events\Event;

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
        ))->resolveJoins($query, $paths);

        return $instance;
    }

    /**
     * @param $joinable
     * @param PathResolver $paths
     * @return $this
     */
    public function resolveJoins($joinable, PathResolver $paths)
    {
        $joins = $joinable->getJoins();

        foreach ($joinable->getRelations()->all() as $name) {
            $join = $joins->get($name);

            $this->relations[$name] = (new static(
                $paths->table($join->getBuilder()),
                $join->getRelation()
            ))->resolveJoins($join, $paths);
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
     * @param array $raw
     * @return null|Record|Set
     */
    public function extract(array $raw, ?Record $parent = null)
    {
        if ($this->factory instanceof Relation && $this->factory->associatesOne()) {
            $extracted = $this->resultRecord()->parent($parent)->extract($raw);

            if ($extracted->isNull()) {
                $extracted = null;
            }

            return $extracted;
        }

        return $this->resultSet()->extract($raw);
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

    /**
     * @return Event
     */
    public function event(string $event): Event
    {
        return $this->factory->makeEvent($event);
    }
}
