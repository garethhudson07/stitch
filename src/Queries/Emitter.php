<?php

namespace Stitch\Queries;

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
        $this->model->emitEvent('fetching', [$query, $this->path]);

        foreach ($this->joins as $join) {
            $join->emitFetching($query);
        }

        return $this;
    }
}
