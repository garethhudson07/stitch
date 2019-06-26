<?php

namespace Stitch\Queries;

class Callback
{
    protected $query;

    public function __construct(Query $query)
    {
        $this->query = $query;
    }

    protected function rewritePath(string $path)
    {
        $path = PathFactory::divide($this->query->getModel(), $path);
    }

    public function __call($method, $arguments)
    {
        $path = array_shift($arguments);

        if (strstr($path, '.')) {
            $path = PathFactory::divide($this->model, $path);
            $relation = $this->getRelation($path['relation']);
            $table = $relation->getModel()->getTable()->getName();
            $column = $path['column']->implode();
        } else {
            $table = $this->model->getTable()->getName();
            $column = $path;
        }

        $this->query{$method}($arguments);
    }
}