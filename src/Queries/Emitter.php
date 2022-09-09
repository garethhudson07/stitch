<?php

namespace Stitch\Queries;

use Stitch\Result\Set as ResultSet;
use Stitch\Result\Record;

class Emitter
{
    protected $model;

    protected $joins;

    protected $path;

    public function __construct($model, $joins, Path $path)
    {
        $this->model = $model;
        $this->joins = $joins;
        $this->path = $path;
    }

    /**
     * @param $model
     * @param $joins
     * @param Path $path
     * @return Emitter
     */
    public static function make($model, $joins, Path $path): Emitter
    {
        return new static($model, $joins, $path);
    }

    /**
     * @param Query $query
     * @return $this
     */
    public function fetching(Query $query): Emitter
    {
        $event = $this->model->makeEvent('fetching')->fillPayload([
            'query' => $query,
            'path' => $this->path
        ])->fire();

        if ($event->propagating()) {
            foreach ($this->joins as $join) {
                $join->emitFetching($query);
            }
        }

        return $this;
    }

    /**
     * @param Query $query
     * @param ResultSet|Record|null $result
     * @return $this
     */
    public function fetched(Query $query, $result): Emitter
    {
        $iterator = [];

        if ($result instanceof Record) {
            $iterator[] = $result;
        }

        if ($result instanceof ResultSet) {
            $iterator = $result->all();
        }

        foreach ($iterator as $record) {
            $event = $this->model->makeEvent('fetched')->fillPayload([
                'query' => $query,
                'path' => $this->path,
                'record' => $record,
            ])->fire();

            if ($event->propagating()) {
                foreach ($this->joins as $join) {
                    $join->emitFetched($query, $record->getRelations()[$join->getRelation()->getName()] ?? null);
                }
            }
        }

        return $this;
    }
}
